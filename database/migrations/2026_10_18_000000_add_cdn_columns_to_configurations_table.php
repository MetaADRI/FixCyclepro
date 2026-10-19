<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCdnColumnsToConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'working_with_cdn')) {
                $table->unsignedTinyInteger('working_with_cdn')->default(0);
            }
            if (!Schema::hasColumn('configurations', 'cloudflare_cdn_url')) {
                $table->string('cloudflare_cdn_url', 500)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['working_with_cdn', 'cloudflare_cdn_url']);
        });
    }
}
