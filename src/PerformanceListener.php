<?php declare(strict_types=1);

namespace EdmondsCommerce\SymfonyPerformanceLogger;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

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

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        if ($this->isListCommand($event->getCommand())) {
            return;
        }

        $this->performanceLogger->startLogging(
            [
                'command' => $event->getCommand()->getName(),
            ]
        );
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if ($this->isListCommand($event->getCommand())) {
            return;
        }

        $this->performanceLogger->endLogging(
            [
                'command' => $event->getCommand()->getName()
            ]
        );
    }

    /*
     * HTTP requests
     */

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $this->performanceLogger->startLogging(
            [
                'Request URI'    => $event->getRequest()->getRequestUri(),
                'Request Params' => $event->getRequest()->request->all()
            ]
        );
    }

    public function onKernelTerminate(PostResponseEvent $event): void
    {
        $this->performanceLogger->endLogging(
            [
                'Request URI'    => $event->getRequest()->getRequestUri()
            ]
        );
    }

    private function isListCommand(Command $command): bool
    {
        return 'list' === $command->getName();
    }
}