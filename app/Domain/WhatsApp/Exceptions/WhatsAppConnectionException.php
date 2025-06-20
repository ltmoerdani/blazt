<?php

namespace App\Domain\WhatsApp\Exceptions;

use Exception;

class WhatsAppConnectionException extends Exception
{
    protected $message = 'WhatsApp connection failed';
    protected $code = 500;

    public static function connectionFailed(string $reason): self
    {
        return new self("Failed to initiate WhatsApp connection: {$reason}");
    }

    public static function nodeServiceFailed(string $reason): self
    {
        return new self("Node service call failed: {$reason}");
    }

    public static function sessionExpired(): self
    {
        return new self("WhatsApp session has expired");
    }

    public static function accountNotFound(): self
    {
        return new self("WhatsApp account not found");
    }
}
