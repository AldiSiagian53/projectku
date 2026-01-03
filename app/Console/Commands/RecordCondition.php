<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorData;
use App\Models\ConditionLog;

class RecordCondition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'condition:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mencatat kondisi sistem (NORMAL/WARMING/WARNING/DANGER/OTHER) dari data sensor terbaru setiap 10 menit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $latest = SensorData::latest()->first();

        if (!$latest) {
            $this->info('Tidak ada data SensorData untuk dicatat.');
            return Command::SUCCESS;
        }

        // Tentukan status berdasarkan suhu (sinkron dengan logika di Arduino)
        $temp = $latest->temperature;
        $status = 'OTHER';

        if ($temp === null) {
            $status = 'OTHER';
        } elseif ($temp >= 65.0) {
            $status = 'DANGER';
        } elseif ($temp >= 50.0) {
            $status = 'WARNING';
        } elseif ($temp >= 35.0) {
            $status = 'WARMING';
        } else {
            $status = 'NORMAL';
        }

        ConditionLog::create([
            'status'          => $status,
            'temperature'     => $latest->temperature,
            'bat_v'           => $latest->bat_v,
            'panel_v'         => $latest->panel_v,
            'panel_power'     => $latest->panel_power,
            'charging_power'  => $latest->charging_power,
            'bat_percent'     => $latest->bat_percent,
            'bat_wh'          => $latest->bat_wh,
        ]);

        $this->info("Kondisi tercatat sebagai {$status}.");

        return Command::SUCCESS;
    }
}


