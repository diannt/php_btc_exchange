<?php

namespace lib\Logging;

class AuditLog
{
    private $logPath;

    public function __construct()
    {
        $this->logPath = $_SERVER['DOCUMENT_ROOT'] . '/logs/audit.log';
        $this->ensureLogDirectory();
    }

    public function record(string $action, int $userId, array $context = []): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'user_id' => $userId,
            'ip_address' => $this->getClientIp(),
            'context' => json_encode($context),
        ];

        $this->writeToFile($entry);
    }

    public function getLogsByAction(string $action, array $filters = []): array
    {
        return [];
    }

    public function getLogsByUser(int $userId): array
    {
        return [];
    }

    private function writeToFile(array $entry): void
    {
        $line = json_encode($entry) . PHP_EOL;
        error_log($line, 3, $this->logPath);
    }

    private function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    private function ensureLogDirectory(): void
    {
        $dir = dirname($this->logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
