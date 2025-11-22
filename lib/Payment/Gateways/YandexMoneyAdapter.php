<?php

namespace lib\Payment\Gateways;

use lib\Payment\PaymentGatewayInterface;
use lib\Payment\PaymentResult;
use lib\Payment\RefundResult;
use lib\Payment\WebhookValidationResult;

class YandexMoneyAdapter implements PaymentGatewayInterface
{
    private $config;
    private $yandexMoneyClient;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->yandexMoneyClient = new \YandexMoney($config['access_token'] ?? '');
    }

    public function charge(array $paymentData): PaymentResult
    {
        try {
            $token = $paymentData['token'] ?? '';
            $receiver = $paymentData['receiver'] ?? '';
            $amount = $paymentData['amount'] ?? 0;

            $resp = $this->yandexMoneyClient->requestPaymentP2P($token, $receiver, $amount);
            if (!$resp->isSuccess()) {
                return PaymentResult::failure('Failed to request payment', $resp);
            }

            $requestId = $resp->getRequestId();

            $resp = $this->yandexMoneyClient->processPaymentByWallet($token, $requestId);
            if (!$resp->isSuccess()) {
                return PaymentResult::failure('Failed to process payment', $resp);
            }

            return PaymentResult::success($requestId, 'Payment processed successfully', $resp);
        } catch (\Exception $e) {
            return PaymentResult::failure('Error: ' . $e->getMessage());
        }
    }

    public function refund(string $transactionId, ?float $amount = null): RefundResult
    {
        return RefundResult::failure('Yandex Money refund not implemented');
    }

    public function validateWebhook(array $payload, string $signature = ''): WebhookValidationResult
    {
        return WebhookValidationResult::valid('Yandex Money uses OAuth flow', $payload);
    }

    public function getTransactionStatus(string $transactionId): array
    {
        return ['status' => 'unknown', 'transaction_id' => $transactionId];
    }

    public function getName(): string
    {
        return 'yandexmoney';
    }

    public function isEnabled(): bool
    {
        return !empty($this->config['access_token']);
    }
}
