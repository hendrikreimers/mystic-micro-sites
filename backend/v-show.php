<?php
declare(strict_types=1);

/**
 * Obfuscating part 3 of 3
 * Third and final part of the mistic site view.
 *
 * After obfuscating and not hitting the browser history, we finally show the requested mystic site.
 *
 */

// Bootstrap
const BACKEND_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Factory\EnvConstantsHelper;
use Helpers\RateLimitHelper;
use Helpers\ResponseHelper;
use Models\SiteLayoutModel;
use Services\EncryptionService;
use Services\EnigmaBase64Service;
use Services\FileService;
use Symfony\Component\HttpFoundation\Request;
use Template\TemplateEngine;
use Utility\ObjectUtility;
use Utility\StringUtility;

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper
$encryptionService = new EncryptionService(); // Initialize encryption service

// Initialize rate limiter
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

// Get URL query parameters
$params = $request->get('p');
if ( !$params ) {
  die("Missing arguments (#1)");
}

// Decrypt and push results to variables
[$fileId, $keyParts, $noVcard, $timestamp, $hashSend] = json_decode(EnigmaBase64Service::enigmaBase64Decode($params));

// Create a comparison hash
$hashLocal = StringUtility::hashString(implode('', [
  $fileId,
  $keyParts,
  $noVcard,
  $timestamp,
  $request->getClientIp(),
  $request->headers->get('User-Agent')
]), SECRET_KEY);

// Check hash and a bit more
if ( !$fileId || !$keyParts || $hashSend !== $hashLocal ) {
  die("Missing arguments (#2)");
}

// Time limit has ended redirect to dummy 404
if ( time() > $timestamp ) {
  http_response_code(404);
  header("Location: /404");
  exit();
}

// Build file path and name and check if they exists
$encFile = join(DIRECTORY_SEPARATOR, [BASE_PATH ,'data', $fileId . '.enc']);
$headerFile = join(DIRECTORY_SEPARATOR, [BASE_PATH, 'data', $fileId . '.enc.h']);
if ( !file_exists($encFile) && !file_exists($headerFile) ) {
  http_response_code(404);
  header("Location: /404");
  exit();
}

// Build the whole password with the given params and the part of the header file
// ATTENTION: urlencode() is needed if you rewrite via htaccess
$keyParts = EnigmaBase64Service::enigmaBase64Decode(urlencode($keyParts)) ?: EnigmaBase64Service::enigmaBase64Decode($keyParts);
$keyA = substr($keyParts, 0, 10);
// $keyB, will be set after decryption with keyA and keyB
$keyC = substr($keyParts, -10);

// Load the file contents of the encrypted header file
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

// Decrypt the content
try {
  $decryptedData = json_decode($encryptionService->decryptData($fileContent, $password, SECRET_KEY));
} catch (\Exception $e) {
  die("Decryption failed");
}

// Load template
$templatePath = join(DIRECTORY_SEPARATOR, [BASE_PATH, 'Resources', 'Private', 'Templates']);
$template = new TemplateEngine($templatePath . DIRECTORY_SEPARATOR. 'MicroSite.html');

// Force type (for template engine as array)
$siteLayout = SiteLayoutModel::fromArray(ObjectUtility::objectToArray($decryptedData));

// Assign variables
$template->view->assignMultiple([
  'bgColor' => $siteLayout->bgColor,
  'textColor' => $siteLayout->textColor,
  'fontFamily' => $siteLayout->fontFamily->value,
  'elements' => ObjectUtility::objectToArray($siteLayout->elements),
  'baseUrl' => BASE_URL,
  'noVcard' => ( $noVcard ) ? '1' : '0',
  'reload_after_minutes' => ( defined('RELOAD_AFTER_MINUTES') ) ? RELOAD_AFTER_MINUTES : 3
]);

// Render output
echo $template->render();
