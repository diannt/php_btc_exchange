<?php

namespace lib\Services;

use lib\Payment\PaymentGatewayFactory;
use lib\Payment\PaymentResult;
use lib\Logging\Logger;

class PaymentService
{
    private $gatewayFactory;
    private $logger;

    public function __construct(PaymentGatewayFactory $factory = null)
    {
        $this->gatewayFactory = $factory ?? new PaymentGatewayFactory();
        $this->logger = new Logger();
    }

    public function processPayment(string $gatewayName, array $paymentData): PaymentResult
    {
        try {
            $gateway = $this->gatewayFactory->create($gatewayName);

            if (!$gateway->isEnabled()) {
                throw new \Exception("Payment gateway '$gatewayName' is not enabled");
            }

            $this->logger->info("Processing payment via $gatewayName", [
                'amount' => $paymentData['amount'] ?? 0,
                'currency' => $paymentData['currency'] ?? 'N/A',
                'user_id' => $paymentData['user_id'] ?? 0,
            ]);

            $result = $gateway->charge($paymentData);

            if ($result->isSuccessful()) {
                $this->logger->info("Payment successful", [
                    'gateway' => $gatewayName,
                    'transaction_id' => $result->getTransactionId(),
                ]);
            } else {
                $this->logger->warning("Payment failed", [
                    'gateway' => $gatewayName,
                    'message' => $result->getMessage(),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("Payment error: " . $e->getMessage(), [
                'gateway' => $gatewayName,
            ]);

            return PaymentResult::failure('Payment processing error: ' . $e->getMessage());
        }
    }

    public function refundPayment(string $gatewayName, string $transactionId, ?float $amount = null)
    {
        try {
            $gateway = $this->gatewayFactory->create($gatewayName);

            $this->logger->info("Processing refund via $gatewayName", [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            $result = $gateway->refund($transactionId, $amount);

            if ($result->isSuccessful()) {
                $this->logger->info("Refund successful", [
                    'gateway' => $gatewayName,
                    'refund_id' => $result->getRefundId(),
                ]);
            } else {
                $this->logger->warning("Refund failed", [
                    'gateway' => $gatewayName,
                    'message' => $result->getMessage(),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("Refund error: " . $e->getMessage());

            return \lib\Payment\RefundResult::failure('Refund processing error: ' . $e->getMessage());
        }
    }

    public function validateWebhook(string $gatewayName, array $payload, string $signature = '')
    {
        try {
            $gateway = $this->gatewayFactory->create($gatewayName);
            $result = $gateway->validateWebhook($payload, $signature);

            if ($result->isValid()) {
                $this->logger->info("Webhook validated", ['gateway' => $gatewayName]);
            } else {
                $this->logger->warning("Webhook validation failed", [
                    'gateway' => $gatewayName,
                    'message' => $result->getMessage(),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("Webhook validation error: " . $e->getMessage());

            return \lib\Payment\WebhookValidationResult::invalid('Webhook validation error: ' . $e->getMessage());
        }
    }

    public function getTransactionStatus(string $gatewayName, string $transactionId): array
    {
        try {
            $gateway = $this->gatewayFactory->create($gatewayName);
            return $gateway->getTransactionStatus($transactionId);
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
