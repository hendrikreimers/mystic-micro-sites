<?php
declare(strict_types=1);

namespace Services;

use Exception;
use phpseclib3\Crypt\AES;
use Random\RandomException;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Hash;
use phpseclib3\Exception\NoKeyLoadedException;

/**
 * Encryption Service Class
 *
 * Service class for encryption using different mechanisms like
 * OpenSSL (Private/Public Keys), Password Hashing (Argon2id) and more.
 *
 * Usage:
 *
 * ```
 * // Generating a general secret key (globally used)
 * $secretKey = $encryptionService->generateSecretKey();
 *
 * // Generating a password hash (eq. for login password verfication)
 * $masterPasswordHash = $encryptionService->hashPassword($MASTER_PASSWORD, $secretKey);
 * $encryptedMasterPasswordHash = $encryptionService->encryptDataWithKeyPair($masterPasswordHash);
 *
 * // Generating Public/Private key pair
 * $keyPair = $encryptionService->generateKeyPair();
 * file_put_contents($encryptionService->config->privateKeyFile, $keyPair->privateKey);
 * file_put_contents($encryptionService->config->publicKeyFile, $keyPair->publicKey);
 *
 * // Verify password by hash
 * $encryptionService = new Services\EncryptionService();
 * define("SECRET_KEY", SECRET_KEY);
 * define("MASTER_PASSWORD_HASH", $encryptionService->decryptDataWithKeyPair(MASTER_PASSWORD));
 * $verifiedMasterPass = $encryptionService->verifyPassword($masterPass, MASTER_PASSWORD_HASH, SECRET_KEY);
 * echo ($verifiedMasterPass ? 'YES' : 'NO') . "\n\n";
 *
 * // Encrypt and decrypt data
 * define("SECRET_KEY", SECRET_KEY);
 * define("MASTER_PASSWORD_HASH", $encryptionService->decryptDataWithKeyPair(MASTER_PASSWORD));
 * $verifiedMasterPass = $encryptionService->verifyPassword($masterPass, MASTER_PASSWORD_HASH, SECRET_KEY);
 * echo ($verifiedMasterPass ? 'YES' : 'NO') . "\n\n";
 * $data = 'lorem ipsum dolor signum naret et';
 * $encryptedData = $encryptionService->encryptData($data, ['blaaa', $masterPass], SECRET_KEY);
 * $decryptedData = $encryptionService->decryptData($encryptedData, 'blaaa', SECRET_KEY);
 * echo $decryptedData . "\n\n";
 * ```
 */
class EncryptionService {

  /**
   * Configuration object
   * @var object
   */
  public object $config;

  /**
   * Initialize class and especially basic configuration
   */
  public function __construct(array $configOverrides = []) {
    $defaultConfig = array(
      'cipher_algo' => 'aes-256-cbc',

      'private_key_bits' => 4096,
      'private_key_type' => 0,

      'argonOptions' => array(
        'memory_cost' => defined('PASSWORD_ARGON2_DEFAULT_MEMORY_COST') ? PASSWORD_ARGON2_DEFAULT_MEMORY_COST : 0,
        'time_cost' => defined('PASSWORD_ARGON2_DEFAULT_TIME_COST') ? PASSWORD_ARGON2_DEFAULT_TIME_COST : 0,
        'threads' => defined('PASSWORD_ARGON2_DEFAULT_THREADS') ? PASSWORD_ARGON2_DEFAULT_THREADS : 0
      ),

      'privateKeyFile' => defined('PRIVATE_KEY_FILE') ? PRIVATE_KEY_FILE : 'data/keys/private.key',
      'publicKeyFile' => defined('PUBLIC_KEY_FILE') ? PUBLIC_KEY_FILE : 'data/keys/public.key',

      'jwtPrivateKeyFile' => defined('JWT_PRIVATE_KEY_PATH') ? JWT_PRIVATE_KEY_PATH : 'data/keys/jwt_private.key',
      'jwtPublicKeyFile' => defined('JWT_PUBLIC_KEY_PATH') ? JWT_PUBLIC_KEY_PATH : 'data/keys/jwt_public.key'
    );

    $mergedConfig = array_merge($defaultConfig, $configOverrides);

    // Convert array to object
    $this->config = (object) $mergedConfig;
  }

  /**
   * Returns the private key
   *
   * @return string
   * @throws Exception
   */
  public function getPrivateKey(): string {
    if ( !file_exists($this->config->privateKeyFile) ) {
      throw new Exception('Private key file does not exist');
    }

    return file_get_contents($this->config->privateKeyFile);
  }

  /**
   * Returns the public key
   *
   * @return string
   * @throws Exception
   */
  public function getPublicKey(): string {
    if ( !file_exists($this->config->publicKeyFile) ) {
      throw new \Exception('Public key file does not exist');
    }

    return file_get_contents($this->config->publicKeyFile);
  }

