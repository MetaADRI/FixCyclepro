<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapAndEtaColumnsToBookingConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('booking_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_configurations', 'map_box_key')) {
                $table->string('map_box_key')->nullable();
            }
            if (!Schema::hasColumn('booking_configurations', 'here_map_key')) {
                $table->string('here_map_key')->nullable();
            }
            if (!Schema::hasColumn('booking_configurations', 'eta_pickup_and_dest')) {
                $table->integer('eta_pickup_and_dest')->nullable();
            }
            if (!Schema::hasColumn('booking_configurations', 'eta_calculation_method')) {
                $table->integer('eta_calculation_method')->nullable();
            }
        });

        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'map_box_autocomplete_enable')) {
                $table->integer('map_box_autocomplete_enable')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('booking_configurations', function (Blueprint $table) {
            $table->dropColumn(['map_box_key', 'here_map_key', 'eta_pickup_and_dest', 'eta_calculation_method']);
        });

        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('map_box_autocomplete_enable');
        });
    }
}
