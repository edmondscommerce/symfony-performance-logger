<?php declare(strict_types=1);

namespace EdmondsCommerce\SymfonyPerformanceLogger;

class PerformanceListener
{
    private $performanceLogger;

    public function __construct(PerformanceLogger $performanceLogger)
    {
        $this->performanceLogger = $performanceLogger;
    }

    /*
     * Console commands
     */

    public function onConsoleCommand(): void
    {
        $this->performanceLogger->startLogging();
    }

    public function onConsoleTerminate(): void
    {
        $this->performanceLogger->endLogging();
    }

    /*
     * HTTP requests
     */

    public function onKernelRequest(): void
    {
        $this->performanceLogger->startLogging();
    }

    public function onKernelTerminate(): void
    {
        $this->performanceLogger->endLogging();
    }
}