<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountDeletionGraceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('deletion_scheduled_at')->nullable();
            $table->text('account_cancel_reason')->nullable();
            $table->unsignedBigInteger('cancel_reason_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deletion_scheduled_at', 'account_cancel_reason', 'cancel_reason_id']);
        });
    }
}
