<?php

/** Writes lines to backend/logs/info.log and backend/logs/error.log. */
class Logger
{
    public static function info(string $message): void
    {
        self::write('info.log', 'INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write('error.log', 'ERROR', $message);
    }

    private static function write(string $file, string $level, string $message): void
    {
        $directory = __DIR__ . '/../../logs';
        if (!is_dir($directory)) {
            return;
        }
        // One line per entry, so a message cannot fake extra log lines
        $message = str_replace(["\r", "\n"], ' ', $message);
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $level . ' ' . $message . PHP_EOL;
        @file_put_contents($directory . '/' . $file, $line, FILE_APPEND | LOCK_EX);
    }
}
