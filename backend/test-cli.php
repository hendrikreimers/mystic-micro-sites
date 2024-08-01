<?php
declare(strict_types=1);

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bootstrap
const CLI_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Only on CLI mode
if ( php_sapi_name() !== 'cli' ) { die("ONLY RUNS IN CLI MODE !!!\n\n\n"); };

use Factory\EnvConstantsHelper;
use Services\FileService;

// Global Enviroment constants definition
EnvConstantsHelper::defineEnvConstants();

// Ask for a master password
echo "Enter your master password: ";
$MASTER_PASSWORD = trim(fgets(STDIN));

// Initialize encryption service
$encryptionService = new Services\EncryptionService();

// Verify master password
$verifiedMasterPass = $encryptionService->verifyPassword($MASTER_PASSWORD, MASTER_PASSWORD_HASH, SECRET_KEY);
echo ($verifiedMasterPass ? 'YES' : 'NO') . "\n\n";

// Prepare test data
$data = 'lorem ipsum dolor signum naret et';

// Encrypt and save data (attention don't do this in reality, it's just a test, usually you shouldn't overwrite master pass anytime with the initial input from stdin)
$passwords = ( !$verifiedMasterPass ) ? ['blaaa'] : ['blaaa', $MASTER_PASSWORD];
$encryptedData = $encryptionService->encryptData($data, $passwords, SECRET_KEY);
FileService::saveFileContent('test.enc', $encryptedData, overwrite: true);

// Decrypt data
$encryptedData = FileService::getFileContent('test.enc');
$decryptedDataWithPass = $encryptionService->decryptData($encryptedData, 'blaaa', SECRET_KEY);
$decryptedDataWithMasterPass = $encryptionService->decryptData($encryptedData, $MASTER_PASSWORD, SECRET_KEY);

// Output
echo "Decrypted with PASSWORD: ";
echo ($decryptedDataWithPass ?: 'Decryption failed...') . "\n\n";
echo "Decrypted with MASTER_PASSWORD: ";
echo ($decryptedDataWithMasterPass ?: 'Decryption failed...') . "\n\n";

// Done
echo "Test done...\n\n";


