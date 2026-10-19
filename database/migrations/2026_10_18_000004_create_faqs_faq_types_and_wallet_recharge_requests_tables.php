<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsFaqTypesAndWalletRechargeRequestsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('faq_types')) {
            Schema::create('faq_types', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->unsignedInteger('faq_type_id')->nullable()->index();
                $table->unsignedTinyInteger('application')->default(1);
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('language_faq_types')) {
            Schema::create('language_faq_types', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->string('locale', 10)->nullable();
                $table->unsignedInteger('faq_type_id');
                $table->string('title', 500)->nullable();
                $table->timestamps();
                $table->unique(['merchant_id', 'locale', 'faq_type_id'], 'lang_faq_type_unique');
            });
        }

        if (!Schema::hasTable('language_faqs')) {
            Schema::create('language_faqs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->string('locale', 10)->nullable();
                $table->unsignedInteger('faq_id');
                $table->text('question')->nullable();
                $table->text('answer')->nullable();
                $table->timestamps();
                $table->unique(['merchant_id', 'locale', 'faq_id'], 'lang_faq_unique');
            });
        }

        if (!Schema::hasTable('wallet_recharge_requests')) {
            Schema::create('wallet_recharge_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->unsignedInteger('driver_id')->nullable()->index();
                $table->decimal('amount_requested', 12, 2)->default(0);
                $table->text('comment')->nullable();
                $table->unsignedTinyInteger('request_status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('wallet_recharge_requests');
        Schema::dropIfExists('language_faqs');
        Schema::dropIfExists('language_faq_types');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_types');
    }
}
