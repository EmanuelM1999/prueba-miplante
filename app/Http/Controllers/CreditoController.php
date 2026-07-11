<?php

namespace App\Http\Controllers;

use App\Http\Requests\Credito\ResumenRequest;
use App\Services\CreditoService;

class CreditoController extends Controller
{
    //Variable donde el constructor inicializa el servicio.
    protected $creditoService;

    public function __construct(CreditoService $creditoService)
    {
        $this->creditoService = $creditoService;
    }

    //Metodo que me devuelve el resumen del credito
    public function resumen(ResumenRequest $request)
    {
        //Variable necesaria para contar los registros que se reciben.  
        $cuotas = $request['cuotas'];

        $resultado = $this->creditoService->resumen($cuotas);

        return response()->json([
            'code' => 200,
            'message' => 'Resumen generado correctamente',
            'data' => $resultado
        ], 200);
    }
}
