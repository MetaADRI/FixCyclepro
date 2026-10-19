<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonTypeForToCancelReasonsTable extends Migration
{
    public function up()
    {
        Schema::table('cancel_reasons', function (Blueprint $table) {
            if (!Schema::hasColumn('cancel_reasons', 'reason_type_for')) {
                $table->unsignedTinyInteger('reason_type_for')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('cancel_reasons', function (Blueprint $table) {
            $table->dropColumn('reason_type_for');
        });
    }
}
