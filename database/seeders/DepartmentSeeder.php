<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dept = [
            ['Dept_name' => 'Faculty', 'building_id' => '2', 'is_active' => '1'],
            ['Dept_name' => 'Procurement', 'building_id' => '2', 'is_active' => '1'],
        ];
        Department::insert($dept);
    }
}
