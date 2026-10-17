<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnDataAndCreditOptionToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'return_data')) {
                $table->text('return_data')->nullable();
            }
            if (!Schema::hasColumn('users', 'credit_option_enable')) {
                $table->unsignedTinyInteger('credit_option_enable')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['return_data', 'credit_option_enable']);
        });
    }
}
