<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresentationColumnsToMerchantSegmentTable extends Migration
{
    public function up()
    {
        Schema::table('merchant_segment', function (Blueprint $table) {
            if (!Schema::hasColumn('merchant_segment', 'dynamic_url')) {
                $table->string('dynamic_url')->nullable();
            }
            if (!Schema::hasColumn('merchant_segment', 'segment_background_gradient_1')) {
                $table->string('segment_background_gradient_1')->nullable();
            }
            if (!Schema::hasColumn('merchant_segment', 'segment_background_gradient_2')) {
                $table->string('segment_background_gradient_2')->nullable();
            }
            if (!Schema::hasColumn('merchant_segment', 'segment_home_screen_image')) {
                $table->string('segment_home_screen_image')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('merchant_segment', function (Blueprint $table) {
            $table->dropColumn([
                'dynamic_url',
                'segment_background_gradient_1',
                'segment_background_gradient_2',
                'segment_home_screen_image',
            ]);
        });
    }
}
