<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/MaintenanceRequestService.php';

class MaintenanceRequestRoutes
{
    public static function register(): void
    {
        RouteHelper::registerCrud('/maintenance-requests', fn () => new MaintenanceRequestService());
    }
}
