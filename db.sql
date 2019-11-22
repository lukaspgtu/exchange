-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Nov-2019 às 22:01
-- Versão do servidor: 10.3.16-MariaDB
-- versão do PHP: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `exchange`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `category` varchar(5) NOT NULL,
  `type` varchar(8) NOT NULL,
  `amount` float(11,2) NOT NULL,
  `fee` float(11,2) NOT NULL,
  `unit_price` float(11,2) NOT NULL,
  `processed` float(11,2) NOT NULL,
  `position` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `id_user`, `category`, `type`, `amount`, `fee`, `unit_price`, `processed`, `position`, `status`, `created_at`, `executed_at`) VALUES
(1, 1, 'buy', 'limited', 500.00, 2.50, 30999.99, 0.00, 3, 'waiting', '2019-11-22 15:56:46', NULL),
(2, 1, 'buy', 'limited', 500.00, 2.50, 31999.99, 0.00, 1, 'waiting', '2019-11-22 15:59:58', NULL),
(3, 1, 'buy', 'limited', 500.00, 2.50, 31599.99, 0.00, 2, 'waiting', '2019-11-22 16:00:18', NULL),
(4, 1, 'buy', 'limited', 500.00, 2.50, 30599.99, 0.00, 4, 'waiting', '2019-11-22 16:01:24', NULL),
(7, 1, 'sale', 'limited', 30599.99, 153.00, 30599.99, 0.00, 3, 'waiting', '2019-11-22 16:10:20', NULL),
(8, 1, 'sale', 'limited', 30499.99, 152.50, 30499.99, 0.00, 2, 'waiting', '2019-11-22 16:10:50', NULL),
(9, 1, 'sale', 'limited', 30999.99, 155.00, 30999.99, 0.00, 4, 'waiting', '2019-11-22 16:11:07', NULL),
(10, 1, 'sale', 'limited', 29999.99, 150.00, 29999.99, 0.00, 1, 'waiting', '2019-11-22 16:11:49', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `system`
--

CREATE TABLE `system` (
  `id` int(1) NOT NULL,
  `bitcoin_buy` float(10,2) DEFAULT NULL,
  `bitcoin_sale` float(10,2) DEFAULT NULL,
  `fee_buy` float(10,2) DEFAULT NULL,
  `fee_sale` float(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `system`
--

INSERT INTO `system` (`id`, `bitcoin_buy`, `bitcoin_sale`, `fee_buy`, `fee_sale`) VALUES
(1, 34245.00, 34092.88, 0.50, 0.50);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `account_type` varchar(7) NOT NULL,
  `document_number` varchar(18) NOT NULL,
  `document_date` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` int(11) DEFAULT NULL,
  `2fa_key` varchar(40) DEFAULT NULL,
  `2fa_status` varchar(8) NOT NULL,
  `email_status` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `account_type`, `document_number`, `document_date`, `password`, `code`, `2fa_key`, `2fa_status`, `email_status`, `created_at`, `updated_at`) VALUES
(1, 'Lucas Moraes Campos', 'lukaspgtu@hotmail.com', 'fisical', '051.507.811-50', '1996-10-11', '$2y$10$aJv93HJqjbmaR0XqosubIe/1OubKQI8b0xE926WrA/n1ILklP2A5C', 520672511, NULL, 'disabled', 'confirmed', '2019-11-18 17:23:12', '2019-11-18 17:30:29');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
