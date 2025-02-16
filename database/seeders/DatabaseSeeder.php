<?php

namespace Database\Seeders;

use App\Models\AdditionalVisitInfo;
use App\Models\ShortLink;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ShortLinkSeeder::class);
        $this->call(VisitUserDataSeeder::class);
        $this->call(AdditionalVisitInfoSeeder::class);
    }
}
