<?php
use PHPUnit\Framework\TestCase;

class CategoriasTest extends TestCase
{
    public function testCategoriasDisponiveis()
    {
        $categorias = [
            'Salário',
            'Aluguel',
            'Palestras',
            'Livros',
            'Transporte',
            'Alimentação',
            'Internet',
            'Freelancer',
            'Educação'
        ];

        $this->assertContains('Aluguel', $categorias);
        $this->assertNotContains('Viagem', $categorias);
        $this->assertEquals(9, count($categorias));
    }
}
