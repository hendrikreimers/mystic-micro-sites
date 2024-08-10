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
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Processing...</title>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="0">
  <script>
    window.onload = function () {
      // Extract the hash part of the URL
      let hash = window.location.hash.slice(1).split('/'); // Remove the "#" from the beginning

      // Create an object from the hash parameters
      let params = new URLSearchParams('id=' + hash[1] + '&key=' + hash[0]);
      let fileId = params.get('id');
      let keyParts = params.get('key');

      let queryParams =  new URLSearchParams(window.location.search);
      let noVcard = queryParams.get('noVcard') || '0';

      // Send the data to the server via POST
      if (fileId && keyParts) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.pathname, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
          if (xhr.status === 200) {
            // Parse the response JSON
            var response = JSON.parse(xhr.responseText);

            // Leitet weiter zu der URL, die der Server zurückgegeben hat
            window.location.replace( response.redirectUrl);
          }
        };

        xhr.send("id=" + encodeURIComponent(fileId) + "&key=" + encodeURIComponent(keyParts) + "&noVcard=" + encodeURIComponent(noVcard));
      } else {
        window.location.replace('/404');
      }
    };
  </script>
</head>
<body>
<p>Process data...</p>
</body>
</html>
