<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebRoutesSmokeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Phase 3 customer-PWA web routes. Each must render (200) or redirect (302),
     * never a server error (500).
     *
     * @return array<string, array{0: string}>
     */
    public static function phase3WebRoutes(): array
    {
        return [
            'landing' => ['/'],
            'login' => ['/login'],
            'signup' => ['/signup'],
            'otp' => ['/otp'],
            'app-home' => ['/app'],
            'booking-new' => ['/app/booking/new'],
            'booking-active' => ['/app/booking/active'],
            'booking-active-with' => ['/app/booking/active/1'],
            'bookings' => ['/app/bookings'],
            'booking-detail' => ['/app/bookings/1'],
            'wallet' => ['/app/wallet'],
            'profile' => ['/app/profile'],
            'support' => ['/app/support'],
            'support-page' => ['/app/support/faq'],
            'offline' => ['/offline'],
        ];
    }

    /**
     * @dataProvider phase3WebRoutes
     */
    public function test_phase3_web_route_does_not_error(string $path): void
    {
        $response = $this->get($path);
        $status = $response->getStatusCode();

        $this->assertLessThan(500, $status, "GET {$path} returned {$status}");
        $this->assertContains($status, [200, 302], "GET {$path} returned {$status}");
    }
}
