<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $building = [
            ['Building_name' => 'Main Campus', 'Address' => 'Rizal Corner Elizano Streets, Legaszpi City', 'is_active' => '1'],
            ['Building_name' => 'Main Office', 'Address' => '2nd Floor King`s Commercial Bldg.Peñaranda St. Legazpi City', 'is_active' => '1'],
            ['Building_name' => 'Forbes Academy', 'Address' => 'Lakandula Drive, Legazpi City', 'is_active' => '1']
        ];
        Building::insert($building);
    }
}
