<?php

namespace Prodavay\GraylogTestBundle\Service;

use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Monolog\Handler\GelfHandler;
use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    /**
     * @param string $loggerHost
     * @param string $loggerPort
     * @param string $loggerName
     * @param array  $handlers
     * @param array  $processors
     */
    public function __construct(
        string $loggerHost,
        string $loggerPort,
        string $loggerName,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($loggerName, $handlers, $processors);

        $this->pushHandler(
            new GelfHandler(
                new Publisher(
                    new UdpTransport($loggerHost, $loggerPort)
                )
            )
        );
    }
}