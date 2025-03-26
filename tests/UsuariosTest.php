<?php
use PHPUnit\Framework\TestCase;

class UsuariosTest extends TestCase
{
    public function testUsuariosFicticios()
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
            [
                'nome' => 'David Ben-Gurion',
                'email' => 'david.ben-gurion@email.com',
                'telefone' => '(55) 98456-1122',
                'endereco' => 'Rua Haifa, 789 - Santa Maria, RS',
            ],
        ];

        $this->assertCount(3, $usuarios);
        $this->assertEquals('Golda Meir', $usuarios[1]['nome']);
    }
}
