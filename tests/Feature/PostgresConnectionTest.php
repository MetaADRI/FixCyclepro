<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostgresConnectionTest extends TestCase
{
    public function test_test_suite_runs_against_postgres(): void
    {
        $this->assertSame('pgsql', DB::connection()->getDriverName());
    }
}
