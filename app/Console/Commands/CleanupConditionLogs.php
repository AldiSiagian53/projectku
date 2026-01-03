<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConditionLog;

class CleanupConditionLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'condition:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus record condition_logs dengan waktu di bawah jam 21:00 agar data tidak terlalu banyak';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Hapus semua record yang waktu (jam-menit-detik) created_at < 21:00:00
        $deleted = ConditionLog::whereTime('created_at', '<', '21:00:00')->delete();

        $this->info("Berhasil menghapus {$deleted} record condition_logs dengan waktu < 21:00.");

        return Command::SUCCESS;
    }
}


