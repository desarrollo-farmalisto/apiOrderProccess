<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdenesExport;
use App\Models\LogScheduleTask;
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
        try {
            // Verificar si hoy es lunes
            $hoy = Carbon::today();
            
            // Si quieres que se ejecute solo los lunes (día 1 de la semana en Carbon)
            if ($hoy->dayOfWeek == Carbon::MONDAY)  { 
                // Calcular fechas de la semana anterior (desde el lunes hasta el domingo)
                $finSemanaAnterior = $hoy->copy()->subDay(); // Domingo (ayer)
                $inicioSemanaAnterior = $finSemanaAnterior->copy()->startOfWeek(); // Lunes de la semana anterior
                
                $fechaInicio = $inicioSemanaAnterior->format('Y-m-d');
                $fechaFin = $finSemanaAnterior->format('Y-m-d');
                
                // Nombre del archivo con fecha
                $nombreArchivo = 'ordenes_traslado_' . $fechaInicio . '_al_' . $fechaFin . '.xlsx';
                
                // Modificar la clase OrdenesExport para que acepte rango de fechas
                $export = new OrdenesExport($fechaInicio, $fechaFin);
                
                // Guardar el archivo
                Excel::store($export, $nombreArchivo, 'public');
                
                $adjunto = storage_path("app/public/{$nombreArchivo}");
                
                // Enviar el correo
                Mail::send('exports.ordenes', 
                    ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin], 
                    function ($message) use ($nombreArchivo, $adjunto, $fechaInicio, $fechaFin) {
                        $message->to(['auxiliar.desarrollo@axa.com.co', 'katherine.rincon@axa.com.co', 'asistente2.abastecimiento@axa.com.co',
                        'alvaro@axa.com.co', 'jorge.pinto@axa.com.co'])
                            ->subject('Reporte semanal de órdenes: ' . $fechaInicio . ' al ' . $fechaFin)
                            ->attach($adjunto);
                    }
                );
                
                $this->info('Reporte semanal de órdenes enviado exitosamente');
                
                // Registrar en log
                LogScheduleTask::create([
                    'task' => 'app:envio-ordenes',
                    'status' => 'success',
                    'message' => 'Reporte semanal enviado: ' . $fechaInicio . ' al ' . $fechaFin,
                    'date' => now()
                ]);
                
                return Command::SUCCESS;
             }  else {
                $this->info('Este comando solo se ejecuta los lunes.');
                return Command::SUCCESS;
            }  
        } catch (\Throwable $th) {
            // Registrar error en log
            LogScheduleTask::create([
                'task' => 'app:envio-ordenes',
                'status' => 'error',
                'message' => $th->getMessage(),
                'date' => now()
            ]);
            
            $this->error('Error al generar el reporte: ' . $th->getMessage());
            return Command::FAILURE;
        }
    }
}