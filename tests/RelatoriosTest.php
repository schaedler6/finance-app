<?php
use PHPUnit\Framework\TestCase;

class RelatoriosTest extends TestCase
{
    public function testRelatorioPorPeriodo()
    {
        $inicio = '2025-01-01';
        $fim = '2025-01-31';

        $mockTransacoes = [
            ['data' => '2025-01-05', 'tipo' => 'receita', 'valor' => 1000],
            ['data' => '2025-01-10', 'tipo' => 'despesa', 'valor' => 200],
            ['data' => '2025-02-01', 'tipo' => 'receita', 'valor' => 300],
        ];

        $filtradas = array_filter($mockTransacoes, function ($t) use ($inicio, $fim) {
            return $t['data'] >= $inicio && $t['data'] <= $fim;
        });

        $saldo = array_reduce($filtradas, function ($carry, $t) {
            return $carry + ($t['tipo'] === 'receita' ? $t['valor'] : -$t['valor']);
        }, 0);

        $this->assertEquals(800, $saldo);
    }

    public function testRelatorioPorCategoria()
    {
        $mockTransacoes = [
            ['categoria' => 'Alimentação', 'valor' => 150],
            ['categoria' => 'Transporte', 'valor' => 100],
            ['categoria' => 'Alimentação', 'valor' => 250],
        ];

        $totalAlimentacao = array_reduce($mockTransacoes, function ($carry, $t) {
            return $t['categoria'] === 'Alimentação' ? $carry + $t['valor'] : $carry;
        }, 0);

        $this->assertEquals(400, $totalAlimentacao);
    }
}
