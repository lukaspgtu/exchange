-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20-Dez-2019 às 21:45
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
-- Estrutura da tabela `earnings`
--

CREATE TABLE `earnings` (
  `id` bigint(20) NOT NULL,
  `buy_id` bigint(20) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `value` float(11,2) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `earnings`
--

INSERT INTO `earnings` (`id`, `buy_id`, `sale_id`, `value`, `created_at`) VALUES
(1, 4, 3, 32.16, '2019-12-16 19:19:53'),
(2, 5, 4, 29.10, '2019-12-16 19:59:02'),
(3, 6, 4, 29.10, '2019-12-18 14:19:37'),
(4, 11, 7, 69000.00, '2019-12-18 14:47:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `extracts`
--

CREATE TABLE `extracts` (
  `id` bigint(20) NOT NULL,
  `user_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` bigint(20) NOT NULL,
  `type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `operational_limits`
--

CREATE TABLE `operational_limits` (
  `id` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposit_BRL` float(11,2) NOT NULL,
  `withdrawal_BRL` float(11,2) NOT NULL,
  `deposit_BTC` float(11,8) NOT NULL,
  `withdrawal_BTC` float(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `operational_limits`
--

INSERT INTO `operational_limits` (`id`, `description`, `deposit_BRL`, `withdrawal_BRL`, `deposit_BTC`, `withdrawal_BTC`) VALUES
(1, 'Sem verificar os documentos', 10000.00, 10000.00, 1.00000000, 0.00000000),
(2, 'Após verificar os documentos', 50000.00, 50000.00, 5.00000000, 5.00000000),
(3, 'Movimentação acima de 15 BTC', 100000.00, 100000.00, 10.00000000, 10.00000000);

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `user_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `type`, `amount`, `fee`, `unit_price`, `processed`, `position`, `status`, `created_at`, `executed_at`) VALUES
(2, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '3215951', '5', '31095', '3215951', 0, 'executed', '2019-12-16 19:16:25', '2019-12-16 19:16:26'),
(4, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '3215951', '4.84', '30095', '3215951', 0, 'executed', '2019-12-16 19:19:53', '2019-12-18 14:19:37'),
(6, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '1000', '16129', '31000', '1000', 0, 'executed', '2019-12-18 14:19:37', '2019-12-18 14:20:48'),
(7, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000000', '155', '31000', '13203032', 0, 'opened', '2019-12-18 14:20:48', NULL),
(8, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '100', '50000', '1000', '1', 1, 'opened', '2019-12-18 14:45:13', NULL),
(9, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '25', '125000', '100', '0', 2, 'opened', '2019-12-18 14:46:30', NULL),
(10, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '25', '12500', '1000', '0', 4, 'opened', '2019-12-18 14:47:18', NULL),
(11, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '10000', '50000', '100000', '10000', 0, 'executed', '2019-12-18 14:47:36', '2019-12-18 14:47:36'),
(12, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000', '0.01', '1000', '100000', 0, 'executed', '2019-12-18 14:49:31', '2019-12-18 14:49:31'),
(13, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000000', '5000', '1000000', '0', 2, 'opened', '2019-12-18 14:49:46', NULL),
(14, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '1000000000', '500', '10000', '0', 3, 'opened', '2019-12-18 14:53:28', NULL),
(15, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '150', '21428', '3500', '0', 3, 'opened', '2019-12-18 15:01:23', NULL),
(18, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '3215951', '5', '31095', '3215951', 0, 'executed', '2019-12-16 19:16:25', '2019-12-16 19:16:26'),
(20, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '3215951', '4.84', '30095', '3215951', 0, 'executed', '2019-12-16 19:19:53', '2019-12-18 14:19:37'),
(22, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '1000', '16129', '31000', '1000', 0, 'executed', '2019-12-18 14:19:37', '2019-12-18 14:20:48'),
(23, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000000', '155', '31000', '13203032', 0, 'opened', '2019-12-18 14:20:48', NULL),
(24, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '100', '50000', '1000', '1', 1, 'opened', '2019-12-18 14:45:13', NULL),
(25, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '25', '125000', '100', '0', 2, 'opened', '2019-12-18 14:46:30', NULL),
(26, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '25', '12500', '1000', '0', 4, 'opened', '2019-12-18 14:47:18', NULL),
(27, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '10000', '50000', '100000', '10000', 0, 'executed', '2019-12-18 14:47:36', '2019-12-18 14:47:36'),
(28, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000', '0.01', '1000', '100000', 0, 'executed', '2019-12-18 14:49:31', '2019-12-18 14:49:31'),
(29, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '100000000', '5000', '1000000', '0', 2, 'opened', '2019-12-18 14:49:46', NULL),
(30, '17091d9593cbe2e32af1cc9c495133dd', 'sale', '1000000000', '500', '10000', '0', 3, 'opened', '2019-12-18 14:53:28', NULL),
(31, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '150', '21428', '3500', '0', 3, 'opened', '2019-12-18 15:01:23', NULL),
(32, '17091d9593cbe2e32af1cc9c495133dd', 'buy', '100', '4504504', '11.1', '0', 5, 'opened', '2019-12-18 15:02:43', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `sessions_log`
--

CREATE TABLE `sessions_log` (
  `id` bigint(20) NOT NULL,
  `user_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jwt_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `sessions_log`
--

INSERT INTO `sessions_log` (`id`, `user_id`, `ip`, `device`, `platform`, `browser`, `location`, `jwt_token`, `created_at`, `updated_at`) VALUES
(2, '17091d9593cbe2e32af1cc9c495133dd', '192.168.0.61', 'WebKit', 'Windows', 'Chrome', 'Não encontrado', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xOTIuMTY4LjAuMzVcL2FwaVwvbG9naW5Ud29GYWN0b3IiLCJpYXQiOjE1NzY4NzMxNzcsImV4cCI6MTU3Njk1OTU3NywibmJmIjoxNTc2ODczMTc3LCJqdGkiOiJHWTBvUUVVT1ZiTkpZWVZLIiwic3ViIjoiMTcwOTFkOTU5M2NiZTJlMzJhZjFjYzljNDk1MTMzZGQiLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.5ryARcqJKWitRIta5pfYv8ax8ZI8qeXqk-_FiPq73xo', '2019-12-20 16:55:48', '2019-12-20 18:19:37'),
(3, '17091d9593cbe2e32af1cc9c495133dd', '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', 'Não encontrado', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xOTIuMTY4LjAuMzVcL2FwaVwvbG9naW5Ud29GYWN0b3IiLCJpYXQiOjE1NzY4Njk4NzMsImV4cCI6MTU3Njk1NjI3MywibmJmIjoxNTc2ODY5ODczLCJqdGkiOiJGZmVjdm56ZVE0elNIOVMyIiwic3ViIjoiMTcwOTFkOTU5M2NiZTJlMzJhZjFjYzljNDk1MTMzZGQiLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.qKJnA-9o_YAOSvx86OQUYhHuN1eJTieqqc6GtLVh4kg', '2019-12-20 17:24:33', '2019-12-20 17:24:33'),
(4, '39845ad957abb8b69915bdc066eab71b', '192.168.0.35', '0', '0', '0', 'Não encontrado', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xOTIuMTY4LjAuMzVcL2FwaVwvcmVnaXN0ZXIiLCJpYXQiOjE1NzY4NzQwNzksImV4cCI6MTU3Njk2MDQ3OSwibmJmIjoxNTc2ODc0MDc5LCJqdGkiOiJNU3dyN0RQaWdIZkk4RkxHIiwic3ViIjoiMzk4NDVhZDk1N2FiYjhiNjk5MTViZGMwNjZlYWI3MWIiLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0._WS6hl0ou_PJzLjWjgDkxFuXdxBV8OUmKA0UI02WwAY', '2019-12-20 18:34:42', '2019-12-20 18:34:42');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `system`
--

INSERT INTO `system` (`id`, `bitcoin_buy`, `bitcoin_sale`, `fee_buy`, `fee_sale`) VALUES
(1, 34245.10, 34092.88, 0.50, 0.50);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_date` date NOT NULL,
  `balance_BTC` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance_use_BTC` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance_BRL` float(11,2) NOT NULL,
  `balance_use_BRL` float(11,2) NOT NULL,
  `wallet_BTC` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operational_limit` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twofactor_key` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twofactor_status` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `account_type`, `document_number`, `document_date`, `balance_BTC`, `balance_use_BTC`, `balance_BRL`, `balance_use_BRL`, `wallet_BTC`, `operational_limit`, `password`, `twofactor_key`, `twofactor_status`, `email_status`, `created_at`, `updated_at`) VALUES
('17091d9593cbe2e32af1cc9c495133dd', 'Bhrenno Ribeiro', 'bhrennoribeiro@gmail.com', 'fisical', '02158697296', '1969-12-31', '-193372225', '1200000000', 90558.99, 400.00, '13nMWKS5ys6ukRFEUpwNaGb6aLT5pZZWLb', 1, '$2y$10$CGux7sdT6oc0VjwNjHNXp.72FlsfEUO0UF6FBUM3bHsBdilvqCAAe', 'V3X3H3WUZVHENF6T', 'enabled', 'confirmed', '2019-11-29 16:16:39', '2019-12-19 15:10:47'),
('39845ad957abb8b69915bdc066eab71b', 'Lucas Moraes Campos', 'lukaspgtu@hotmail.com', 'fisical', '05150781150', '1996-10-11', '0', '0', 0.00, 0.00, '13kXz65iEzTh99MjkxPiYsqn5r2utyXdxA', 1, '$2y$10$W7qR.aFN3i8LIgF6WVUUUu4jpQEOaY1kECMAx0bABGI1jNY94YK5m', '6GL5RVDZCHN2346B', 'disabled', 'unconfirmed', '2019-12-20 18:34:32', '2019-12-20 18:34:32');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `earnings`
--
ALTER TABLE `earnings`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `extracts`
--
ALTER TABLE `extracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ExtractUser` (`user_id`);

--
-- Índices para tabela `operational_limits`
--
ALTER TABLE `operational_limits`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_OrderUser` (`user_id`);

--
-- Índices para tabela `sessions_log`
--
ALTER TABLE `sessions_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_SessionUser` (`user_id`);

--
-- Índices para tabela `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_UserLimit` (`operational_limit`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `earnings`
--
ALTER TABLE `earnings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `extracts`
--
ALTER TABLE `extracts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `operational_limits`
--
ALTER TABLE `operational_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `sessions_log`
--
ALTER TABLE `sessions_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `extracts`
--
ALTER TABLE `extracts`
  ADD CONSTRAINT `FK_ExtractUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_OrderUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `sessions_log`
--
ALTER TABLE `sessions_log`
  ADD CONSTRAINT `FK_SessionUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_UserLimit` FOREIGN KEY (`operational_limit`) REFERENCES `operational_limits` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
