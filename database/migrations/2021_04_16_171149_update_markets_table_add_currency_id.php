<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketsTableAddCurrencyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->integer('currency_right')->default(0);
            $table->integer('currency_id')->unsigned()->nullable()->default(1);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null')->onUpdate('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn('currency_right');
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
}



