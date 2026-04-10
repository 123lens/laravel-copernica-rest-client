<?php

namespace Budgetlens\Copernica\RestClient\Http;

class JSONWebToken
{
    private array $payload;
    private string $raw;

    public function __construct(string $data)
    {
        $parts = explode('.', trim($data));

        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid JWT structure');
        }

        $payload = json_decode(self::base64UrlDecode($parts[1]), true);

        if (!is_array($payload) || !isset($payload['exp'])) {
            throw new \InvalidArgumentException('Invalid JWT payload');
        }

        $this->payload = $payload;
        $this->raw = trim($data);
    }

    public static function fromServer(string $accessToken, string $authUrl): ?self
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($authUrl, [
                'form_params' => ['access_token' => $accessToken],
            ]);

            return new self($response->getBody()->getContents());
        } catch (\Throwable) {
            return null;
        }
    }

    public function expired(): bool
    {
        return time() >= $this->payload['exp'];
    }

    public function raw(): string
    {
        return $this->raw;
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
