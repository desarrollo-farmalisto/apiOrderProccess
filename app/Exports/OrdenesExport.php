<?php

namespace App\Exports;

use App\Models\OrdenesTraslado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdenesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dia;

    public function __construct($dia)
    {
        $this->dia = $dia;
    }

    public function collection()
    {
        //$resultado = DB::select("SELECT * FROM inventories");
        $resultado = OrdenesTraslado::all();
        
        //dd($resultado);
        $resultsTpCollection = collect($resultado);

        return $resultsTpCollection;
    }

    public function headings(): array
    {
        return [
           'Id',
           'Orden id',
           'Tipo orden',
           'canal',
           'Desde compañia',
           'Para compañia',
           'Desde ubicacion',
           'Para ubicacion',
           'Empleado',
           'Comentarios',
           'Centro costo',
           'Netsuite id',
           'Numero orden traslado',
           'Fecha netsuite',
           'Estado',
           'Estado netsuite',
           'Fecha creacion',
           'Fecha actualizacion'
        ];
    }

    public function map($orden): array
    {
        return [
            $orden->id,
            $orden->orden_id,
            $orden->tipo_orden,
            $orden->canal,
            $orden->desde_compania,
            $orden->para_compania,
            $orden->desde_ubicacion,
            $orden->para_ubicacion,
            $orden->empleado,
            $orden->comentarios,
            $orden->centro_costo,
            $orden->netsuite_id,
            $orden->numero_orden_traslado,
            $orden->fecha_netsuite,
            $orden->estado,
            $orden->estado_netsuite,
            $orden->created_at,
            $orden->updated_at
        ];
    }

}
