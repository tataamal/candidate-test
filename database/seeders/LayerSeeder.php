<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layups;
use App\Models\Layers;

class LayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layupData = Layups::first();
        Layers::updateOrCreate(
            ['id' => 1],
            [
                'layup_id' => $layupData->id,
                'layer_order' => '0000001',
                'thickness' => 10.5,
                'width' => 100.0,
                'angle' => 45.0
            ]
        );
    }
}
