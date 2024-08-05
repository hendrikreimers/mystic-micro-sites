<?php
declare(strict_types=1);

// Bootstrap
const BACKEND_CONTEXT = true;
require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Factory\EnvConstantsHelper;
use Helpers\CsrfTokenHelper;
use Helpers\JwtHelper;
use Helpers\RateLimitHelper;
use Helpers\ResponseHelper;
use Services\ApiService;
use Symfony\Component\HttpFoundation\Request;
use Utility\StringUtility;

// Global Environment Constants declaration
EnvConstantsHelper::defineEnvConstants();

// Initialize Class Instances
$request = Request::createFromGlobals(); // Symfony Request Handler
$response = new ResponseHelper(); // ResponseHelper
$csrfTokenHelper = new CsrfTokenHelper($response, $request); // Initialize CSRF Token Handler
$jwtHelper = new JwtHelper(); // Initialize JWT Helper
$encryptionService = new Services\EncryptionService(); // Initialize encryption service

// Initialize the API Endpoint Service and API headers
$apiService = new ApiService($request, $response, $encryptionService, $jwtHelper, $csrfTokenHelper);
$apiService->sendApiHeader();

// Initialize rate limiter (after API headers send)
$rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter

// Get the requested action and call the requested API Endpoint (Magic call)
$actionName = StringUtility::escapeString($request->query->get('action', ''), true);
$apiService->{$actionName}(); // Magic call the endpoint based on action name
