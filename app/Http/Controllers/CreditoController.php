<?php

namespace App\Http\Controllers;

use App\Http\Requests\Credito\ResumenRequest;
use App\Services\CreditoService;
use Illuminate\Support\Facades\Log;

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
        try {
            $cuotas = $request->validated()['cuotas'];

            Log::info("Ingresando data para generar resumen", $cuotas);

            $resultado = $this->creditoService->resumen($cuotas);

            Log::info("Resumen generado correctamente", $resultado);

            return response()->json([
                'code' => 200,
                'message' => 'Resumen generado correctamente',
                'data' => $resultado
            ], 200);
        } catch (\Exception $e) {

            Log::error('Error al generar resumen del crédito', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'code' => 500,
                'message' => 'Ocurrió un error al generar el resumen.'
            ], 500);
        }
    }
}
