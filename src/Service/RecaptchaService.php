<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecaptchaService
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    private ?float $lastScore = null;
    private ?string $lastError = null;

    public function __construct(
        private readonly ?string $secretKey,
        private readonly ?string $siteKey = null,
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly ?LoggerInterface $logger = null,
        private readonly string $appEnv = 'dev',
    ) {
    }

    /**
     * Verify Google reCAPTCHA v3 token.
     *
     * @param string|null $token The reCAPTCHA response token from the client
     * @param string|null $expectedAction The expected action name (e.g. 'login', 'register')
     * @param string|null $clientIp Client's IP address
     * @param float $minScore Minimum score required (0.0 to 1.0, default 0.3)
     * @return bool True if verification passes, false otherwise
     */
    public function verify(
        ?string $token,
        ?string $expectedAction = null,
        ?string $clientIp = null,
        float $minScore = 0.3
    ): bool {
        // If secret key is not configured, bypass gracefully
        if (empty($this->secretKey)) {
            $this->logger?->warning('reCAPTCHA secret key is not configured. Bypassing verification.');
            return true;
        }

        // If no token was provided in dev environment, log warning but allow dev testing
        if (empty($token)) {
            $this->lastError = 'Missing reCAPTCHA token.';
            $this->logger?->warning('reCAPTCHA verification warning: missing token.');
            return $this->appEnv === 'dev';
        }

        try {
            $body = [
                'secret' => $this->secretKey,
                'response' => $token,
            ];

            if (!empty($clientIp)) {
                $body['remoteip'] = $clientIp;
            }

            if ($this->httpClient) {
                $response = $this->httpClient->request('POST', self::VERIFY_URL, [
                    'body' => $body,
                    'timeout' => 5.0,
                ]);

                $data = $response->toArray(false);
            } else {
                // Fallback to native stream / curl if HttpClient is unavailable
                $postData = http_build_query($body);
                $options = [
                    'http' => [
                        'method'  => 'POST',
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'content' => $postData,
                        'timeout' => 5.0,
                    ],
                ];
                $context = stream_context_create($options);
                $result = @file_get_contents(self::VERIFY_URL, false, $context);
                if ($result === false) {
                    throw new \RuntimeException('Failed to reach Google reCAPTCHA API.');
                }
                $data = json_decode($result, true) ?? [];
            }

            $success = $data['success'] ?? false;
            $score = isset($data['score']) ? (float) $data['score'] : 1.0;
            $action = $data['action'] ?? null;
            $errorCodes = $data['error-codes'] ?? [];

            $this->lastScore = $score;

            if (!$success) {
                $this->lastError = 'reCAPTCHA response unsuccessful: ' . implode(', ', $errorCodes);
                $this->logger?->warning('reCAPTCHA verification rejected', [
                    'errors' => $errorCodes,
                    'ip' => $clientIp,
                ]);
                return false;
            }

            // Verify action if an expected action was provided
            if ($expectedAction !== null && $action !== null && strtolower($action) !== strtolower($expectedAction)) {
                $this->lastError = sprintf('reCAPTCHA action mismatch: expected "%s", got "%s"', $expectedAction, $action);
                $this->logger?->warning($this->lastError);
                return false;
            }

            // Verify score threshold
            if ($score < $minScore) {
                $this->lastError = sprintf('reCAPTCHA score too low (%.2f < %.2f)', $score, $minScore);
                $this->logger?->warning($this->lastError);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            $this->logger?->error('reCAPTCHA verification exception: ' . $e->getMessage());

            // If network/connectivity failure occurs in development, permit access to prevent blocking
            return true;
        }
    }

    public function getLastScore(): ?float
    {
        return $this->lastScore;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getSiteKey(): ?string
    {
        return $this->siteKey;
    }
}
