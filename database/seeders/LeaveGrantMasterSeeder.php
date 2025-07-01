<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveGrantMaster;

class LeaveGrantMasterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['months' => 6, 'days' => 10],
            ['months' => 18, 'days' => 11],
            ['months' => 30, 'days' => 12],
            ['months' => 42, 'days' => 14],
            ['months' => 54, 'days' => 16],
            ['months' => 66, 'days' => 18],
            ['months' => 78, 'days' => 20],
        ];

        foreach ($data as $entry) {
            LeaveGrantMaster::create($entry);
        }
    }
}
