<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkingWithRedisToApplicationConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('application_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('application_configurations', 'working_with_redis')) {
                $table->smallInteger('working_with_redis')->nullable()->default(2);
            }
        });
    }

    public function down()
    {
        Schema::table('application_configurations', function (Blueprint $table) {
            $table->dropColumn('working_with_redis');
        });
    }
}
