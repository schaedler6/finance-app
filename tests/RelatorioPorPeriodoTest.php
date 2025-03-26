<?php
use PHPUnit\Framework\TestCase;

class RelatorioPorPeriodoTest extends TestCase
{
    private array $transacoes;

    protected function setUp(): void
    {
        $this->transacoes = [
            ['tipo' => 'receita', 'valor' => 1000, 'data' => '2025-03-01'],
            ['tipo' => 'despesa', 'valor' => 400, 'data' => '2025-03-05'],
            ['tipo' => 'receita', 'valor' => 500, 'data' => '2025-03-10'],
            ['tipo' => 'despesa', 'valor' => 100, 'data' => '2025-03-20'],
        ];
    }

    public function testSaldoEntre05e15()
    {
        $inicio = new DateTime('2025-03-05');
        $fim = new DateTime('2025-03-15');
        $saldo = 0;

        foreach ($this->transacoes as $t) {
            $data = new DateTime($t['data']);
            if ($data >= $inicio && $data <= $fim) {
                $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
            }
        }

        $this->assertEquals(100, $saldo);
    }
}
