<?php

namespace lib\Payment;

class PaymentResult
{
    private $success;
    private $transactionId;
    private $message;
    private $rawResponse;

    public function __construct(bool $success, string $transactionId = '', string $message = '', $rawResponse = null)
    {
        $this->success = $success;
        $this->transactionId = $transactionId;
        $this->message = $message;
        $this->rawResponse = $rawResponse;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public static function success(string $transactionId, string $message = '', $rawResponse = null): self
    {
        return new self(true, $transactionId, $message, $rawResponse);
    }

    public static function failure(string $message = '', $rawResponse = null): self
    {
        return new self(false, '', $message, $rawResponse);
    }
}
