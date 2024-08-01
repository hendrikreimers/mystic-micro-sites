<?php
declare(strict_types=1);

// Bootstrap
const BACKEND_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Factory\EnvConstantsHelper;
use Helpers\RateLimitHelper;
use Helpers\ResponseHelper;
use Models\SiteLayoutModel;
use Services\EnigmaBase64Service;
use Services\FileService;
use Symfony\Component\HttpFoundation\Request;
use Template\TemplateEngine;
use Utility\ObjectUtility;

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper
$encryptionService = new Services\EncryptionService(); // Initialize encryption service

// Initialize rate limiter
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

// Get URL query parameters
$fileId = $request->get('id');
$keyParts = $request->get('key');

// Simple Check
if ( (!$fileId || !$keyParts) ) {
  die("Missing arguments");
}

// Build file path and name and check if they exists
$encFile = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $fileId . '.enc';
$headerFile = $encFile = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $fileId . '.enc.h';
if ( !file_exists($encFile) && !file_exists($headerFile) ) {
  die("Not found");
}

// Build the whole password with the given params and the part of the header file
// ATTENTION: urlencode() is needed if you rewrite via htaccess
$keyParts = EnigmaBase64Service::enigmaBase64Decode(urlencode($keyParts)) ?: EnigmaBase64Service::enigmaBase64Decode($keyParts);
$keyA = substr($keyParts, 0, 10);
// $keyB, will be set after decryption with keyA and keyB
$keyC = substr($keyParts, -10);

// Load the file contents of the encrypted file and the header file
// Decrypt the rest of the password from the header file
$headerContent = (array)json_decode(EnigmaBase64Service::enigmaBase64Decode($encryptionService->decryptData(
  FileService::getFileContent($fileId . '.enc.h'),
  $keyA . DIRECTORY_SEPARATOR . $keyC, // see ApiService SAVE Endpoint for the opposite handling
  SECRET_KEY
)));
$keyB = $headerContent['keyB'];

// Define decryption password
$password = $keyA . $keyB . $keyC;

// Get the file content of the main data file
$fileContent = FileService::getFileContent($fileId . '.enc');

// Decrypt
try {
  $decryptedData = json_decode($encryptionService->decryptData($fileContent, $password, SECRET_KEY));
} catch (\Exception $e) {
  die("Decryption failed");
}

// Load template
$templatePath = join(DIRECTORY_SEPARATOR, [BASE_PATH, 'Resources', 'Private', 'Templates']);
$template = new TemplateEngine($templatePath . DIRECTORY_SEPARATOR. 'MicroSite.html');

// Force type
$siteLayout = SiteLayoutModel::fromArray(ObjectUtility::objectToArray($decryptedData));

// Assign variables
$template->view->assignMultiple([
  'bgColor' => $siteLayout->bgColor,
  'textColor' => $siteLayout->textColor,
  'fontFamily' => $siteLayout->fontFamily->value,
  'elements' => ObjectUtility::objectToArray($siteLayout->elements),
  'baseUrl' => BASE_URL
]);

// Render output
echo $template->render();
