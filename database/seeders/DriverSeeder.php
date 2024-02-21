<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    /**
     * create sample data.
     *
     * @return array
     */
    private function createData(): array
    {
        $data = [];

        for ($i = 0; $i < 6; $i++) {
            $driver = [
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon:: now()->format('Y-m-d H:i:s'),
            ];

            array_push($data, $driver);
        }

        return $data;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = $this->createData();

        DB::table('drivers')->insert($drivers);
    }
}
