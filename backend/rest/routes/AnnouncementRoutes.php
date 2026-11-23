<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/AnnouncementService.php';

class AnnouncementRoutes
{
    public static function register(): void
    {
        RouteHelper::registerCrud('/announcements', fn () => new AnnouncementService());
    }
}
