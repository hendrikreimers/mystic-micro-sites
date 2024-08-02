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

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper

// Initialize rate limiter
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

// Get URL query parameters
$fileId = $request->get('id');
$keyParts = $request->get('key');

// Simple Check
if ( (!$fileId || !$keyParts) ) {
  die("Missing arguments");
}

$timestamp = time() + 10; // Current timestamp plus 10 seconds
$params = urlencode(rawurlencode(EnigmaBase64Service::enigmaBase64Encode(json_encode([$fileId, $keyParts, $timestamp]))));
$middleUrl = "/m/$params";
header("Location: $middleUrl", true, 303); // 303 See Other
exit();
