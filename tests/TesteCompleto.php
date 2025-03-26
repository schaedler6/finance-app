
<?php
use PHPUnit\Framework\TestCase;

// === Tratamento global de erros ===
set_exception_handler(function ($e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Erro inesperado. Tente novamente.";
});

// === Função com cache em arquivo ===
function getComCache($arquivo, $tempoSegundos, callable $gerador)
{
    if (file_exists($arquivo) && (time() - filemtime($arquivo)) < $tempoSegundos) {
        return json_decode(file_get_contents($arquivo), true);
    }

    $dados = $gerador();
    file_put_contents($arquivo, json_encode($dados));
    return $dados;
}

// === Função real a ser testada ===
function calcularSaldo(array $transacoes): float
{
    $saldo = 0;
    foreach ($transacoes as $t) {
        $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
    }
    return $saldo;
}

// === Teste com PHPUnit ===
class TesteCompleto extends TestCase
{
    public function testSaldoComCacheSimulado()
    {
        $cacheFile = __DIR__ . '/.cache_test.json';

        $transacoes = getComCache($cacheFile, 60, function () {
            return [
                ['tipo' => 'receita', 'valor' => 1000],
                ['tipo' => 'despesa', 'valor' => 250],
                ['tipo' => 'receita', 'valor' => 150],
            ];
        });

        $saldo = calcularSaldo($transacoes);
        $this->assertEquals(900, $saldo);

        // Limpa o cache após o teste
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}

