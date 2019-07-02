<?php declare(strict_types=1);

namespace EdmondsCommerce\SymfonyPerformanceLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PerformanceLogger
{
    /**
     * @var float
     */
    private $criticalRuntime;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $logId;
    /**
     * @var array
     */
    private $timestamps = [];

    public function __construct(
        float $criticalRuntime,
        LoggerInterface $logger
    ) {
        $this->generateLogId();

        $this->criticalRuntime = $criticalRuntime;
        $this->logger          = $logger;
    }

    public function startLogging(array $context = []): void
    {
        $this->addTimestamp();

        $this->log('Start Logging', $context);
    }

    public function endLogging(array $context = []): void
    {
        $this->addTimestamp();

        $startTimestamp        = $this->getStartTimestamp();
        $endTimestamp          = $this->getLatestTimestamp();
        $totalRuntime          = $endTimestamp - $startTimestamp;
        $formattedTotalRuntime = $this->formatTimestamp($totalRuntime);

        $this->addContext($context, 'Total Runtime', "$formattedTotalRuntime seconds");

        $level = $totalRuntime < $this->criticalRuntime ? LogLevel::INFO : LogLevel::CRITICAL;

        $this->log('End Logging', $context, $level);
    }

    public function logEvent(string $message, array $context = [], string $level = LogLevel::INFO): void
    {
        $this->addTimestamp();

        $previousTimestamp = $this->getPreviousTimestamp();
        $latestTimestamp   = $this->getLatestTimestamp();
        $sectionRuntime    = $this->formatTimestamp($latestTimestamp - $previousTimestamp);

        $this->addContext($context, 'Section Runtime', "$sectionRuntime seconds");

        $this->log($message, $context, $level);
    }

    private function generateLogId(): void
    {
        $this->logId = md5(uniqid('', true) . $this->getTimestamp());
    }

    private function getTimestamp(): float
    {
        return microtime(true);
    }

    private function log(string $message, array $context = [], string $level = LogLevel::INFO): void
    {
        $timestamp = round($this->getLatestTimestamp(), 4);

        $this->logger->log(
            $level,
            "[log_id:{$this->logId}] [microtime:$timestamp] [message:$message]",
            $context
        );
    }

    private function getStartTimestamp(): float
    {
        $this->assertTimestampsExists();

        return $this->timestamps[0];
    }

    private function getLatestTimestamp(): float
    {
        $this->assertTimestampsExists();

        return $this->timestamps[\count($this->timestamps) - 1];
    }

    private function getPreviousTimestamp(): float
    {
        $this->assertTimestampsExists();

        return $this->timestamps[\count($this->timestamps) - 2];
    }

    private function addTimestamp(): void
    {
        $this->timestamps[] = $this->getTimestamp();
    }

    private function formatTimestamp(float $timestamp): string
    {
        return number_format($timestamp, 4);
    }

    private function addContext(array &$context, string $key, $value): void
    {
        $context[$key] = $value;
    }

    private function assertTimestampsExists(): void
    {
        if (0 !== \count($this->timestamps)) {
            return;
        }

        throw new \RuntimeException(
            "No timestamps added yet. Make sure you've called startLogging()"
        );
    }
}