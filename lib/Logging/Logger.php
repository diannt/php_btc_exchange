<?php

namespace lib\Logging;

class Logger
{
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';

    private $logPath;
    private $minLogLevel;

    public function __construct(string $logPath = null, string $minLogLevel = self::INFO)
    {
        $this->logPath = $logPath ?? $_SERVER['DOCUMENT_ROOT'] . '/logs/app.log';
        $this->minLogLevel = $minLogLevel;
        $this->ensureLogDirectory();
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logLine = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        error_log($logLine, 3, $this->logPath);
    }

    private function shouldLog(string $level): bool
    {
        $levels = [
            self::DEBUG => 0,
            self::INFO => 1,
            self::WARNING => 2,
            self::ERROR => 3,
            self::CRITICAL => 4,
        ];

        return $levels[$level] >= $levels[$this->minLogLevel];
    }

    private function ensureLogDirectory(): void
    {
        $dir = dirname($this->logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
