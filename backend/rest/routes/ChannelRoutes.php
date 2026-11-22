<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/ChannelService.php';

class ChannelRoutes
{
    public static function register(): void
    {
        RouteHelper::registerCrud('/channels', fn () => new ChannelService());
    }
}
