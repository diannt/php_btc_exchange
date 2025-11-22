<?php

namespace lib\Payment;

interface PaymentGatewayInterface
{
    public function charge(array $paymentData): PaymentResult;
    public function refund(string $transactionId, ?float $amount = null): RefundResult;
    public function validateWebhook(array $payload, string $signature = ''): WebhookValidationResult;
    public function getTransactionStatus(string $transactionId): array;
    public function getName(): string;
    public function isEnabled(): bool;
}