  /**
   * Returns the JWT Private Key
   *
   * @return string
   * @throws Exception
   */
  public function getJwtPrivateKey(): string {
    if ( !file_exists($this->config->jwtPrivateKeyFile) ) {
      throw new \Exception('JWT private key file does not exist');
    }

    return file_get_contents($this->config->jwtPrivateKeyFile);
  }

  /**
   * Returns the JWT Public Key
   *
   * @return string
   * @throws Exception
   */
  public function getJwtPublicKey(): string {
    if ( !file_exists($this->config->jwtPublicKeyFile) ) {
      throw new \Exception('JWT public key file does not exist');
    }

    return file_get_contents($this->config->jwtPublicKeyFile);
  }

  /**
   * Generates a private and public key pair
   *
   * @return object
   */
  public function generateKeyPair(): object {
    $res = openssl_pkey_new([
      'private_key_bits' => $this->config->private_key_bits,
      'private_key_type' => $this->config->private_key_type
    ]);

    openssl_pkey_export($res, $privateKey);
    $publicKey = openssl_pkey_get_details($res)['key'];

    return (object) [
      'privateKey' => $privateKey,
      'publicKey' => $publicKey
    ];
  }

  /**
   * Generate some random bytes
   *
   * @param int $length
   * @return string
   */
  public function getRandomBytes(int $length = 0): string {
    if ( $length === 0 ) {
      $length = $this->getIvLength();
    }

    return openssl_random_pseudo_bytes($length);
  }

  /**
   * Returns the length of the initialization vector
   *
   * @return int
   */
  public function getIvLength(): int {
    if ( defined("IV_LENGTH") ) {
      return intval(IV_LENGTH);
    } else return openssl_cipher_iv_length($this->config->cipher_algo) ?: 16;
  }

  /**
   * Generates a secret key
   *
   * @param int $length
   * @return string
   * @throws \Random\RandomException
   */
  public function generateSecretKey(int $length = 32): string {
    $entropy = $this->getRandomBytes($length); // additional entropy
    return bin2hex($this->getRandomBytes($length) . $entropy);
  }

  /**
   * Hashes a password using Argon2ID
   *
   * @param string $password
   * @return string
   */
  public function hashPassword(string $password, string $secretKey): string {
    return password_hash($password . $secretKey, PASSWORD_ARGON2ID, $this->config->argonOptions);
  }

  /**
   * Verifies a password against a hash
   *
   * @param string $password
   * @param string $hash
   * @param string $secretKey
   * @return bool
   */
  public function verifyPassword(string $password, string $hash, string $secretKey): bool {
    return password_verify($password . $secretKey, $hash);
  }

  /**
   * Secures the password hash by encrypting it using the public key
   *
   * @param string $passwordHash
   * @return string
   * @throws Exception
   */
  public function encryptDataWithKeyPair(string $data): string {
    $publicKey = $this->getPublicKey();

    if (openssl_public_encrypt($data, $encryptedData, $publicKey) === false) {
      throw new Exception('Unable to encrypt data with public key');
    }

    return EnigmaBase64Service::enigmaBase64Encode(bin2hex($encryptedData));
  }

  /**
   * Decrypts a password hash which is encrypted using the private key and undo enigma und base64 decode it first
   *
   * @param string $enigmaEncodedAndEncryptedData
   * @return string
   * @throws Exception
   */
  public function decryptDataWithKeyPair(string $enigmaEncodedAndEncryptedData): string {
    $encryptedData = hex2bin(EnigmaBase64Service::enigmaBase64Decode($enigmaEncodedAndEncryptedData));
    $privateKey = $this->getPrivateKey();

    if (openssl_private_decrypt($encryptedData, $decryptedData, $privateKey) === false) {
      throw new Exception('Unable to decrypt password hash');
    }

    return $decryptedData;
  }

  /**
   * Decrypts the given "Web Crypto API" (JWT Public Key) encrypted data using the provided private key
   *
   * Hint:
   * It uses phpseclib, because in Combination with the Web Crypto API it's not possible to decrypt with SHA-512
   * using native PHP functions.
   *
   * @param string $encryptedBody The base64 encoded encrypted body
   * @return string The decrypted data
   * @throws Exception If the encrypted body format is invalid or decryption fails
   */
  public function decryptEncryptedBody(string $encryptedBody): string {
    // Decode the base64 encoded encryptedBody
    $encryptedDataJson = base64_decode($encryptedBody);

    // Decode the JSON string to get the encrypted key, data, iv, and tag
    $encryptedDataArray = json_decode($encryptedDataJson, true);

    if (
      (!array_key_exists('encryptedKey', $encryptedDataArray) || !isset($encryptedDataArray['encryptedKey'])) ||
      (!array_key_exists('encryptedData', $encryptedDataArray) || !isset($encryptedDataArray['encryptedData'])) ||
      (!array_key_exists('iv', $encryptedDataArray) || !isset($encryptedDataArray['iv'])) ||
      (!array_key_exists('tag', $encryptedDataArray) || !isset($encryptedDataArray['tag']))
    ) {
      throw new Exception('Invalid encrypted body format');
    }

    // Load the private key
    $privateKey = PublicKeyLoader::load($this->getJwtPrivateKey())->withPadding(RSA::ENCRYPTION_OAEP);

    // Decode the base64 encoded encrypted key, iv, and tag
    $encryptedKeyBinary = base64_decode($encryptedDataArray['encryptedKey']);
    $ivBinary = base64_decode($encryptedDataArray['iv']);
    $tagBinary = base64_decode($encryptedDataArray['tag']);

    // Decrypt the AES key
    $aesKey = $privateKey->decrypt($encryptedKeyBinary);

    // Decode the base64 encoded encrypted data
    $encryptedDataBinary = base64_decode($encryptedDataArray['encryptedData']);

    // Initialize AES decryption
    $aes = new AES('gcm');
    $aes->setKey($aesKey);
    $aes->setNonce($ivBinary);
    $aes->setTag($tagBinary);

    // Decrypt the data
    return $aes->decrypt($encryptedDataBinary);
  }

