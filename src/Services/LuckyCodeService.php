<?php

namespace LuckyCode\IntegrationHelper\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use LuckyCode\IntegrationHelper\Models\CredentialModel;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;
use LuckyCode\IntegrationHelper\Models\RevealCodeRequest;
use LuckyCode\IntegrationHelper\Models\RedeemCodeRequest;
use LuckyCode\IntegrationHelper\Models\CustomerPakageLogQuery;
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Support\ApiResponse;

class LuckyCodeService implements LuckyCodeServiceContract
{
    private Client $http;
    private ?array $cachedToken = null; // ['accessToken' => string, 'expiresAt' => int]

    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $clientId,
        private bool $sslVerify = true,
        private ?LoggerInterface $logger = null
    ) {
        // Remove /api/v1 from baseUrl since paths already include it
        $cleanBaseUrl = rtrim($this->baseUrl, '/');
        if (str_ends_with($cleanBaseUrl, '/api/v1')) {
            $cleanBaseUrl = rtrim(substr($cleanBaseUrl, 0, -7), '/');
        }

        $this->http = new Client([
            'base_uri' => $cleanBaseUrl,
            'timeout' => 60,
            'connect_timeout' => 10,
            'verify' => $this->sslVerify,
            'http_errors' => false, // Don't throw exceptions on HTTP error codes
        ]);
    }

    public function ensureValidToken(): string
    {
        $now = time();
        if ($this->cachedToken && ($this->cachedToken['expiresAt'] ?? 0) > ($now + 30)) {
            return $this->cachedToken['accessToken'];
        }

        $response = $this->getToken();
        $data = $response->data ?? null;
        if (!$data || !isset($data['accessToken'])) {
            throw new \RuntimeException('Failed to retrieve token from LuckyCode API.');
        }

        $expiresIn = (int) ($data['expiresIn'] ?? 15 * 60);
        $this->cachedToken = [
            'accessToken' => (string) $data['accessToken'],
            'expiresAt' => $now + $expiresIn,
        ];

        return $this->cachedToken['accessToken'];
    }

    public function getTokenWithCredential(array $credential): ApiResponse
    {
        return $this->postJson('/api/v1/account/external-sign-in', $credential);
    }

    public function getToken(): ApiResponse
    {
        $credential = new CredentialModel(apiKey: $this->apiKey, clientId: $this->clientId);
        return $this->getTokenWithCredential([
            'apiKey' => $credential->apiKey,
            'clientId' => $credential->clientId,
        ]);
    }

    public function pullCode(PullCodeRequest $dto): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->postJson('/api/v1/lucky-code-adapter/pull', $this->toArray($dto), $token);
    }

    public function revealCode(RevealCodeRequest $dto): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->postJson('/api/v1/lucky-code-adapter/reveal', $this->toArray($dto), $token);
    }

    public function redeemCode(RedeemCodeRequest $dto): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->postJson('/api/v1/lucky-code-adapter/redeem', $this->toArray($dto), $token);
    }

    public function multiPull(PullCodeRequest $dto): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->postJson('/api/v1/lucky-code-adapter/multi-pull', $this->toArray($dto), $token);
    }

    public function checkSerialCode(string $serialCode): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->getJson('/api/v1/lucky-code-adapter/check-serialcode', ['serialCode' => $serialCode], $token);
    }

    public function getCustomersLog(CustomerPakageLogQuery $query): ApiResponse
    {
        $token = $this->ensureValidToken();
        return $this->getJson('/api/v1/lucky-code-adapter/customerpakage-log', [
            'page' => $query->page,
            'pageSize' => $query->pageSize,
            'customerRef' => $query->customerRef,
        ], $token);
    }

    private function postJson(string $path, array $payload, ?string $bearer = null): ApiResponse
    {
        try {
            $fullUrl = rtrim($this->baseUrl, '/') . $path;
            $this->logger?->info('LuckyCode API Request', [
                'url' => $fullUrl,
                'path' => $path,
                'baseUrl' => $this->baseUrl,
                'payload' => $payload
            ]);

            $res = $this->http->post($path, [
                'headers' => $this->headers($bearer),
                'json' => $payload,
            ]);

            $httpCode = $res->getStatusCode();
            $body = (string) $res->getBody();

            $this->logger?->info('LuckyCode API Response', [
                'url' => $fullUrl,
                'http_code' => $httpCode,
                'body' => $body
            ]);

            if ($httpCode >= 400) {
                return new ApiResponse(false, null, error: new \LuckyCode\IntegrationHelper\Support\ErrorDto('http_error', "HTTP {$httpCode}: {$body}"));
            }

            return $this->decodeResponse($body);
        } catch (GuzzleException $e) {
            $this->logger?->error('LuckyCode API Error', [
                'url' => rtrim($this->baseUrl, '/') . $path,
                'error' => $e->getMessage()
            ]);
            return new ApiResponse(false, null, error: new \LuckyCode\IntegrationHelper\Support\ErrorDto('http_error', $e->getMessage()));
        }
    }

    private function getJson(string $path, array $query = [], ?string $bearer = null): ApiResponse
    {
        try {
            $res = $this->http->get($path, [
                'headers' => $this->headers($bearer),
                'query' => $query,
            ]);
            return $this->decodeResponse((string) $res->getBody());
        } catch (GuzzleException $e) {
            return new ApiResponse(false, null, error: new \LuckyCode\IntegrationHelper\Support\ErrorDto('http_error', $e->getMessage()));
        }
    }

    private function headers(?string $bearer): array
    {
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        if ($bearer) {
            $headers['Authorization'] = 'Bearer '.$bearer;
        }
        return $headers;
    }

    private function decodeResponse(string $json): ApiResponse
    {
        $data = json_decode($json, true);
        $success = (bool) ($data['success'] ?? false);
        $error = null;
        if (isset($data['error'])) {
            $error = new \LuckyCode\IntegrationHelper\Support\ErrorDto(
                $data['error']['code'] ?? null,
                $data['error']['message']['errors'] ?? null
            );
        }
        return new ApiResponse(
            success: $success,
            data: $data['data'] ?? null,
            error: $error,
            sourceProvider: (string) ($data['source-provider'] ?? 'Likecard-Luckycode'),
            sourceProviderRef: (string) ($data['source_provider_ref'] ?? '01092024')
        );
    }

    private function toArray(object $dto): array
    {
        return json_decode(json_encode($dto, JSON_UNESCAPED_UNICODE), true) ?? [];
    }
}

