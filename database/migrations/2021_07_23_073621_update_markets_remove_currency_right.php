<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketsRemoveCurrencyRight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn('currency_right');
        });
    }

    public function down()
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->integer('currency_right')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
}
