-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Nov-2019 às 22:08
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `type` varchar(8) NOT NULL,
  `amount` varchar(40) NOT NULL,
  `fee` varchar(40) NOT NULL,
  `unit_price` varchar(40) NOT NULL,
  `processed` varchar(40) NOT NULL,
  `position` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `id_user`, `type`, `amount`, `fee`, `unit_price`, `processed`, `position`, `status`, `created_at`, `executed_at`) VALUES
(1, 1, 'buy', '25.00', '0.00000404', '30947.87', '25.00', 0, 'executed', '2019-11-26 17:32:26', '2019-11-26 17:56:17'),
(8, 1, 'sale', '0.00080781', '0.12', '30847.87', '0.00080781', 0, 'executed', '2019-11-26 17:56:17', '2019-11-26 17:56:17'),
(9, 1, 'sale', '0.00080781', '0.12', '30847.87', '0.00080781', 0, 'executed', '2019-11-26 18:12:30', '2019-11-26 18:58:27'),
(16, 1, 'buy', '25', '0.00000404', '30947.87', '25', 0, 'executed', '2019-11-26 18:58:26', '2019-11-26 18:58:26');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sessions_log`
--

CREATE TABLE `sessions_log` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `ip` varchar(40) DEFAULT NULL,
  `device` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `sessions_log`
--

INSERT INTO `sessions_log` (`id`, `user_id`, `ip`, `device`, `platform`, `browser`, `created_at`, `updated_at`) VALUES
(1, 9, '192.168.0.35', '0', '0', '0', '2019-11-28 17:44:57', '2019-11-28 17:44:57'),
(2, 10, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 18:28:19', '2019-11-28 18:28:19'),
(3, 11, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:06:49', '2019-11-29 15:10:30'),
(4, 12, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:25:00', '2019-11-28 20:25:00'),
(5, 13, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:29:13', '2019-11-28 20:29:13'),
(6, 14, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:33:27', '2019-11-28 20:33:27'),
(7, 15, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:34:15', '2019-11-28 20:34:15'),
(8, 16, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:35:51', '2019-11-28 20:35:51'),
(9, 17, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 20:52:32', '2019-11-28 20:52:32'),
(10, 18, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 21:12:21', '2019-11-28 21:12:21'),
(11, 19, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-28 21:16:10', '2019-11-28 21:16:10'),
(12, 20, '192.168.0.61', 'WebKit', 'Windows', 'Chrome', '2019-11-28 21:22:15', '2019-11-28 21:22:15'),
(13, 1, '192.168.0.35', '0', '0', '0', '2019-11-29 14:47:55', '2019-11-29 15:23:06'),
(14, 21, '192.168.0.35', '0', '0', '0', '2019-11-29 15:28:54', '2019-11-29 18:06:53'),
(15, 22, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-29 16:16:42', '2019-11-29 18:42:10'),
(16, 21, '192.168.0.61', 'Nexus', 'AndroidOS', 'Chrome', '2019-11-29 18:43:16', '2019-11-29 18:43:43');

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
  `code` varchar(40) DEFAULT NULL,
  `twofactor_key` varchar(40) NOT NULL,
  `twofactor_status` varchar(8) NOT NULL,
  `email_status` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `account_type`, `document_number`, `document_date`, `password`, `code`, `twofactor_key`, `twofactor_status`, `email_status`, `created_at`, `updated_at`) VALUES
(21, 'Lucas Moraes Campos', 'lukaspgtu@hotmail.com', 'fisical', '05150781150', '1996-10-11', '$2y$10$VNuaHxNgncZNqsYxSbfEQerdspRreS0l/NnjsQIsyftMEce.8gCtG', '177814421', 'SGWEQFMKBXC344MB', 'enabled', 'confirmed', '2019-11-29 15:28:50', '2019-11-29 16:20:36'),
(22, 'Bhrenno Ribeiro', 'bhrennoribeiro@gmail.com', 'fisical', '02158697296', '1969-12-31', '$2y$10$LkjlNtHakh0AqTGuh/Mxt.gUeLQbSHPPX4YpdODbgMwIA4wNh6rT.', '637763022', 'V3X3H3WUZVHENF6T', 'enabled', 'unconfirmed', '2019-11-29 16:16:39', '2019-11-29 16:16:39');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `earnings`
--
ALTER TABLE `earnings`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `sessions_log`
--
ALTER TABLE `sessions_log`
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
-- AUTO_INCREMENT de tabela `earnings`
--
ALTER TABLE `earnings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `sessions_log`
--
ALTER TABLE `sessions_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
