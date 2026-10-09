<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Appends the business_segments.membership_plan_id foreign key.
 *
 * The FK originally lived in 2019_11_01_130536_create_business_segments_table,
 * but that starts before merchant_membership_plans exists (created by
 * 2025_01_20_100000_create_merchant_membership_plans_table). PostgreSQL
 * requires the referenced table to exist when the constraint is created, so
 * the FK was stripped from the original migration and is re-added here, after
 * every migration has run.
 *
 * Legacy MySQL databases already created the constraint from the original
 * migration, so it is only created when it does not already exist.
 */
class AddMembershipPlanForeignKeyToBusinessSegmentsTable extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('business_segments')
            && Schema::hasTable('merchant_membership_plans')
            && !$this->foreignKeyExists('business_segments', 'business_segments_membership_plan_id_foreign')
        ) {
            Schema::table('business_segments', function (Blueprint $table) {
                $table->foreign('membership_plan_id')
                    ->references('id')->on('merchant_membership_plans')
                    ->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
        }
    }

    public function down()
    {
        if ($this->foreignKeyExists('business_segments', 'business_segments_membership_plan_id_foreign')) {
            Schema::table('business_segments', function (Blueprint $table) {
                $table->dropForeign('business_segments_membership_plan_id_foreign');
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $conn = DB::connection();

        if ($conn->getDriverName() === 'pgsql') {
            return (bool) $conn->selectOne(
                'SELECT 1 FROM pg_constraint WHERE conname = ? AND conrelid = ?::regclass',
                [$constraint, $table]
            );
        }

        if ($conn->getDriverName() === 'mysql') {
            return (bool) $conn->selectOne(
                'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ? AND TABLE_NAME = ?',
                [$constraint, $table]
            );
        }

        return false;
    }
}