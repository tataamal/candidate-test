<?php

namespace Database\Seeders;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class LayupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppierData = Supplier::first();
        Layup::updateOrCreate(
            ['id' => 1],
            [
                'supplier_id' => $suppierData->id,
                'name' => 'Sample Layup 1',
            ]
        );
    }
}
