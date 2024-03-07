<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class JobExecutionController extends Controller
{
    public function SendOrder(Request $request)
    {
        // dd("llegue");
        try {
            $jobType = $request->input('job_type');
            $jobData = $request->input('data');
            // Despachar el trabajo correspondiente basado en el tipo proporcionado desde el proyecto A
            $jobType::dispatchNow($jobData['pedido_id']);
            Log::create([
                'proceso' => "Contoller JobExecutionController {$jobData['pedido_id']} SendOrder",
                'tabla' => 'TP_GALENICAS',
                'cant_registro' => 0,
                'cant_insertados' => 0,
                'mensaje' => 'SendOrder controller',
                'fecha' => Carbon::now('America/Bogota'),
            ]);
    
            return response()->json(['message' => 'Trabajo ejecutado con éxito']);
        } catch (Throwable $exception) {
            Log::create([
                'proceso' => "Error Contoller JobExecutionController {$jobData['pedido_id']} SendOrder",
                'tabla' => 'TP_GALENICAS',
                'cant_registro' => 0,
                'cant_insertados' => 0,
                'mensaje' => $exception,
                'fecha' => Carbon::now('America/Bogota'),
            ]);
        }
    }
}
