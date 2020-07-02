<?php

namespace Prodavay\GraylogTestBundle\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class LogService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $environment;

    /**
     * Разрешенное окружение для записи
     */
    private const PROHIBITED_ENVIRONMENT = 'prod';

    /**
     * @param Logger $logger
     * @param string $environment
     */
    public function __construct(Logger $logger, string $environment)
    {
        $this->logger = $logger;
        $this->environment = $environment;
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

        try {
            $this->logger->info(
                'placeholder',
                [
                    'request' => [
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
            $this->logger->info(
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
     * @param array $log
     *
     * @return bool
     */
    public function writeAtolApiLog(array $log): bool
    {
        try {
            $this->logger->info('placeholder', $log);

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
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

    /**
     * @param string $log
     *
     * @return bool
     */
    public function writeTestLog(string $log): bool
    {
        $this->logger->info(
            'placeholder',
            [
                'step' => $log,
            ]
        );

        return true;
    }

    /**
     * @param  string      $uri
     * @param  array       $requestHeaders
     * @param  array | null  $requestBody
     * @param  array       $responseHeaders
     * @param  int         $httpCode
     * @param  array | null  $responseBody
     *
     * @return bool
     */
    public function writeApiLog(
        string $uri,
        array $requestHeaders,
        ?array $requestBody,
        array $responseHeaders,
        int $httpCode,
        ?array $responseBody
    ): bool {
        try {
            $this->logger->info(
                'placeholder',
                [
                    'request' => [
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
     * @param array  $oldSchedules
     * @param array  $newSchedules
     * @param string $search
     *
     * @return bool
     */
    public function writeGrabOnesLog(
        array $oldSchedules,
        array $newSchedules,
        string $search
    ): bool {
        try {
            $this->logger->info(
                'placeholder',
                [
                    'search_string' => $search,
                    'old_schedules' => $oldSchedules,
                    'new_schedules' => $newSchedules,
                ]
            );

            return true;
        } catch (RuntimeException $exception) {
            // Обработка ошибки подключения к хосту логгера
            return false;
        }
    }

//    /**
//     * @param User        $user
//     * @param int         $merchantId
//     * @param Rate        $newRate
//     * @param Rate | null $oldRate
//     *
//     * @return bool
//     */
//    public function writeRateLog(
//        User $user,
//        int $merchantId,
//        ?Rate $newRate = null,
//        Rate $oldRate = null
//    ): bool {
//        try {
//            return $this->logger->addInfo('placeholder', [
//                'search_string' => "Изменение тарифа мерча $merchantId",
//                'user' => [
//                    'id' => $user->getId(),
//                    'name' => $user->getFullName(),
//                    'phone' => $user->getPhone()->getValue(),
//                ],
//                'new_rate' => empty($newRate) ? [] : $this->getRateData($newRate),
//                'old_rate' => empty($oldRate) ? [] : $this->getRateData($oldRate),
//            ]);
//        } catch (RuntimeException $exception) {
//            // Обработка ошибки подключения к хосту логгера
//            return false;
//        }
//    }

//    /**
//     * @param Rate $rate
//     *
//     * @return array
//     */
//    private function getRateData(Rate $rate): array
//    {
//        return [
//            'activation_date' => $rate->getActivationDate()->format('Y-m-d'),
//            'deactivation_date' => empty($rate->getDeactivationDate()) ? null : $rate->getDeactivationDate()->format('Y-m-d'),
//            'default' => $rate->isDefault(),
//            'name' => $rate->getName(),
//            'type' => $rate->getRateType()->getName(),
//            'three_month_discount' => $rate->getThreeMonthDiscount(),
//            'six_month_discount' => $rate->getSixMonthDiscount(),
//        ];
//    }

    /**
     * @param array  $schedules
     * @param string $search
     *
     * @return bool
     */
    public function writeScheduleLog(array $schedules, string $search): bool
    {
        if (self::PROHIBITED_ENVIRONMENT !== $this->environment) {
            return false;
        }

        try {
            $this->logger->info('placeholder', [
                'search_string' => $search,
                'schedules' => $schedules,
            ]);

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
            $this->logger->info('placeholder', ['smtpLog' => $smtpLog]);

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
            $this->logger->info('placeholder', ['firebasePushLog' => $firebaseLog]);

            return true;
        } catch (RuntimeException $exception) {
            return false;
        }
    }
}