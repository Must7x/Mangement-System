<?php

namespace Database\Seeders;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['name' => 'HP Omen 15', 'type' => 'laptop', 'serial_number' => 'SN-HP-001', 'status' => AssetStatus::Warehouse],
            ['name' => 'MacBook Pro 14', 'type' => 'laptop', 'serial_number' => 'SN-MBP-002', 'status' => AssetStatus::Warehouse],
            ['name' => 'HP LaserJet Pro', 'type' => 'printer', 'serial_number' => 'SN-PRT-003', 'status' => AssetStatus::Maintenance],
            ['name' => 'Dell OptiPlex', 'type' => 'desktop', 'serial_number' => 'SN-DELL-004', 'status' => AssetStatus::Warehouse],
            ['name' => 'طاولة اجتماعات', 'type' => 'furniture', 'serial_number' => 'SN-FRN-005', 'status' => AssetStatus::Warehouse],
        ];

        foreach ($samples as $sample) {
            Asset::updateOrCreate(
                ['serial_number' => $sample['serial_number']],
                $sample
            );
        }
    }
}
