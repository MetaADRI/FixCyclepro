<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('integration_configurations')) {
            Schema::create('integration_configurations', function (Blueprint $table) {
                $table->id();
                $table->integer('merchant_id')->nullable();
                $table->string('whatsapp_message_enable')->nullable();
                $table->string('whatsapp_message_enable_text')->nullable();
                $table->string('whatsapp_order_detail')->nullable();
                $table->string('whatsapp_order_detail_text')->nullable();
                $table->string('whatsapp_ride_detail')->nullable();
                $table->string('whatsapp_ride_detail_text')->nullable();
                $table->string('whatsapp_forgot_password')->nullable();
                $table->string('whatsapp_forgot_password_text')->nullable();
                $table->string('whatsapp_new_order_for_merchant')->nullable();
                $table->string('whatsapp_new_order_for_merchant_text')->nullable();
                $table->string('whatsapp_new_order_for_merchant_url')->nullable();
                $table->string('whatsapp_order_cancel_merchant')->nullable();
                $table->string('whatsapp_order_cancel_merchant_text')->nullable();
                $table->string('whatsapp_order_cancel_merchant_url')->nullable();
                $table->string('whatsapp_order_assign_driver')->nullable();
                $table->string('whatsapp_order_assign_driver_text')->nullable();
                $table->string('whatsapp_order_assign_driver_url')->nullable();
                $table->string('whatsapp_order_status')->nullable();
                $table->string('whatsapp_order_status_text')->nullable();
                $table->string('whatsapp_order_status_url')->nullable();
                $table->string('whatsapp_new_ride_for_driver')->nullable();
                $table->string('whatsapp_new_ride_for_driver_text')->nullable();
                $table->string('whatsapp_new_ride_for_driver_url')->nullable();
                $table->string('whatsapp_ride_accept_driver')->nullable();
                $table->string('whatsapp_ride_accept_driver_text')->nullable();
                $table->string('whatsapp_ride_accept_driver_url')->nullable();
                $table->string('whatsapp_ride_assign_driver')->nullable();
                $table->string('whatsapp_ride_assign_driver_text')->nullable();
                $table->string('whatsapp_ride_assign_driver_url')->nullable();
                $table->string('whatsapp_ride_status')->nullable();
                $table->string('whatsapp_ride_status_text')->nullable();
                $table->string('whatsapp_ride_status_url')->nullable();
                $table->string('whatsapp_ride_cancel_driver')->nullable();
                $table->string('whatsapp_ride_cancel_driver_text')->nullable();
                $table->string('whatsapp_ride_cancel_driver_url')->nullable();
                $table->string('whatsapp_ride_cancel_user')->nullable();
                $table->string('whatsapp_ride_cancel_user_text')->nullable();
                $table->string('whatsapp_ride_cancel_user_url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('integration_configurations');
    }
};
