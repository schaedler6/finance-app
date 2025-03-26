<?php
declare(strict_types=1);

namespace App;

class FuncoesFinanceiras
{
    public static function somar(float $a, float $b): float
    {
        return $a + $b;
    }

    public static function calcularSaldo(array $transacoes): float
    {
        $saldo = 0;
        foreach ($transacoes as $t) {
            $saldo += $t["tipo"] === "receita" ? $t["valor"] : -$t["valor"];
        }
        return $saldo;
    }
}
