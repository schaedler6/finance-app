<?php
use PHPUnit\Framework\TestCase;

// === Usuários fictícios ===
class UsuariosTest extends TestCase
{
    public function testUsuarios()
    {
        $usuarios = [
            [
                'nome' => 'Moisés Ben Levi',
                'email' => 'moises.levi@email.com',
                'telefone' => '(51) 91234-5678',
                'endereco' => 'Rua Monte Sinai, 123 - Porto Alegre, RS',
            ],
            [
                'nome' => 'Golda Meir',
                'email' => 'golda.meir@email.com',
                'telefone' => '(53) 99876-4321',
                'endereco' => 'Av. Jerusalém, 456 - Pelotas, RS',
            ],
        ];

        $this->assertCount(2, $usuarios);
        $this->assertEquals('Golda Meir', $usuarios[1]['nome']);
    }
}

// === Transações por usuário ===
class TransacoesTest extends TestCase
{
    public function testSaldoUsuario()
    {
        $transacoes = [
            ['usuario' => 'Moisés', 'tipo' => 'receita', 'valor' => 2000],
            ['usuario' => 'Moisés', 'tipo' => 'despesa', 'valor' => 500],
        ];

        $saldo = 0;
        foreach ($transacoes as $t) {
            if ($t['usuario'] === 'Moisés') {
                $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
            }
        }

        $this->assertEquals(1500, $saldo);
    }
}

// === Relatório por período ===
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

// === Relatório por categoria ===
class RelatorioPorCategoriaTest extends TestCase
{
    public function testTotaisPorCategoria()
    {
        $transacoes = [
            ['categoria' => 'Aluguel', 'valor' => 800, 'tipo' => 'despesa'],
            ['categoria' => 'Aluguel', 'valor' => 1000, 'tipo' => 'despesa'],
            ['categoria' => 'Salário', 'valor' => 2500, 'tipo' => 'receita'],
        ];

        $agrupado = [];

        foreach ($transacoes as $t) {
            $cat = $t['categoria'];
            $valor = $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
            $agrupado[$cat] = ($agrupado[$cat] ?? 0) + $valor;
        }

        $this->assertEquals(-1800, $agrupado['Aluguel']);
        $this->assertEquals(2500, $agrupado['Salário']);
    }
}

// === Simulação com SQLite em memória ===
class SqlitePersistenciaTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE transacoes (id INTEGER PRIMARY KEY, tipo TEXT, valor REAL)");

        $this->db->exec("INSERT INTO transacoes (tipo, valor) VALUES
            ('receita', 1200),
            ('despesa', 400),
            ('despesa', 100)");
    }

    public function testSaldoComBanco()
    {
        $saldo = 0;
        $stmt = $this->db->query("SELECT tipo, valor FROM transacoes");

        while ($t = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
        }

        $this->assertEquals(700, $saldo);
    }
}

// === Falha simulada (opcional, comentar se quiser tudo OK) ===
class FalhaSimuladaTest extends TestCase
{
    public function testErroProposital()
    {
        $this->assertEquals(5, 2 + 2, 'Simulando falha proposital');
    }
}<?php
// Função genérica para testes
function somar($a, $b) {
    return $a + $b;
}

// Função de saldo total para usar em relatórios
function calcularSaldo(array $transacoes): float {
    $saldo = 0;
    foreach ($transacoes as $t) {
        $saldo += $t['tipo'] === 'receita' ? $t['valor'] : -$t['valor'];
    }
    return $saldo;
}

