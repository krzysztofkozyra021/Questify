<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ClassAttribute;
use Illuminate\Database\Seeder;

class ClassAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassAttribute::Factory(4)->create();
    }
}
