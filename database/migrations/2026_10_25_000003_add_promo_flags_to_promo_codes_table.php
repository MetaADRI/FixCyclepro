<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoFlagsToPromoCodesTable extends Migration
{
    public function up()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('promo_codes', 'is_default_promo_code')) {
                $table->smallInteger('is_default_promo_code')->nullable()->default(2);
            }
            if (!Schema::hasColumn('promo_codes', 'to_show_in_app')) {
                $table->smallInteger('to_show_in_app')->nullable()->default(1);
            }
        });
    }

    public function down()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['is_default_promo_code', 'to_show_in_app']);
        });
    }
}