  /**
   * Genereates a derived key from password
   * Use this to get a unique key derivation based on your password
   * It's important to use this for encrypt symetric data (encrypt and decrypt with password functions)
   *
   * @param string $passwordHash
   * @param string $secretKey
   * @return string
   */
  public function getDerivedKeyFromPassword(string $password, string $secretKey): string {
    return hash_pbkdf2('sha256', $password, $secretKey, 10000, 32, true);
  }

  /**
   * Symetric encryption of given data using a password and global secret
   *
   * @param string $data data to encrypt
   * @param string $password a password
   * @param string $secret global secret
   * @return string
   */
  public function encryptWithPassword(string $data, string $password, string $secret): string {
    $key = $this->getDerivedKeyFromPassword($password, $secret);

    // Initialization Vector
    $ivLength = $this->getIvLength();
    $iv = openssl_random_pseudo_bytes($ivLength);

    $ciphertext = openssl_encrypt($data, $this->config->cipher_algo, $key, OPENSSL_RAW_DATA, $iv);

    return EnigmaBase64Service::enigmaBase64Encode($iv . $ciphertext);
  }

  /**
   * Symetric data decryption using a password
   *
   * @param string $encryptedData encrypted data
   * @param string $password a password
   * @param string $secret global secret
   * @return false|string
   */
  public function decryptWithPassword(string $encryptedData, string $password, string $secret): false|string {
    $key = $this->getDerivedKeyFromPassword($password, $secret);
    $data = EnigmaBase64Service::enigmaBase64Decode($encryptedData);
    $ivLength = $this->getIvLength();
    $iv = substr($data, 0, $ivLength);
    $ciphertext = substr($data, $ivLength);

    return openssl_decrypt($ciphertext, $this->config->cipher_algo, $key, OPENSSL_RAW_DATA, $iv);
  }

  /**
   * Encrypts data using a unique key which is protected by multiple passwords (usually a unique one and a general master password)
   *
   * @param string $data the data to encrypt
   * @param array $passwords array of passwords (usually a unique password and the master password)
   * @param string $secretKey the global secret key
   * @return string
   * @throws RandomException
   * @throws Exception
   */
  public function encryptData(string $data, array $passwords, string $secretKey): string {
    // Random password for the data encryption
    $dataPassword = $this->generateSecretKey();

    // Encrypt Data
    $encryptedData = $this->encryptWithPassword($data, $dataPassword, $secretKey);

    // Encrypt data password with passwords
    $header = [];
    foreach ( $passwords as $password ) {
      $encryptedDataPasswordWithPassword = $this->encryptWithPassword($dataPassword, $password, $secretKey);
      $header[] = $this->encryptDataWithKeyPair($encryptedDataPasswordWithPassword);
    }

    // put all together
    $encryptedDataPackage = (object) array(
      'header' => $header,
      'data' => $encryptedData
    );

    return EnigmaBase64Service::enigmaBase64Encode(json_encode($encryptedDataPackage));
  }

  /**
   * Decrypts an encrypted data package
   *
   * @param string $encryptedDataPackage encrypted data package build by encryptData
   * @param string $password a password or the master password
   * @param string $secretKey the global secret key
   * @return false|string
   * @throws Exception
   */
  public function decryptData(string $encryptedDataPackage, string $password, string $secretKey): false|string {
    // Decrypt with private key
    $dataPackage = json_decode(EnigmaBase64Service::enigmaBase64Decode($encryptedDataPackage));

    // Try to decrypt the data key
    foreach ( $dataPackage->header as $encryptedKey ) {
      $key = $this->decryptDataWithKeyPair($encryptedKey);
      $dataKey = $this->decryptWithPassword($key, $password, $secretKey);

      if ( $dataKey !== false ) break;
    }
    if ( $dataKey === false || !isset($dataKey) ) return false;

    // Decrypt data
    return $this->decryptWithPassword($dataPackage->data, $dataKey, $secretKey);
  }

}
