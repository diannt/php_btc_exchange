<?php

namespace lib\Payment;

class PaymentGatewayFactory
{
    private static $gateways = [];
    private static $config = [];

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function create(string $gatewayName): PaymentGatewayInterface
    {
        $gatewayName = strtolower($gatewayName);

        if (isset(self::$gateways[$gatewayName])) {
            return self::$gateways[$gatewayName];
        }

        $adapterClass = self::resolveAdapterClass($gatewayName);
        $config = self::$config[$gatewayName] ?? [];

        $instance = new $adapterClass($config);
        self::$gateways[$gatewayName] = $instance;

        return $instance;
    }

    private static function resolveAdapterClass(string $gatewayName): string
    {
        $mapping = [
            'okpay' => 'lib\Payment\Gateways\OKPayAdapter',
            'perfectmoney' => 'lib\Payment\Gateways\PerfectMoneyAdapter',
            'yandexmoney' => 'lib\Payment\Gateways\YandexMoneyAdapter',
            'egopay' => 'lib\Payment\Gateways\EgoPayAdapter',
        ];

        if (!isset($mapping[$gatewayName])) {
            throw new \Exception("Payment gateway '$gatewayName' is not supported");
        }

        $className = $mapping[$gatewayName];
        if (!class_exists($className)) {
            throw new \Exception("Payment gateway adapter '$className' not found");
        }

        return $className;
    }

    public static function getAvailableGateways(): array
    {
        return ['okpay', 'perfectmoney', 'yandexmoney', 'egopay'];
    }

    public static function clearCache(?string $gatewayName = null): void
    {
        if ($gatewayName) {
            unset(self::$gateways[strtolower($gatewayName)]);
        } else {
            self::$gateways = [];
        }
    }
}
