<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use LogicException;

class StandardPlansSeeder extends Seeder
{
    public function run(): void
    {
        $connection = config('database.default');
        $databaseConfig = config("database.connections.{$connection}");
        $isLocalDatabase = $connection === 'sqlite'
            || in_array($databaseConfig['host'] ?? null, ['localhost', '127.0.0.1', '::1'], true);

        if (! app()->environment(['local', 'testing']) || ! $isLocalDatabase) {
            throw new LogicException('Standard plans can only be seeded in local or testing environments.');
        }

        $plans = [
            [
                'name' => 'STARTER',
                'slug' => 'starter',
                'price' => 10000,
                'max_employees' => 10,
            ],
            [
                'name' => 'GROWTH',
                'slug' => 'growth',
                'price' => 25000,
                'max_employees' => 30,
            ],
            [
                'name' => 'BUSINESS',
                'slug' => 'business',
                'price' => 50000,
                'max_employees' => 80,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['slug' => $plan['slug']],
                [
                    ...$plan,
                    'currency' => 'XOF',
                    'dashboard_type' => 'Complet',
                    'history_duration' => 'Illimitée',
                    'support_type' => 'Email',
                    'is_active' => true,
                    'is_visible' => true,
                ]
            );
        }
    }
}
