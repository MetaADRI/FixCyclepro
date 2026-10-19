<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapConfigurationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('map_configurations')) {
            Schema::create('map_configurations', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->string('api_slug', 80)->nullable();
                $table->unsignedTinyInteger('map_type')->default(1);
                $table->timestamps();
                $table->unique(['merchant_id', 'api_slug']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('map_configurations');
    }
}
