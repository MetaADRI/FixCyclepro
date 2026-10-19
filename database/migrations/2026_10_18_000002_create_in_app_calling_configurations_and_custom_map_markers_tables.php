<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInAppCallingConfigurationsAndCustomMapMarkersTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('in_app_calling_configurations')) {
            Schema::create('in_app_calling_configurations', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->nullable()->index();
                $table->unsignedInteger('in_app_calling_id')->nullable();
                $table->string('provider_slug', 60)->nullable();
                $table->string('api_key')->nullable();
                $table->string('api_secret')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('calling_number', 60)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('custom_map_markers')) {
            Schema::create('custom_map_markers', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->string('name')->nullable();
                $table->string('marker_image')->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('custom_map_markers');
        Schema::dropIfExists('in_app_calling_configurations');
    }
}
