<?php
declare(strict_types=1);

// CONTEXT CHECKS
if (
  (!defined("BACKEND_CONTEXT") && !defined("CLI_CONTEXT")) || // Not context defined
  (defined("BACKEND_CONTEXT") && php_sapi_name() === 'cli') || // Backend Context but in CLI mode
  (defined("CLI_CONTEXT") && php_sapi_name() !== 'cli') // CLI Context but not on command line
) {
  die("WRONG_CONTEXT\n");
}

// Include general Autoloader
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

// Custom Classes Autoloader registration
spl_autoload_register(function ($class) {
  $file = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';

  if (file_exists($file)) {
    require_once $file;
    return true;
  }
  return false;
});

// .env Helper
use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(realpath(dirname(__FILE__)));
$dotenv->safeLoad();

// Set noch cache header
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
