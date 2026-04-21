<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

// The worker file is in /app/, so vendor is at /app/vendor
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from /app/.env
(new Dotenv())->bootEnv(__DIR__ . '/.env');

// Create and boot the kernel once
$kernel = new Kernel($_ENV['APP_ENV'] ?? 'prod', (bool) ($_ENV['APP_DEBUG'] ?? false));
$kernel->boot();

// Handle requests in the worker loop
while (frankenphp_handle_request(function () use ($kernel) {
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

    return $response;
})) {
    // Reset the kernel for the next request
    $kernel->reboot(null);
}