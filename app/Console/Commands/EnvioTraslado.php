<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdenesExport;
use App\Services\GenerateLogs;
use App\Mail\ExcelMail;
use App\Mail\MailOrdenes;
use Carbon\Carbon; 

class EnvioTraslado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:envio-ordenes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para enviar las ordenes creadas semanalmente (cada lunes con datos de la semana anterior)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $error = 0;
        $mensaje_error = '';
        $fecha_inicio_log = Date('Y-m-d\TH:i:s');
        $valores_actualizados = 0;
        $valores_creados = 0;
        $cantidad_busqueda = 0;

        try {
            $hoy = Carbon::today();

            if ($hoy->dayOfWeek == Carbon::MONDAY) {
                $finSemanaAnterior = $hoy->copy()->subDay();
                $inicioSemanaAnterior = $finSemanaAnterior->copy()->startOfWeek();

                $fechaInicio = $inicioSemanaAnterior->format('Y-m-d');
                $fechaFin = $finSemanaAnterior->format('Y-m-d');

                $nombreArchivo = 'ordenes_traslado_' . $fechaInicio . '_al_' . $fechaFin . '.xlsx';

                $export = new OrdenesExport($fechaInicio, $fechaFin);
                Excel::store($export, $nombreArchivo, 'public');

                $adjunto = storage_path("app/public/{$nombreArchivo}");

                Mail::send('exports.ordenes',
                    ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin],
                    function ($message) use ($nombreArchivo, $adjunto, $fechaInicio, $fechaFin) {
                        // $message->to(['wilmar.guerrero@farmalisto.com.co'])
                        $message->to(['fernando.castaneda@farmalisto.com.co', 'Coordinador.comercial@farmalisto.com.co'])
                            ->subject('Reporte semanal de órdenes: ' . $fechaInicio . ' al ' . $fechaFin)
                            ->attach($adjunto);
                    }
                );

                $valores_creados = 1;
            }
        } catch (\Throwable $th) {
            $error = 1;
            $mensaje_error = $th->getMessage();
        }

        GenerateLogs::generateLogScheduleTask("ENVIO ORDENES SEMANAL", "ordenes_traslados", $error, $cantidad_busqueda, $valores_creados, $valores_actualizados, $fecha_inicio_log, $mensaje_error);
    }
}