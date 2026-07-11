<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreditoControllerTest extends TestCase
{
    public function test_calcula_resumen_del_credito()
    {
        $response = $this->postJson('/api/credito/resumen', [
            'cuotas' => [
                [
                    'valor' => 100000,
                    'fecha' => '2026-08-01'
                ],
                [
                    'valor' => 150000,
                    'fecha' => '2026-09-01'
                ],
                [
                    'valor' => 200000,
                    'fecha' => '2026-10-01'
                ],
            ]
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'code' => 200,
            'message' => 'Resumen generado correctamente',
            'data' => [
                'total_pagar' => 450000,
                'numero_cuotas' => 3,
                'ultima_cuota' => '2026-10-01',
            ]
        ]);
    }

    public function test_retorna_error_si_no_envian_cuotas()
    {
        $response = $this->postJson('/api/credito/resumen', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'cuotas'
        ]);
    }

    public function test_retorna_error_si_la_lista_de_cuotas_esta_vacia()
    {
        $response = $this->postJson('/api/credito/resumen', [
            'cuotas' => []
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'cuotas'
        ]);
    }

    public function test_retorna_error_si_falta_el_valor_de_una_cuota()
    {
        $response = $this->postJson('/api/credito/resumen', [
            'cuotas' => [
                [
                    'fecha' => '2026-08-01'
                ]
            ]
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'cuotas.0.valor'
        ]);
    }

    public function test_retorna_error_si_la_fecha_no_es_valida()
    {
        $response = $this->postJson('/api/credito/resumen', [
            'cuotas' => [
                [
                    'valor' => 100000,
                    'fecha' => 'hola'
                ]
            ]
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'cuotas.0.fecha'
        ]);
    }

    public function test_retorna_error_si_el_valor_es_negativo()
    {
        $response = $this->postJson('/api/credito/resumen', [
            'cuotas' => [
                [
                    'valor' => -50000,
                    'fecha' => '2026-08-01'
                ]
            ]
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'cuotas.0.valor'
        ]);
    }
}
