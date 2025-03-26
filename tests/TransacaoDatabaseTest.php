<?php
use PHPUnit\Framework\TestCase;

class TransacaoDatabaseTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec("CREATE TABLE transacoes (id INTEGER PRIMARY KEY, tipo TEXT, valor REAL)");
    }

    public function testInserirTransacao()
    {
        $stmt = $this->pdo->prepare("INSERT INTO transacoes (tipo, valor) VALUES (?, ?)");
        $stmt->execute(['receita', 500]);

        $count = $this->pdo->query("SELECT COUNT(*) FROM transacoes")->fetchColumn();
        $this->assertEquals(1, $count);
    }

    public function testComCache()
    {
        $arquivo = __DIR__ . '/../cache/transacoes.json';

        // Garante que o diretório exista
        if (!file_exists(dirname($arquivo))) {
            mkdir(dirname($arquivo));
        }

        // Simula dados com função de cache
        $dados = getComCache($arquivo, 60, function () {
            return [['tipo' => 'despesa', 'valor' => 100]];
        });

        $this->assertIsArray($dados);
        $this->assertEquals('despesa', $dados[0]['tipo']);
    }
}

// Função de cache incluída no mesmo arquivo para testes
function getComCache($arquivo, $tempoSegundos, callable $gerador)
{
    if (file_exists($arquivo) && (time() - filemtime($arquivo)) < $tempoSegundos) {
        return json_decode(file_get_contents($arquivo), true);
    }

    $dados = $gerador();
    file_put_contents($arquivo, json_encode($dados));
    return $dados;
}
