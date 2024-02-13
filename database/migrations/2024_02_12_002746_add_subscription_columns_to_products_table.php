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
            $table->boolean('subscription_status')->default(false)->after('status');
            $table->enum('subscription_type', ['weekly', 'biweekly', 'monthly'])->nullable()->after('subscription_status')
            ->comment('Subscription type: weekly, biweekly, or monthly');
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
            $table->dropColumn(['subscription_status', 'subscription_type']);
        });
    }
};
