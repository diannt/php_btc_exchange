<?php

namespace lib\Payment\Gateways;

use lib\Payment\PaymentGatewayInterface;
use lib\Payment\PaymentResult;
use lib\Payment\RefundResult;
use lib\Payment\WebhookValidationResult;

class PerfectMoneyAdapter implements PaymentGatewayInterface
{
    private $config;
    private $perfectMoneyClient;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->perfectMoneyClient = new \PerfectMoney();
    }

    public function charge(array $paymentData): PaymentResult
    {
        try {
            $this->perfectMoneyClient->setAccountID($this->config['account_id'] ?? '');
            $this->perfectMoneyClient->setPassPhrase($this->config['pass_phrase'] ?? '');

            $response = $this->perfectMoneyClient->transfer(
                $this->config['account'] ?? '',
                $paymentData['receiver_account'] ?? '',
                $paymentData['amount'] ?? 0,
                $paymentData['description'] ?? 'Payment'
            );

            if ($response && isset($response['PAYMENT_BATCH_NUM'])) {
                return PaymentResult::success(
                    $response['PAYMENT_BATCH_NUM'],
                    'Payment processed successfully',
                    $response
                );
            }

            return PaymentResult::failure('Failed to process payment', $response);
        } catch (\Exception $e) {
            return PaymentResult::failure('Error: ' . $e->getMessage());
        }
    }

    public function refund(string $transactionId, ?float $amount = null): RefundResult
    {
        return RefundResult::failure('Perfect Money refund not yet implemented');
    }

    public function validateWebhook(array $payload, string $signature = ''): WebhookValidationResult
    {
        try {
            $alternatePassPhrase = strtoupper(md5($this->config['alternate_pass_phrase'] ?? ''));

            $string =
                ($payload['PAYMENT_ID'] ?? '') . ':' .
                ($payload['PAYEE_ACCOUNT'] ?? '') . ':' .
                ($payload['PAYMENT_AMOUNT'] ?? '') . ':' .
                ($payload['PAYMENT_UNITS'] ?? '') . ':' .
                ($payload['PAYMENT_BATCH_NUM'] ?? '') . ':' .
                ($payload['PAYER_ACCOUNT'] ?? '') . ':' .
                $alternatePassPhrase . ':' .
                ($payload['TIMESTAMPGMT'] ?? '');

            $hash = strtoupper(md5($string));

            if ($hash === ($payload['V2_HASH'] ?? '')) {
                return WebhookValidationResult::valid('Webhook verified', $payload);
            }

            return WebhookValidationResult::invalid('Hash mismatch');
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
        return 'perfectmoney';
    }

    public function isEnabled(): bool
    {
        return !empty($this->config['account_id']) && !empty($this->config['pass_phrase']);
    }
}
