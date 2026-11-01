<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'calling_button')) {
                $table->tinyInteger('calling_button')->nullable()->default(1);
            }
            if (!Schema::hasColumn('drivers', 'tracking_freeze_enable')) {
                $table->tinyInteger('tracking_freeze_enable')->nullable()->default(0);
            }
            if (!Schema::hasColumn('drivers', 'kin_details')) {
                $table->text('kin_details')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'calling_button')) {
                $table->dropColumn('calling_button');
            }
            if (Schema::hasColumn('drivers', 'tracking_freeze_enable')) {
                $table->dropColumn('tracking_freeze_enable');
            }
            if (Schema::hasColumn('drivers', 'kin_details')) {
                $table->dropColumn('kin_details');
            }
        });
    }
};
