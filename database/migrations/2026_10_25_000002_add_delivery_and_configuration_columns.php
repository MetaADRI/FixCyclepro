<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryAndConfigurationColumns extends Migration
{
    public function up()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_types', 'is_gallery_image_upload')) {
                $table->smallInteger('is_gallery_image_upload')->nullable()->default(2);
            }
            if (!Schema::hasColumn('vehicle_types', 'volumetric_capacity')) {
                $table->decimal('volumetric_capacity', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('vehicle_types', 'package_weight_range')) {
                $table->string('package_weight_range')->nullable();
            }
            if (!Schema::hasColumn('vehicle_types', 'engine_type')) {
                $table->integer('engine_type')->nullable();
            }
        });

        Schema::table('booking_configurations', function (Blueprint $table) {
            $columns = [
                'volumetric_capacity_calculation' => ['decimal', [10, 2]],
                'location_update_timeband' => ['integer', []],
                'driver_eta_for_ride_request' => ['integer', []],
                'currency_exchange_key' => ['string', []],
                'currency_exchange_data' => ['text', []],
                'driver_ride_request_business_segment' => ['integer', []],
                'max_hours_for_online_driver' => ['integer', []],
                'min_rest_hours_for_driver' => ['integer', []],
                'sharable_link_expire_time_after_end_ride' => ['integer', []],
                'search_place_radius' => ['integer', []],
                'sos_driver_count' => ['integer', []],
                'sos_driver_radius' => ['integer', []],
                'upcoming_notification_time' => ['integer', []],
            ];
            foreach ($columns as $name => $definition) {
                if (Schema::hasColumn('booking_configurations', $name)) {
                    continue;
                }
                [$type, $args] = $definition;
                if ($type === 'decimal') {
                    $table->decimal($name, $args[0], $args[1])->nullable();
                } else {
                    $table->{$type}($name)->nullable();
                }
            }
        });
    }

    public function down()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropColumn(['is_gallery_image_upload', 'volumetric_capacity', 'package_weight_range', 'engine_type']);
        });

        Schema::table('booking_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'volumetric_capacity_calculation',
                'location_update_timeband',
                'driver_eta_for_ride_request',
                'currency_exchange_key',
                'currency_exchange_data',
                'driver_ride_request_business_segment',
                'max_hours_for_online_driver',
                'min_rest_hours_for_driver',
                'sharable_link_expire_time_after_end_ride',
                'search_place_radius',
                'sos_driver_count',
                'sos_driver_radius',
                'upcoming_notification_time',
            ]);
        });
    }
}
