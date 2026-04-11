<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;

class supplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'sample_admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role' => 'supplier'
            ]
        );

        $SampleUser = User::where('email', 'sample_admin@gmail.com')->first();

        Supplier::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Sample Supplier 1',
                'user_id' => $SampleUser->id
            ]
        );
    }
}
