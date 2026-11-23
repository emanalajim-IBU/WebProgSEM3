<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/UserService.php';

class UserRoutes
{
    public static function register(): void
    {
        RouteHelper::registerCrud('/users', fn () => new UserService());
    }
}
