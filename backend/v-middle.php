<?php
declare(strict_types=1);

/**
 * Obfuscating part 2 of 3
 * This is a part of viewing a mystic site by rewrite urls and tricking out the browser history.
 *
 * see view.php and v-show.php for the other steps.
 */

// Bootstrap
const BACKEND_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Factory\EnvConstantsHelper;
use Helpers\RateLimitHelper;
use Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Request;

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper

// Initialize rate limiter
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

// Break on missing parameter
$p = urlencode(rawurlencode($request->get('p')));
if ( $p === null ) {
  http_response_code(404);
  header("Location: /404");
  exit();
}

$finalUrl = "/show/$p";

?>
<!DOCTYPE html>
<html>
<head>
  <title>Redirecting...</title>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="0">
  <script>
    // Change URL and redirect
    window.onload = function() {
      window.history.replaceState(null, null, '<?= $finalUrl ?>');
      window.location.href = '<?= $finalUrl ?>';
    };
  </script>
</head>
<body style="background-color: #000; color: #fff;">
<p>Redirecting...</p>
</body>
</html>
