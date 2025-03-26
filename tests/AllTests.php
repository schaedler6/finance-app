
<?php
use PHPUnit\Framework\TestCase;

// ==== Funções incluídas no mesmo arquivo ====
function somar($a, $b) {
    return $a + $b;
}

function calcularSaldo(array $transacoes): float {
    $saldo = 0;
    foreach ($transacoes as $t) {
        $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
    }
    return $saldo;
}

function getComCache($arquivo, $tempoSegundos, callable $gerador) {
    if (file_exists($arquivo) && (time() - filemtime($arquivo)) < $tempoSegundos) {
        return json_decode(file_get_contents($arquivo), true);
    }

    $dados = $gerador();
    file_put_contents($arquivo, json_encode($dados));
    return $dados;
}

// ==== Testes unitários ====
class SaldoTest extends TestCase {
    public function testSomar() {
        $this->assertEquals(7, somar(3, 4));
    }

    public function testCalcularSaldo() {
        $transacoes = [
            ['tipo' => 'receita', 'valor' => 1000],
            ['tipo' => 'despesa', 'valor' => 300],
            ['tipo' => 'receita', 'valor' => 200],
            ['tipo' => 'despesa', 'valor' => 100],
        ];

        $saldo = calcularSaldo($transacoes);
        $this->assertEquals(800, $saldo);
    }
}

class TransacaoDatabaseTest extends TestCase {
    private PDO $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec("CREATE TABLE transacoes (id INTEGER PRIMARY KEY, tipo TEXT, valor REAL)");
    }

    public function testInserirTransacao() {
        $stmt = $this->pdo->prepare("INSERT INTO transacoes (tipo, valor) VALUES (?, ?)");
        $stmt->execute(['receita', 500]);

        $count = $this->pdo->query("SELECT COUNT(*) FROM transacoes")->fetchColumn();
        $this->assertEquals(1, $count);
    }

    public function testComCache() {
        $arquivo = __DIR__ . '/../cache/transacoes.json';

        if (!file_exists(dirname($arquivo))) {
            mkdir(dirname($arquivo), recursive: true);
        }

        $dados = getComCache($arquivo, 60, function () {
            return [['tipo' => 'despesa', 'valor' => 100]];
        });

        $this->assertIsArray($dados);
        $this->assertEquals('despesa', $dados[0]['tipo']);
    }
}