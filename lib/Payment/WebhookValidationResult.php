<?php

namespace lib\Payment;

class WebhookValidationResult
{
    private $isValid;
    private $message;
    private $data;

    public function __construct(bool $isValid, string $message = '', array $data = [])
    {
        $this->isValid = $isValid;
        $this->message = $message;
        $this->data = $data;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function valid(string $message = '', array $data = []): self
    {
        return new self(true, $message, $data);
    }

    public static function invalid(string $message = '', array $data = []): self
    {
        return new self(false, $message, $data);
    }
}
