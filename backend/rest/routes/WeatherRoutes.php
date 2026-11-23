<?php

require_once __DIR__ . '/../services/WeatherService.php';

class WeatherRoutes
{
    public static function register(): void
    {
        Flight::route('GET /weather', function () {
            Flight::json((new WeatherService())->getCurrentWeather());
        });
    }
}
