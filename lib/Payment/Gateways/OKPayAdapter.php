<?php

namespace lib\Payment\Gateways;

use lib\Payment\PaymentGatewayInterface;
use lib\Payment\PaymentResult;
use lib\Payment\RefundResult;
use lib\Payment\WebhookValidationResult;

class OKPayAdapter implements PaymentGatewayInterface
{
    private $config;
    private $okpayClient;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->okpayClient = new \OKPay();
    }

    public function charge(array $paymentData): PaymentResult
    {
        try {
            $response = $this->okpayClient->send_money(
                $this->config['wallet_id'] ?? '',
                $this->config['api_password'] ?? '',
                $paymentData['currency'] ?? 'USD',
                $paymentData['receiver_email'] ?? '',
                $paymentData['amount'] ?? 0,
                $paymentData['description'] ?? 'Payment',
                true,
                $paymentData['invoice_id'] ?? ''
            );

            if ($response && isset($response['ID'])) {
                return PaymentResult::success($response['ID'], 'Payment processed successfully', $response);
            }

            return PaymentResult::failure('Failed to process payment', $response);
        } catch (\Exception $e) {
            return PaymentResult::failure('Error: ' . $e->getMessage());
        }
    }

    public function refund(string $transactionId, ?float $amount = null): RefundResult
    {
        return RefundResult::failure('OKPay does not support automatic refunds');
    }

    public function validateWebhook(array $payload, string $signature = ''): WebhookValidationResult
    {
        try {
            $result = $this->okpayClient->verify_notification($payload);

            if ($result === 'VERIFIED') {
                return WebhookValidationResult::valid('Webhook verified', $payload);
            }

            return WebhookValidationResult::invalid('Webhook verification failed');
        } catch (\Exception $e) {
            return WebhookValidationResult::invalid('Webhook error: ' . $e->getMessage());
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        return ['status' => 'unknown', 'transaction_id' => $transactionId];
    }

    public function getName(): string
    {
        return 'okpay';
    }

    public function isEnabled(): bool
    {
        return !empty($this->config['wallet_id']) && !empty($this->config['api_password']);
    }
}
