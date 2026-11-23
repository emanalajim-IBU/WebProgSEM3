<?php

// Entry point of the REST API (FlightPHP).
// Never show PHP errors in the response; they go to backend/logs/error.log.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error.log');

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/rest/services/ApiException.php';
require_once __DIR__ . '/rest/services/Logger.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/ChannelService.php';
require_once __DIR__ . '/rest/services/MessageService.php';
require_once __DIR__ . '/rest/services/MaintenanceRequestService.php';
require_once __DIR__ . '/rest/services/AnnouncementService.php';
require_once __DIR__ . '/rest/services/WeatherService.php';

require_once __DIR__ . '/rest/routes/RouteHelper.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/ChannelRoutes.php';
require_once __DIR__ . '/rest/routes/MessageRoutes.php';
require_once __DIR__ . '/rest/routes/MaintenanceRequestRoutes.php';
require_once __DIR__ . '/rest/routes/AnnouncementRoutes.php';
require_once __DIR__ . '/rest/routes/WeatherRoutes.php';

// Flight must not print errors itself; our handler below answers with JSON.
Flight::set('flight.log_errors', false);

Flight::map('error', function (Throwable $e) {
    if ($e instanceof ApiException) {
        if ($e->getStatus() >= 500) {
            Logger::error('ApiException ' . $e->getStatus() . ': ' . $e->getMessage());
        }
        $body = ['error' => $e->getMessage()];
        if (!empty($e->getDetails())) {
            $body['details'] = $e->getDetails();
        }
        Flight::json($body, $e->getStatus());
        return;
    }

    Logger::error(get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');

    // Integrity constraint (duplicate value, missing parent row) is a client error
    if ($e instanceof PDOException && (string) $e->getCode() === '23000') {
        Flight::json(['error' => 'The request conflicts with existing data'], 400);
        return;
    }

    // No stack traces, SQL or file paths in the response
    Flight::json(['error' => 'Internal server error'], 500);
});

Flight::map('notFound', function () {
    Flight::json(['error' => 'Route not found'], 404);
});

Flight::route('GET /', function () {
    Flight::json([
        'name' => 'Building Management System API',
        'documentation' => Flight::request()->base . '/swagger/',
    ]);
});

// OpenAPI document; the server URL is set from the current location so "Try it out" works anywhere
Flight::route('GET /openapi.json', function () {
    $json = file_get_contents(__DIR__ . '/../docs/openapi.json');
    $spec = json_decode($json === false ? '' : $json, true);
    if (!is_array($spec)) {
        throw new ApiException('OpenAPI document is missing or invalid', 500);
    }
    $spec['servers'] = [['url' => Flight::request()->base]];
    Flight::json($spec);
});

UserRoutes::register();
ChannelRoutes::register();
MessageRoutes::register();
MaintenanceRequestRoutes::register();
AnnouncementRoutes::register();
WeatherRoutes::register();

Flight::start();
