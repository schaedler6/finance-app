-- Criação do Banco de Dados (se não existir)
CREATE DATABASE IF NOT EXISTS finance_db;
USE finance_db;

-- Tabela de usuários (já existente)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Inserir usuário admin se não existir
INSERT INTO usuarios (nome, email, senha) 
SELECT 'Admin', 'admin@email.com', SHA2('123456', 256)
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'admin@email.com');

-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    cor VARCHAR(7) DEFAULT '#000000' -- Para usar na visualização gráfica
);

-- Inserir algumas categorias padrão
INSERT INTO categorias (nome, tipo, cor) VALUES 
('Salário', 'receita', '#28a745'),
('Freelance', 'receita', '#17a2b8'),
('Alimentação', 'despesa', '#dc3545'),
('Transporte', 'despesa', '#fd7e14'),
('Moradia', 'despesa', '#6f42c1'),
('Lazer', 'despesa', '#20c997'),
('Outros', 'receita', '#6c757d'),
('Outros', 'despesa', '#6c757d');

-- Tabela de Transações (Receitas e Despesas)
CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data DATE NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
); 