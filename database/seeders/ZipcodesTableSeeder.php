<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ZipcodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            \DB::table('zipcodes')->insert([
                'city_id' => rand(1, 10), // Assuming city_id is the foreign key
                'zipcode' => $faker->postcode,
                // Add other columns if necessary
            ]);
        }
    }
    
}
