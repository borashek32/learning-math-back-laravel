<?php

namespace App\Helpers\UniSender;

use App\Helpers\UniSender\Dto\EmailDataDto;
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
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function sendEmail(EmailDataDto $data): array
    {
        $requestBody = [
            'message' => [
                'recipients' => [[
                    'email' => $data->email,
                    'substitutions' => $data->data,
                ]],
                'template_id' => $data->template_id,
            ],
        ];

        try {
            $response = $this->client->request('POST', 'email/send.json', [
                'headers' => $this->headers,
                'json' => $requestBody,
            ]);

            return [json_decode($response->getBody(), true)];
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody()->getContents();

            return [json_decode($body, true)];
        } catch (GuzzleException) {
            return [];
        }
    }
}
