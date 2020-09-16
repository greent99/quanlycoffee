<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category')->insert([
            'name' => 'none',
        ]);

        DB::table('category')->insert([
            'name' => 'Đồ uống',
        ]);

        DB::table('category')->insert([
            'name' => 'Thức ăn',
        ]);
    }
}
