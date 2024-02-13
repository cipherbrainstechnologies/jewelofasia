<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('loyalty_point')->default(0);
            $table->double('wallet_balance')->default(0);
            $table->string('referral_code')->nullable();
            $table->string('referred_by')->nullable();
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
            $table->dropColumn('loyalty_point');
            $table->dropColumn('wallet_balance');
            $table->dropColumn('referral_code');
            $table->dropColumn('referred_by');
        });
    }
}
