<?php
namespace App\Services;

class CreditoService
{
    public function resumen(array $cuotas): array
    {
        return [
            'total_pagar' => collect($cuotas)->sum('valor'),
            'numero_cuotas' => count($cuotas),
            'ultima_cuota' => collect($cuotas)->max('fecha'),
        ];
    }
}