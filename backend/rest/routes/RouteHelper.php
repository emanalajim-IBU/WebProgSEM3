<?php

require_once __DIR__ . '/../services/ApiException.php';

class RouteHelper
{
    /**
     * Registers the five CRUD routes for one entity.
     * $factory creates the service when a request arrives (routes never call DAO classes).
     */
    public static function registerCrud(string $path, callable $factory): void
    {
        Flight::route("GET $path", function () use ($factory) {
            Flight::json($factory()->listFiltered($_GET));
        });

        Flight::route("GET $path/@id:[0-9]+", function ($id) use ($factory) {
            Flight::json($factory()->getById((int) $id));
        });

        Flight::route("POST $path", function () use ($factory) {
            Flight::json($factory()->create(self::readJsonBody()), 201);
        });

        $update = function ($id) use ($factory) {
            Flight::json($factory()->update((int) $id, self::readJsonBody()));
        };
        Flight::route("PUT $path/@id:[0-9]+", $update);
        Flight::route("PATCH $path/@id:[0-9]+", $update);

        Flight::route("DELETE $path/@id:[0-9]+", function ($id) use ($factory) {
            $factory()->delete((int) $id);
            Flight::json(['message' => 'Deleted successfully']);
        });
    }

    /** Reads the request body as a JSON object (400 if it is missing or invalid). */
    public static function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw === false ? '' : $raw, true);
        if (!is_array($data)) {
            throw new ApiException('Request body must be a valid JSON object', 400);
        }
        return $data;
    }
}
