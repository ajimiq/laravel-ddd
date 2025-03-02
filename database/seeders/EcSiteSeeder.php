<?php

namespace Database\Seeders;

use App\Models\EcSite;
use Illuminate\Database\Seeder;

class EcSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EcSite::create([
            'code' => 'TEST-MALL',
            'name' => 'テストモール',
            'description' => 'テスト用のECサイトです。フィットネス用品を取り扱っています。',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 