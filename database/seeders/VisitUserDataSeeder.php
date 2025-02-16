<?php

namespace Database\Seeders;

use App\Models\VisitUserData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VisitUserData::factory()->count(10000)->create();
    }
}
