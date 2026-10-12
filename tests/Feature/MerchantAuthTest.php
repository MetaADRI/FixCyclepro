<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MerchantAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/merchant/admin/dashboard');

        $response->assertRedirect();
    }

    public function test_login_with_unknown_alias_does_not_error(): void
    {
        $response = $this->post('/merchant/admin/login/does-not-exist', [
            'email' => 'nobody@example.com',
            'password' => 'incorrect-password',
        ]);

        $this->assertLessThan(500, $response->getStatusCode());
    }
}
