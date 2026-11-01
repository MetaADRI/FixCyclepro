<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_details', 'on_accept_google_response')) {
                $table->text('on_accept_google_response')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            if (Schema::hasColumn('booking_details', 'on_accept_google_response')) {
                $table->dropColumn('on_accept_google_response');
            }
        });
    }
};
