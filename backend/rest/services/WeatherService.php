<?php

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/Logger.php';

/** Calls the external OpenWeatherMap API and returns the weather at the building. */
class WeatherService
{
    private const API_URL = 'https://api.openweathermap.org/data/2.5/weather';
    private const CONNECT_TIMEOUT_SECONDS = 3;
    private const TIMEOUT_SECONDS = 5;
    private const FRIENDLY_MESSAGE = 'Weather information is currently unavailable.';

    public function getCurrentWeather(): array
    {
        $config = $this->loadConfig();
        $apiKey = (string) ($config['externalApiKey'] ?? '');
        $city = (string) ($config['weatherCity'] ?? 'London');

        if ($apiKey === '' || $apiKey === 'change-me') {
            Logger::error('Weather API: externalApiKey is not set in backend/config/config.php');
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }
        if (!function_exists('curl_init')) {
            Logger::error('Weather API: the PHP curl extension is not enabled');
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }

        $url = self::API_URL . '?' . http_build_query([
            'q' => $city,
            'units' => 'metric',
            'appid' => $apiKey,
        ]);

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT_SECONDS,
            CURLOPT_TIMEOUT => self::TIMEOUT_SECONDS,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        // The URL contains the API key, so it is never written to the logs
        if ($body === false) {
            Logger::error('Weather API request failed: ' . $curlError);
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }
        if ($status !== 200) {
            Logger::error('Weather API returned HTTP ' . $status);
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }

        $data = json_decode($body, true);
        if (!is_array($data) || !isset($data['main']['temp'], $data['main']['humidity'])) {
            Logger::error('Weather API returned an unexpected response');
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }

        Logger::info('External API call: OpenWeatherMap current weather OK');

        return [
            'city' => $data['name'] ?? $city,
            'temperature' => (int) round($data['main']['temp']),
            'feels_like' => (int) round($data['main']['feels_like'] ?? $data['main']['temp']),
            'humidity' => (int) $data['main']['humidity'],
            'wind_speed_kmh' => (int) round(($data['wind']['speed'] ?? 0) * 3.6),
            'description' => ucfirst((string) ($data['weather'][0]['description'] ?? '')),
            'icon' => $data['weather'][0]['icon'] ?? null,
        ];
    }

    private function loadConfig(): array
    {
        $path = __DIR__ . '/../../config/config.php';
        if (!file_exists($path)) {
            Logger::error('Weather API: backend/config/config.php is missing');
            throw new ApiException(self::FRIENDLY_MESSAGE, 503);
        }
        $config = require $path;
        return is_array($config) ? $config : [];
    }
}
