<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestColumnsToBookingRequestDriversTable extends Migration
{
    public function up()
    {
        Schema::table('booking_request_drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_request_drivers', 'coordinates_at_pickup')) {
                $table->string('coordinates_at_pickup')->nullable();
            }
            if (!Schema::hasColumn('booking_request_drivers', 'eta_at_pickup')) {
                $table->string('eta_at_pickup')->nullable();
            }
            if (!Schema::hasColumn('booking_request_drivers', 'timestamp_at_pickup')) {
                $table->timestamp('timestamp_at_pickup')->nullable();
            }
            if (!Schema::hasColumn('booking_request_drivers', 'laundry_outlet_order_id')) {
                $table->unsignedBigInteger('laundry_outlet_order_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('booking_request_drivers', function (Blueprint $table) {
            $table->dropColumn(['coordinates_at_pickup', 'eta_at_pickup', 'timestamp_at_pickup', 'laundry_outlet_order_id']);
        });
    }
}
