<?php

namespace App\Http\Controllers;

use App\Http\Requests\Credito\ResumenRequest;

class CreditoController extends Controller
{
    //Metodo que me devuelve el resumen del credito
    public function resumen(ResumenRequest $request)
    {      
        //Variable necesaria para contar los registros que se reciben.  
        $cuotas = $request['cuotas'];

        //Suma el valor total de las cuotas que llegan
        $total = collect($cuotas)->sum('valor');

        //Toma la fecha de la última cuota
        $ultimaFecha = collect($cuotas)
            ->max('fecha');

        return response()->json([
            'total_pagar' => $total,
            'numero_cuotas' => count($cuotas),
            'ultima_cuota' => $ultimaFecha,
        ]);
    }
}
