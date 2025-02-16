<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShortLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        ShortLink::factory()->count(10000)->create(['user_id' => $user->id]);
    }
}
