<?php
declare(strict_types=1);

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bootstrap
const CLI_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Initialize encryption service
$encryptionService = new Services\EncryptionService();

// simple check if already installed
if (
  file_exists('.env') ||
  file_exists($encryptionService->config->privateKeyFile) ||
  file_exists($encryptionService->config->publicKeyFile)
) {
  die("ALREADY CONFIGURED !?\n\n");
}

// Ask for a master password and username
echo "Enter your master password: ";
$MASTER_PASSWORD = trim(fgets(STDIN));
echo "Enter your username: ";
$USERNAME = trim(fgets(STDIN));
echo "Enter base URL (like: https://your-site.com/path): ";
$BASE_URL = trim(fgets(STDIN));
$BASE_URL = ( $BASE_URL[strlen($BASE_URL) - 1] === '/' ) ? substr($BASE_URL, 0, -1) : $BASE_URL; // Remove ending Slash

// Create public/private key pairs
$keyPair = $encryptionService->generateKeyPair();
file_put_contents($encryptionService->config->privateKeyFile, $keyPair->privateKey);
file_put_contents($encryptionService->config->publicKeyFile, $keyPair->publicKey);

// JWT: Create public/private key pairs
$jwtBaseDir = dirname(getcwd() . DIRECTORY_SEPARATOR . $encryptionService->config->privateKeyFile);
$jwtPrivateKey  = $jwtBaseDir . '/jwt_private.key';
$jwtPublicKey  = $jwtBaseDir . '/jwt_public.key';
$keyPair = $encryptionService->generateKeyPair();
file_put_contents($jwtBaseDir . '/jwt_private.key', $keyPair->privateKey);
file_put_contents($jwtBaseDir. '/jwt_public.key', $keyPair->publicKey);

// Generate unique secret key
$secretKey = $encryptionService->generateSecretKey();

// Hash master password and encrypt it
$masterPasswordHash = $encryptionService->hashPassword($MASTER_PASSWORD, $secretKey);
$encryptedMasterPasswordHash = $encryptionService->encryptDataWithKeyPair($masterPasswordHash);

// Get absolute paths to key pairs
$pathPrivateKeyFile = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $encryptionService->config->privateKeyFile;
$pathPublicKeyFile = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $encryptionService->config->publicKeyFile;

// Get initialization vector size
$ivLength = $encryptionService->getIvLength();

// Not really secure, but makes it boring to base64_decode
$enigmaShift = 5; //rand(1,9);

// Script path
$BASE_DIR = realpath(dirname(__FILE__));

// Prepare .env file
$envContent = "
PRIVATE_KEY_FILE=$pathPrivateKeyFile
PUBLIC_KEY_FILE=$pathPublicKeyFile
SECRET_KEY=$secretKey
MASTER_PASSWORD=$encryptedMasterPasswordHash
IV_LENGTH=$ivLength
ENIGMA_SHIFT=$enigmaShift

JWT_PRIVATE_KEY_PATH=$jwtPrivateKey
JWT_PUBLIC_KEY_PATH=$jwtPublicKey
JWT_EXPIRATION_TIME=3600
JWT_REFRESH_EXPIRATION_TIME=604800
CORS_ALLOWED_ORIGINS=*
RATE_LIMIT=100
RATE_LIMIT_WINDOW=60
USERNAME=$USERNAME

BASE_URL=$BASE_URL
BASE_PATH=$BASE_DIR

VIEW_TIMELIMIT=10
";

// Write .env file
file_put_contents('.env', $envContent);

// Done
echo "Installation completed... \n\n\n";
