<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditoController extends Controller
{
    public function resumen(Request $request)
    {
        $data = $request->validate([
            'cuotas' => 'required|array|min:1',
            'cuotas.*.valor' => 'required|numeric|min:0',
            'cuotas.*.fecha' => 'required|date',
        ]);

        $cuotas = $data['cuotas'];

        $total = collect($cuotas)->sum('valor');

        $ultimaFecha = collect($cuotas)
            ->max('fecha');

        return response()->json([
            'total_pagar' => $total,
            'numero_cuotas' => count($cuotas),
            'ultima_cuota' => $ultimaFecha,
        ]);
    }
}
