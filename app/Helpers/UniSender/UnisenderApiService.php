<?php

namespace App\Helpers\UniSender;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

class UniSenderApiService
{
    private static $instance;
    protected $client;
    protected $apiKey;
    protected $headers;

    private function __construct()
    {
        $this->apiKey = env('API_KEY_UNISENDER');
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-KEY' => $this->apiKey,
        ];
        $this->client = new Client([
            'base_uri' => 'https://go2.unisender.ru/ru/transactional/api/v1/',
        ]);
    }

    /**
     * Get singleton instance
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sendEmail($data): array
    {
        $responses = [];

        foreach ($data as $user) {
            $requestBody = [
                "message" => [
                    "recipients" => [
                        [
                            "email" => $user['email'],
                            "substitutions" => [
                                "CustomerId" => $user['CustomerId'],
                                "to_name" => $user['to_name']
                            ],
                            "metadata" => [
                                "campaign_id" => "c77f4f4e-3561-49f7-9f07-c35be01b4f43",
                                "customer_hash" => "b253ac7"
                            ]
                        ]
                    ],
                    // "template_id" => "string",
                ]
            ];

            try {
                $response = $this->client->request('POST', 'email/send.json', [
                    'headers' => $this->headers,
                    'json' => $requestBody,
                ]);

                $responses[] = json_decode($response->getBody(), true);

            } catch (BadResponseException $e) {
                $response = $e->getResponse();
                $body = $response->getBody()->getContents();
                $responses[] = json_decode($body, true);

            } catch (GuzzleException $e) {
            }
        }

        return $responses;
    }
}
