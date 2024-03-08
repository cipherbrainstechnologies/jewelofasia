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
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->foreignId('city_id')->constrained('cities')->after('time_slot_id');
            $table->foreignId('zipcode_id')->constrained('zipcodes');
            $table->timestamp('delivery_date')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            // Drop the foreign key constraints
            
            $table->dropForeign(['city_id']);
            $table->dropForeign(['zipcode_id']);

            // Drop the columns
            $table->dropColumn('delivery_date');
            $table->dropColumn('city_id');
            $table->dropColumn('zipcode_id');
        });
    }
};
