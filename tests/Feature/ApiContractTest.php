<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApiContractTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Android-facing endpoints whose JSON envelope ({version, result, message, data})
     * must stay stable across the port.
     *
     * @return array<string, array{0: string}>
     */
    public static function userApiEndpoints(): array
    {
        return [
            'login' => ['/api/user/login'],
            'signup' => ['/api/user/signup'],
            'configuration' => ['/api/user/configuration'],
            'home-screen' => ['/api/user/website/home-screen'],
            'cars' => ['/api/user/cars'],
        ];
    }

    /**
     * @dataProvider userApiEndpoints
     */
    public function test_user_endpoint_returns_json_envelope(string $path): void
    {
        $response = $this->postJson($path, []);

        $response->assertJsonStructure(['result', 'message']);
        $this->assertStringContainsString(
            'application/json',
            (string) $response->headers->get('Content-Type'),
            "POST {$path} did not return JSON"
        );
    }
}
