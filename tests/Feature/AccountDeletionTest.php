<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use DatabaseTransactions;

    private function signupUser(string $email, string $password): array
    {
        $phone = '+26097' . random_int(100000, 999999);
        return $this->postJson('/api/user/signup', [
            'merchant_id' => 2,
            'access_pin' => '568938',
            'first_name' => 'Delete',
            'last_name' => 'Test',
            'country_id' => 2,
            'country_code' => 'ZM',
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'requested_from' => 'web',
        ])->json();
    }

    /**
     * The Passport guard caches the resolved user on the auth manager
     * singleton, which persists across in-process test requests. Real HTTP
     * requests always run in a fresh process, so drop the cached guard
     * before making an authenticated call.
     */
    private function resetAuth(): void
    {
        $this->app['auth']->forgetGuards();
    }

    private function loginPayload(string $email, string $password): array
    {
        return [
            'merchant_id' => 2,
            'access_pin' => '568938',
            'phone' => $email,
            'email' => $email,
            'password' => $password,
            'logintype' => 'EMAIL',
            'login_type' => 'EMAIL',
            'requested_from' => 'web',
        ];
    }

    public function test_delete_request_requires_password(): void
    {
        $signup = $this->signupUser('del.nopass.' . random_int(100000, 999999) . '@example.com', 'Secret!123');
        $this->assertSame('1', (string) $signup['result']);

        $token = $signup['data']['access_token'];

        $response = $this->postJson('/api/user/account/delete-request', [
            'merchant_id' => 2,
            'access_pin' => '568938',
        ], ['Authorization' => 'Bearer ' . $token]);

        $response->assertJsonStructure(['result', 'message']);
        $this->assertSame('0', (string) $response->json('result'));    }

    public function test_delete_request_schedules_deletion_and_login_cancels_it(): void
    {
        $email = 'del.grace.' . random_int(100000, 999999) . '@example.com';
        $password = 'Secret!123';
        $signup = $this->signupUser($email, $password);
        $this->assertSame('1', (string) $signup['result']);
        $token = $signup['data']['access_token'];
        $userId = User::where('email', $email)->value('id');
        $this->assertNotNull($userId);

        /* wrong password is rejected and does not schedule anything */
        $this->resetAuth();
        $wrong = $this->postJson('/api/user/account/delete-request', [
            'merchant_id' => 2,
            'access_pin' => '568938',
            'password' => 'WrongPass!1',
            'account_cancel_reason' => 'testing',
        ], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame('0', (string) $wrong->json('result'));
        $this->assertDatabaseHas('users', ['id' => $userId, 'user_delete' => null]);

        /* correct password schedules deletion after the grace window */
        $this->resetAuth();
        $ok = $this->postJson('/api/user/account/delete-request', [
            'merchant_id' => 2,
            'access_pin' => '568938',
            'password' => $password,
            'account_cancel_reason' => 'testing',
        ], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame('1', (string) $ok->json('result'));
        $this->assertNotEmpty($ok->json('data.deletion_date'));

        $user = User::find($userId);
        $this->assertSame(1, (int) $user->user_delete);
        $this->assertNotNull($user->deletion_scheduled_at);
        $this->assertSame('testing', $user->account_cancel_reason);

        /* the old token is revoked */
        $this->resetAuth();
        $details = $this->postJson('/api/user/details', [
            'merchant_id' => 2,
            'access_pin' => '568938',
        ], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame('999', (string) $details->json('result'));

        /* wrong password during grace keeps the deletion scheduled */
        $wrongLogin = $this->postJson('/api/user/login', $this->loginPayload($email, 'WrongPass!1'));
        $this->assertSame('0', (string) $wrongLogin->json('result'));
        $this->assertSame(1, (int) User::find($userId)->user_delete);

        /* correct password during grace cancels the deletion */
        $login = $this->postJson('/api/user/login', $this->loginPayload($email, $password));
        $this->assertSame('1', (string) $login->json('result'));
        $this->assertTrue((bool) $login->json('data.account_deletion_cancelled'));

        $user = User::find($userId);
        $this->assertNull($user->user_delete);
        $this->assertNull($user->deletion_scheduled_at);
        $this->assertNull($user->account_cancel_reason);
    }

    public function test_purge_command_only_removes_accounts_past_grace(): void
    {
        $pastEmail = 'del.past.' . random_int(100000, 999999) . '@example.com';
        $keptEmail = 'del.keep.' . random_int(100000, 999999) . '@example.com';
        $password = 'Secret!123';

        foreach ([$pastEmail => 31, $keptEmail => 5] as $email => $daysAgo) {
            $signup = $this->signupUser($email, $password);
            $this->assertSame('1', (string) $signup['result']);
            $userId = User::where('email', $email)->value('id');
            $this->assertNotNull($userId);

            $this->resetAuth();
            $delete = $this->postJson('/api/user/account/delete-request', [
                'merchant_id' => 2,
                'access_pin' => '568938',
                'password' => $password,
            ], ['Authorization' => 'Bearer ' . $signup['data']['access_token']]);
            $this->assertSame('1', (string) $delete->json('result'), (string) $delete->json('message'));
            $this->assertSame(1, (int) User::find($userId)->user_delete);

            /* backdate the schedule to simulate elapsed grace time */
            DB::table('users')
                ->where('id', $userId)
                ->update(['deletion_scheduled_at' => Carbon::now()->subDays($daysAgo)]);
        }

        $this->artisan('accounts:purge-deleted');

        $this->assertDatabaseMissing('users', ['email' => $pastEmail]);
        $this->assertDatabaseHas('users', ['email' => $keptEmail, 'user_delete' => 1]);
    }
}
