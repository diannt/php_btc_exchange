<?php

namespace lib\Payment;

class RefundResult
{
    private $success;
    private $refundId;
    private $message;
    private $rawResponse;

    public function __construct(bool $success, string $refundId = '', string $message = '', $rawResponse = null)
    {
        $this->success = $success;
        $this->refundId = $refundId;
        $this->message = $message;
        $this->rawResponse = $rawResponse;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public static function success(string $refundId, string $message = '', $rawResponse = null): self
    {
        return new self(true, $refundId, $message, $rawResponse);
    }

    public static function failure(string $message = '', $rawResponse = null): self
    {
        return new self(false, '', $message, $rawResponse);
    }
}
