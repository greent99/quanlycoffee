<?php

use Illuminate\Database\Seeder;

class GroupCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groupcategory')->insert([
            'name' => 'none',
            'category_id' => '1'
        ]);
    }
}
