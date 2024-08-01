<?php
declare(strict_types=1);

// Bootstrap
const CLI_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CliFunctions' . DIRECTORY_SEPARATOR . 'DecryptAllFunctions.php';

// Only on CLI mode
if ( php_sapi_name() !== 'cli' ) { die("ONLY RUNS IN CLI MODE !!!\n\n\n"); };

use Factory\EnvConstantsHelper;
use Services\FileService;

// Global Enviroment constants definition
EnvConstantsHelper::defineEnvConstants();

// Initialize encryption service
$encryptionService = new Services\EncryptionService();

// Request password
echo "Enter your master password: ";
$MASTER_PASSWORD = trim(fgets(STDIN));
$verifiedMasterPass = $encryptionService->verifyPassword($MASTER_PASSWORD, MASTER_PASSWORD_HASH, SECRET_KEY);

// Verify
if ( !$verifiedMasterPass ) {
  echo "Wrong password!\n";
  exit();
}

// Get file list
$fileList = FileService::getEncryptionFileList();

// Decrypt all
if ( sizeof( $fileList ) > 0 ) {
  $targetFolder = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . '_decoded';

  // Create dir if not exists
  if ( !is_dir($targetFolder) ) {
    mkdir($targetFolder);
  }

  // Decrypt all files
  foreach ( $fileList as $file ) {
    // Decrypt
    $decryptedData = decryptFile($file, $MASTER_PASSWORD, $encryptionService);

    if ( $decryptedData ) {
      // Render as HTML
      $htmlContent = renderTemplate($decryptedData);

      // Write
      FileService::saveFileContent('_decoded' . DIRECTORY_SEPARATOR . $file . '.json', $decryptedData);
      FileService::saveFileContent('_decoded' . DIRECTORY_SEPARATOR . $file . '.html', $htmlContent);
    } else echo "Failed to load file: $file\n";
  }

  echo "All done... see: data/_decoded/\n\n";
} else echo "Nothing found...\n";
