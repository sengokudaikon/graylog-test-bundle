<?php

namespace Prodavay\GraylogTestBundle\Service;

use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use GuzzleHttp\Client;
use Monolog\Handler\GelfHandler;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Monolog\Logger as MonologLogger;

class LogService extends MonologLogger
{
    /**
     * @param  string  $loggerHost
     * @param  string  $loggerPort
     * @param  string  $loggerName
     * @param  array   $handlers
     * @param  array   $processors
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

    /**
     * @param Client            $client
     * @param string            $uri
     * @param ResponseInterface $response
     * @param string            $responseBody
     * @param array             $body
     * @param array             $headers
     *
     * @return bool
     */
    public function writeExternalApiLog(
        Client $client,
        string $uri,
        ResponseInterface $response,
        string $responseBody,
        array $body = [],
        array $headers = []
    ): bool {
        $responseBody = $this->decodeUnicode($responseBody);
        $method = $client->getConfig('method');
        try {
            $this->info(
                'placeholder',
                [
                    'request' => [
                        'method'=> $method,
                        'url' => "{$client->getConfig('base_uri')}$uri",
                        'headers' => array_merge($headers, $client->getConfig('headers')),
                        'body' => $body,
                    ],
                    'response' => [
                        'code' => $response->getStatusCode(),
                        'headers' => $response->getHeaders(),
                        'body' => $this->isJson($responseBody) ? json_decode($responseBody, true) : $responseBody,
                    ],
                ]
            );

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
    }

    /**
     * @param  string        $method
     * @param  string        $uri
     * @param  array         $requestHeaders
     * @param  array | null  $requestBody
     * @param  array         $responseHeaders
     * @param  int           $httpCode
     * @param  array | null  $responseBody
     *
     * @return bool
     */
    public function writeApiLog(
        string $method,
        string $uri,
        array $requestHeaders,
        ?array $requestBody,
        array $responseHeaders,
        int $httpCode,
        ?array $responseBody
    ): bool {
        try {
            $this->info(
                'placeholder',
                [
                    'request' => [
                        'method' => $method,
                        'url' => $uri,
                        'headers' => $requestHeaders,
                        'body' => $requestBody,
                    ],
                    'response' => [
                        'code' => $httpCode,
                        'headers' => $responseHeaders,
                        'body' => $responseBody,
                    ],
                ]
            );

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
    }

    /**
     * @param  string  $baseUrl
     * @param  string  $command
     * @param  string  $filename
     * @param  bool    $success
     *
     * @return bool
     */
    public function writeYandexCloudApi(
        string $baseUrl,
        string $command,
        string $filename,
        ?bool $success = false
    ): bool {
        try {
            $this->info(
                'placeholder',
                [
                    'request' => [
                        'url' => $baseUrl,
                        'body' => [
                            'filepath' => $filename,
                            'command' => $command,
                        ]
                    ],
                    'response' => [
                        'success' => $success
                    ],
                ]
            );

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
    }

    /**
     * @param string $smtpLog
     *
     * @return bool
     */
    public function writeSmtpLog(string $smtpLog): bool
    {
        try {
            $this->info('placeholder', ['smtpLog' => $smtpLog]);

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
    }


    /**
     * @param string $firebaseLog
     *
     * @return bool
     */
    public function writeFirebaseLog(string $firebaseLog): bool
    {
        try {
            $this->info('placeholder', ['firebasePushLog' => $firebaseLog]);

            return true;
        } catch (RuntimeException $exception) {
            return false;
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function decodeUnicode($string): string
    {
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function (array $match): string {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
            },
            $string
        );
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isJson(string $string): bool
    {
        return (is_string($string) && (is_object(json_decode($string))));
    }
}