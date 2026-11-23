<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/MessageService.php';

class MessageRoutes
{
    public static function register(): void
    {
        RouteHelper::registerCrud('/messages', fn () => new MessageService());

        // All messages of one channel, with the author's name
        Flight::route('GET /channels/@id:[0-9]+/messages', function ($id) {
            Flight::json((new MessageService())->getByChannel((int) $id));
        });
    }
}
