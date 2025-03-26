<?php
use PHPUnit\Framework\TestCase;

class TransacoesTest extends TestCase
{
    public function testTransacoesPorUsuario()
    {
        $transacoes = [
            [
                'usuario' => 'Moisés Ben Levi',
                'tipo' => 'receita',
                'valor' => 2500.00,
                'categoria' => 'Salário',
                'data' => '2025-03-01'
            ],
            [
                'usuario' => 'Moisés Ben Levi',
                'tipo' => 'despesa',
                'valor' => 800.00,
                'categoria' => 'Aluguel',
                'data' => '2025-03-05'
            ],
            [
                'usuario' => 'Golda Meir',
                'tipo' => 'receita',
                'valor' => 1800.00,
                'categoria' => 'Palestras',
                'data' => '2025-03-02'
            ],
            [
                'usuario' => 'Golda Meir',
                'tipo' => 'despesa',
                'valor' => 350.00,
                'categoria' => 'Livros',
                'data' => '2025-03-04'
            ],
            [
                'usuario' => 'David Ben-Gurion',
                'tipo' => 'despesa',
                'valor' => 120.00,
                'categoria' => 'Transporte',
                'data' => '2025-03-03'
            ],
        ];

        $this->assertEquals(5, count($transacoes));

        $totalMoises = 0;
        foreach ($transacoes as $t) {
            if ($t['usuario'] === 'Moisés Ben Levi') {
                $totalMoises += ($t['tipo'] === 'receita') ? $t['valor'] : -$t['valor'];
            }
        }

        $this->assertEquals(1700.00, $totalMoises);
    }
}
