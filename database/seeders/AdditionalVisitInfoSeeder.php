<?php

namespace Database\Seeders;

use App\Models\AdditionalVisitInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdditionalVisitInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdditionalVisitInfo::factory()->count(11000)->create();
    }
}
