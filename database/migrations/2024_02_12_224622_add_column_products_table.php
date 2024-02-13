<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('paypal_product_id')->after('id')->nullable();
            $table->string('paypal_weekly_plan_id')->after('paypal_product_id')->nullable();
            $table->string('paypal_biweekly_plan_id')->after('paypal_weekly_plan_id')->nullable();
            $table->string('paypal_monthly_plan_id')->after('paypal_biweekly_plan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('paypal_product_id');
            $table->dropColumn('paypal_weekly_plan_id');
            $table->dropColumn('paypal_biweekly_plan_id');
            $table->dropColumn('paypal_monthly_plan_id');
        });
    }
};
