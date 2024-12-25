<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->insert([
            ['project_id' => 1, 'name' => 'Design geospatial map', 'priority' => 1, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 1, 'name' => 'Integrate geospatial map', 'priority' => 2, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 1, 'name' => 'Test geospatial integration', 'priority' => 3, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 1, 'name' => 'Deploy geospatial map to stage', 'priority' => 4, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 2, 'name' => 'Review web app current seo', 'priority' => 1, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 2, 'name' => 'Highlight seo gaps', 'priority' => 2, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_id' => 2, 'name' => 'Build seo impl. plan', 'priority' => 3, 'active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
