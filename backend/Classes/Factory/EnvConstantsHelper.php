<?php
declare(strict_types=1);

namespace Factory;

use Services\EncryptionService;

class EnvConstantsHelper {

  /**
   * Defines global constants. Similar to the .env file
   * No foreach, because PHP will recognize them as undefined and will throw an error.
   *
   * @throws \Exception
   */
  public static function defineEnvConstants() {
    define("PRIVATE_KEY_FILE", $_ENV['PRIVATE_KEY_FILE']);
    define("PUBLIC_KEY_FILE", $_ENV['PUBLIC_KEY_FILE']);
    define("SECRET_KEY", $_ENV['SECRET_KEY']);
    define("MASTER_PASSWORD", $_ENV['MASTER_PASSWORD']);
    define("IV_LENGTH", $_ENV['IV_LENGTH']);
    define("ENIGMA_SHIFT", $_ENV['ENIGMA_SHIFT']);

    define("JWT_PRIVATE_KEY_PATH", $_ENV['JWT_PRIVATE_KEY_PATH']);
    define("JWT_PUBLIC_KEY_PATH", $_ENV['JWT_PUBLIC_KEY_PATH']);
    define("JWT_EXPIRATION_TIME", $_ENV['JWT_EXPIRATION_TIME']);
    define("JWT_REFRESH_EXPIRATION_TIME", $_ENV['JWT_REFRESH_EXPIRATION_TIME']);
    define("CORS_ALLOWED_ORIGINS", $_ENV['CORS_ALLOWED_ORIGINS']);
    define("RATE_LIMIT", $_ENV['RATE_LIMIT']);
    define("RATE_LIMIT_WINDOW", $_ENV['RATE_LIMIT_WINDOW']);
    define("USERNAME", $_ENV['USERNAME']);

    define("BASE_URL", $_ENV['BASE_URL']);
    define("BASE_PATH", $_ENV['BASE_PATH']);

    $encryptionService = new EncryptionService();
    define("MASTER_PASSWORD_HASH", $encryptionService->decryptDataWithKeyPair(MASTER_PASSWORD));

    define("VIEW_TIMELIMIT", $_ENV['VIEW_TIMELIMIT'] ?? 10);
    define("RELOAD_AFTER_MINUTES", $_ENV['RELOAD_AFTER_MINUTES'] ?? 3);
  }

}
