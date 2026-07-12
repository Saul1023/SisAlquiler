<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User administrador
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
            ]
        );




    }
}
