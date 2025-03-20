-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/03/2025 às 00:41
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `finance_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `cor` varchar(7) DEFAULT '#000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `tipo`, `cor`) VALUES
(1, 'Salário', 'receita', '#28a745'),
(2, 'Freelance', 'receita', '#17a2b8'),
(3, 'Alimentação', 'despesa', '#dc3545'),
(4, 'Transporte', 'despesa', '#fd7e14'),
(5, 'Moradia', 'despesa', '#6f42c1'),
(6, 'Lazer', 'despesa', '#20c997'),
(7, 'Outros', 'receita', '#6c757d'),
(8, 'Outros', 'despesa', '#6c757d');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transacoes`
--

INSERT INTO `transacoes` (`id`, `usuario_id`, `categoria_id`, `descricao`, `valor`, `data`, `tipo`) VALUES
(1, 1, 1, 'teste', 2000.00, '2025-03-20', 'receita'),
(2, 1, 8, 'teste', 1050.00, '2025-03-20', 'despesa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `limite_gastos` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `limite_gastos`) VALUES
(1, 'Admin', 'admin@email.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 1000.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `transacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `transacoes_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
