<?php
declare(strict_types=1);

/**
 * Obfuscating part 1 of 3
 * This is a part of viewing a mystic site by rewrite urls and tricking out the browser history.
 *
 * see v-middle.php and v-show.php for the next steps.
 */

// Bootstrap
const BACKEND_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Factory\EnvConstantsHelper;
use Helpers\RateLimitHelper;
use Helpers\ResponseHelper;
use Services\EnigmaBase64Service;
use Symfony\Component\HttpFoundation\Request;
use Template\TemplateEngine;
use Utility\StringUtility;

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper

// Initialize rate limiter
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

if ($request->getMethod() === 'POST') {
  // Get URL query parameters
  $fileId = $request->get('id');
  $keyParts = $request->get('key');
  $noVcard = $request->get('noVcard') ?: false;

  // Simple Check
  if ((!$fileId || !$keyParts)) {
    die("Missing arguments");
  }

  // Create a timestamp for access limitation
  $timestamp = time() + VIEW_TIMELIMIT; // Current timestamp plus 10 seconds

  // Hash it, so we will recognize any modification
  $hash = StringUtility::hashString(implode('', [
    $fileId,
    $keyParts,
    $noVcard,
    $timestamp,
    $request->getClientIp(),
    $request->headers->get('User-Agent')
  ]), SECRET_KEY);

  // Build the data for the URL query param
  $params = json_encode([$fileId, $keyParts, $noVcard, $timestamp, $hash]);

  // Transform data appending hash
  $params = urlencode(rawurlencode(EnigmaBase64Service::enigmaBase64Encode($params)));

  // Build URL and start redirect
  $middleUrl = "/m/$params";

  // Return the URL for the next step instead of redirecting
  echo json_encode(['redirectUrl' => $middleUrl]);
  exit();
} else {
  // Show the HTML redirect template
  $templatePath = join(DIRECTORY_SEPARATOR, [BASE_PATH, 'Resources', 'Private', 'Templates']);
  $template = new TemplateEngine($templatePath . DIRECTORY_SEPARATOR. 'view.html');

  // Assign variables
  $template->view->assign('baseUrl', BASE_URL);

  // Render output
  echo $template->render();
}
