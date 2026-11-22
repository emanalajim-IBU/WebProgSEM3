<?php

/** An error that is safe to show to the client: message, HTTP status and optional field details. */
class ApiException extends RuntimeException
{
    private array $details;

    public function __construct(string $message, int $status = 400, array $details = [])
    {
        parent::__construct($message, $status);
        $this->details = $details;
    }

    public function getStatus(): int
    {
        return $this->getCode();
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
