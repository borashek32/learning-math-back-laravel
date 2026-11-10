<?php

declare(strict_types=1);

namespace App\Helpers\UniSender;

use App\Helpers\UniSender\Dto\EmailDataDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class UniSenderApiService
{
    private static ?self $instance = null;

    protected Client $client;

    protected string $apiKey;

    /** @var array<string, string> */
    protected array $headers;

    private function __construct()
    {
        $apiKey = config('unisender.api_key');
        $baseUrl = config('unisender.base_url');

        if (!is_string($apiKey) || $apiKey === '') {
            throw new InvalidArgumentException('UniSender API key must be a non-empty string.');
        }

        if (!is_string($baseUrl) || $baseUrl === '') {
            throw new InvalidArgumentException('UniSender base URL must be a non-empty string.');
        }

        $this->apiKey = $apiKey;
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-KEY' => $this->apiKey,
        ];
        $this->client = new Client([
            'base_uri' => $baseUrl,
        ]);
    }

    /**
     * Get singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return list<array<array-key, mixed>>
     */
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

            return $this->decodeResponse($response->getBody()->getContents());
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody()->getContents();

            return $this->decodeResponse($body);
        } catch (GuzzleException) {
            return [];
        }
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    private function decodeResponse(string $body): array
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? [$decoded] : [];
    }
}
