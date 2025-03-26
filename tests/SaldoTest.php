<?php
use PHPUnit\Framework\TestCase;
use App\FuncoesFinanceiras;

class SaldoTest extends TestCase
{
    public function testSomar()
    {
        $this->assertEquals(7, FuncoesFinanceiras::somar(3, 4));
    }

    public function testCalcularSaldo()
    {
        $transacoes = [
            ["tipo" => "receita", "valor" => 1000],
            ["tipo" => "despesa", "valor" => 300],
            ["tipo" => "receita", "valor" => 200],
            ["tipo" => "despesa", "valor" => 100],
        ];

        $saldo = FuncoesFinanceiras::calcularSaldo($transacoes);
        $this->assertEquals(800, $saldo);
    }
}
