<?php declare(strict_types=1);

namespace EdmondsCommerce\SymfonyPerformanceLogger;

use Psr\Log\LoggerInterface;

class PerformanceLogger
{
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

    public function __construct(LoggerInterface $logger)
    {
        $this->generateLogId();

        $this->logger = $logger;
    }

    public function startLogging(): void
    {
        $this->addTimestamp();

        $this->log('Started logging');
    }

    public function endLogging(): void
    {
        $this->addTimestamp();

        $startTimestamp = $this->getStartTimestamp();
        $endTimestamp   = $this->getLatestTimestamp();
        $totalRuntime   = $endTimestamp - $startTimestamp;

        $this->log(
            'Ending logging',
            [
                'Total Runtime' => $totalRuntime . ' seconds',
            ]
        );
    }

    public function logEvent(string $message, array $context = []): void
    {
        $this->addTimestamp();

        $previousTimestamp = $this->getPreviousTimestamp();
        $latestTimestamp   = $this->getLatestTimestamp();
        $sectionRuntime    = $latestTimestamp - $previousTimestamp;

        $context['Section Runtime'] = $sectionRuntime . ' seconds';

        $this->log($message, $context);
    }

    private function generateLogId(): void
    {
        $this->logId = md5(uniqid('', true) . $this->getTimestamp());
    }

    private function getTimestamp(): float
    {
        return microtime(true);
    }

    private function log(string $message, array $context = []): void
    {
        $timestamp = $this->getLatestTimestamp();

        $this->logger->info(
            "[{$this->logId}][$timestamp]" . $message,
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