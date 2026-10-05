<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\LogScheduleTask;
use App\Services\GenerateLogs;
use Illuminate\Console\Command;
use Log;

class CleanNetsuiteLogsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extraccion:clean-netsuite-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta la limpieza de la tabla log_schedule_tags para evitar que crezca demasiado y afecte el rendimiento del sistema.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            
            $logsNetsuite = LogScheduleTask::count();
            if ($logsNetsuite > 0) {
                LogScheduleTask::truncate();
                (new Helper)->storeAuditLogs("Truncar Tabla", "LogScheduleTask", 0, "Se trunca tabla log_schedule_tags", '{}', "info");
            } else {
                (new Helper)->storeAuditLogs("Truncar Tabla", "LogScheduleTask", 0, "No hubo nada que truncar en la tabla log_schedule_tags", '{}', "info");
            }

        } catch (\Throwable $th) {
            Log::error("Error al truncar tabla log_schedule_tags: " . $th->getMessage());
            (new Helper)->storeAuditLogs("Truncar Tabla", "LogScheduleTask", 0, "Error al truncar la tabla log_schedule_tags", $th->getMessage(), "error");
        }
    }
}
