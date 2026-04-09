<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Layups;

class LayupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppierData = Supplier::first();
        Layups::updateOrCreate(
            ['id' => 1],
            [
                'supplier_id' => $suppierData->id,
                'name' => 'Sample Layup 1'
            ]
        );
    }
}
