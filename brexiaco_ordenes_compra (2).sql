-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 24, 2026 at 09:30 PM
-- Server version: 5.7.44-48
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brexiaco_ordenes_compra`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `auditable_type`, `auditable_id`, `action`, `description`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 'App\\Models\\User', 2, 'superadmin_user_created', 'Usuario admon.empresas2107@gmail.com creado por Super Administrador.', NULL, '2026-07-02 23:55:24', '2026-07-02 23:55:24'),
(2, 1, 'App\\Models\\User', 3, 'superadmin_user_created', 'Usuario aaguirre_sorem@yahoo.com.mx creado por Super Administrador.', NULL, '2026-07-03 03:21:52', '2026-07-03 03:21:52'),
(3, 1, 'App\\Models\\User', 2, 'superadmin_user_updated', 'Usuario admon.empresas2107@gmail.com actualizado por Super Administrador.', NULL, '2026-07-03 03:23:56', '2026-07-03 03:23:56'),
(4, 1, 'App\\Models\\User', 2, 'superadmin_user_updated', 'Usuario admon.empresas2107@gmail.com actualizado por Super Administrador.', NULL, '2026-07-03 03:25:44', '2026-07-03 03:25:44'),
(5, 3, 'App\\Models\\Company', 1, 'company_created', 'Empresa Prodifem S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:14:24', '2026-07-03 04:14:24'),
(6, 2, 'App\\Models\\RecurringService', 1, 'service_created', 'Servicio SRV-001 dado de alta.', NULL, '2026-07-03 04:27:09', '2026-07-03 04:27:09'),
(7, 2, 'App\\Models\\RecurringService', 1, 'service_updated', 'Servicio SRV-001 actualizado.', NULL, '2026-07-03 04:27:28', '2026-07-03 04:27:28'),
(8, 2, 'App\\Models\\RecurringService', 1, 'service_support_loaded', 'Recibo cargado para SRV-001 periodo 2026-07-13.', NULL, '2026-07-03 04:30:39', '2026-07-03 04:30:39'),
(9, 3, 'App\\Models\\Company', 2, 'company_created', 'Empresa Farmasoma S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:35:52', '2026-07-03 04:35:52'),
(10, 3, 'App\\Models\\Company', 3, 'company_created', 'Empresa Vidicron S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:37:23', '2026-07-03 04:37:23'),
(11, 3, 'App\\Models\\Company', 4, 'company_created', 'Empresa Centro Biotecnologico de Terapias Avanzadas S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:38:55', '2026-07-03 04:38:55'),
(12, 3, 'App\\Models\\Company', 5, 'company_created', 'Empresa Alejandro Martinez Ruiz creada por Finanzas.', NULL, '2026-07-03 04:40:00', '2026-07-03 04:40:00'),
(13, 3, 'App\\Models\\Company', 6, 'company_created', 'Empresa Gustavo Diaz Martinez creada por Finanzas.', NULL, '2026-07-03 04:42:03', '2026-07-03 04:42:03'),
(14, 3, 'App\\Models\\Company', 7, 'company_created', 'Empresa Findelz S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:44:26', '2026-07-03 04:44:26'),
(15, 3, 'App\\Models\\Company', 8, 'company_created', 'Empresa Brimak S.A. de C.V. creada por Finanzas.', NULL, '2026-07-03 04:45:31', '2026-07-03 04:45:31'),
(16, 3, 'App\\Models\\Company', 8, 'company_updated', 'Empresa Brimak S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-03 04:45:49', '2026-07-03 04:45:49'),
(17, 1, 'App\\Models\\User', 4, 'superadmin_user_created', 'Usuario mmanayar@farmasoma.com.mx creado por Super Administrador.', NULL, '2026-07-03 04:54:35', '2026-07-03 04:54:35'),
(18, 4, 'App\\Models\\Provider', 1, 'provider_created', 'Proveedor HED Distribuidora Farmaceutica S.A. de C.V. dado de alta por comprador.', NULL, '2026-07-03 05:01:08', '2026-07-03 05:01:08'),
(19, 4, 'App\\Models\\PurchaseOrder', 1, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-03 05:06:12', '2026-07-03 05:06:12'),
(20, 3, 'App\\Models\\PurchaseOrder', 1, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-03 05:11:22', '2026-07-03 05:11:22'),
(21, 3, 'App\\Models\\PurchaseOrder', 1, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-03 05:16:26', '2026-07-03 05:16:26'),
(22, 3, 'App\\Models\\Company', 9, 'company_created', 'Empresa Sandra Paola Camacho Fonseca creada por Finanzas.', NULL, '2026-07-03 05:19:44', '2026-07-03 05:19:44'),
(23, 3, 'App\\Models\\User', 5, 'user_created', 'Usuario gcortesm@prodifem.com.mx creado por Finanzas.', NULL, '2026-07-03 05:24:29', '2026-07-03 05:24:29'),
(24, 5, 'App\\Models\\Provider', 2, 'provider_created', 'Proveedor Grupo Unimedical Soluciones S.A. de C.V. dado de alta por comprador.', NULL, '2026-07-03 05:31:16', '2026-07-03 05:31:16'),
(25, 5, 'App\\Models\\PurchaseOrder', 2, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-03 05:34:07', '2026-07-03 05:34:07'),
(26, 3, 'App\\Models\\PurchaseOrder', 2, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-03 05:35:56', '2026-07-03 05:35:56'),
(27, 3, 'App\\Models\\PurchaseOrder', 2, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-03 05:37:33', '2026-07-03 05:37:33'),
(28, 2, 'App\\Models\\RecurringService', 2, 'service_created', 'Servicio SRV-002 dado de alta.', NULL, '2026-07-04 00:53:37', '2026-07-04 00:53:37'),
(29, 2, 'App\\Models\\RecurringService', 3, 'service_created', 'Servicio SRV-003 dado de alta.', NULL, '2026-07-04 00:55:48', '2026-07-04 00:55:48'),
(30, 2, 'App\\Models\\RecurringService', 4, 'service_created', 'Servicio SRV-004 dado de alta.', NULL, '2026-07-04 00:57:28', '2026-07-04 00:57:28'),
(31, 2, 'App\\Models\\RecurringService', 5, 'service_created', 'Servicio SRV-005 dado de alta.', NULL, '2026-07-04 00:59:30', '2026-07-04 00:59:30'),
(32, 2, 'App\\Models\\RecurringService', 2, 'service_updated', 'Servicio SRV-002 actualizado.', NULL, '2026-07-04 00:59:51', '2026-07-04 00:59:51'),
(33, 2, 'App\\Models\\RecurringService', 3, 'service_updated', 'Servicio SRV-003 actualizado.', NULL, '2026-07-04 01:00:09', '2026-07-04 01:00:09'),
(34, 2, 'App\\Models\\RecurringService', 5, 'service_updated', 'Servicio SRV-005 actualizado.', NULL, '2026-07-04 01:00:41', '2026-07-04 01:00:41'),
(35, 2, 'App\\Models\\RecurringService', 4, 'service_updated', 'Servicio SRV-004 actualizado.', NULL, '2026-07-04 01:00:55', '2026-07-04 01:00:55'),
(36, 2, 'App\\Models\\RecurringService', 6, 'service_created', 'Servicio SRV-006 dado de alta.', NULL, '2026-07-04 01:02:55', '2026-07-04 01:02:55'),
(37, 2, 'App\\Models\\RecurringService', 7, 'service_created', 'Servicio SRV-007 dado de alta.', NULL, '2026-07-04 01:05:45', '2026-07-04 01:05:45'),
(38, 4, 'App\\Models\\Provider', 3, 'provider_created', 'Proveedor LEONCIO GONZALEZ MARTINEZ dado de alta por comprador.', NULL, '2026-07-06 21:49:06', '2026-07-06 21:49:06'),
(39, 4, 'App\\Models\\PurchaseOrder', 3, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-06 21:52:34', '2026-07-06 21:52:34'),
(40, 4, 'App\\Models\\Provider', 3, 'provider_updated', 'Proveedor LEONCIO GONZALEZ MARTINEZ actualizado por comprador.', NULL, '2026-07-06 22:11:45', '2026-07-06 22:11:45'),
(41, 4, 'App\\Models\\Provider', 4, 'provider_created', 'Proveedor PLOMECSA ( PLOMERIA MEXICANA DEL CENTRO, SA DE CV. ) dado de alta por comprador.', NULL, '2026-07-06 23:35:28', '2026-07-06 23:35:28'),
(42, 4, 'App\\Models\\PurchaseOrder', 4, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-07 00:20:09', '2026-07-07 00:20:09'),
(43, 3, 'App\\Models\\PurchaseOrder', 3, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-07 00:35:35', '2026-07-07 00:35:35'),
(44, 3, 'App\\Models\\PurchaseOrder', 4, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-07 00:36:03', '2026-07-07 00:36:03'),
(45, 3, 'App\\Models\\PurchaseOrder', 3, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-07 00:39:32', '2026-07-07 00:39:32'),
(46, 5, 'App\\Models\\Provider', 5, 'provider_created', 'Proveedor BAXTER HEALTHCARE MEXICO dado de alta por comprador.', NULL, '2026-07-07 01:06:19', '2026-07-07 01:06:19'),
(47, 5, 'App\\Models\\PurchaseOrder', 5, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-07 01:08:00', '2026-07-07 01:08:00'),
(48, 3, 'App\\Models\\RecurringService', 2, 'service_status_updated', 'Estado del servicio SRV-002 actualizado a paused.', NULL, '2026-07-07 01:10:00', '2026-07-07 01:10:00'),
(49, 3, 'App\\Models\\RecurringService', 2, 'service_status_updated', 'Estado del servicio SRV-002 actualizado a active.', NULL, '2026-07-07 01:10:02', '2026-07-07 01:10:02'),
(50, 4, 'App\\Models\\Provider', 6, 'provider_created', 'Proveedor INDUSTRIAS NOVACERAMIC, S.A. DE C.V. dado de alta por comprador.', NULL, '2026-07-07 01:19:32', '2026-07-07 01:19:32'),
(51, 4, 'App\\Models\\PurchaseOrder', 6, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-07 01:28:01', '2026-07-07 01:28:01'),
(52, 2, 'App\\Models\\RecurringService', 2, 'service_updated', 'Servicio SRV-002 actualizado.', NULL, '2026-07-07 01:30:32', '2026-07-07 01:30:32'),
(53, 3, 'App\\Models\\PurchaseOrder', 5, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-07 01:34:53', '2026-07-07 01:34:53'),
(54, 2, 'App\\Models\\RecurringService', 4, 'service_support_loaded', 'Recibo cargado para SRV-004 periodo 2026-07-03.', NULL, '2026-07-07 01:38:13', '2026-07-07 01:38:13'),
(55, 3, 'App\\Models\\PurchaseOrder', 6, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-07 01:38:14', '2026-07-07 01:38:14'),
(56, 2, 'App\\Models\\RecurringService', 2, 'service_support_loaded', 'Recibo cargado para SRV-002 periodo 2026-07-03.', NULL, '2026-07-07 01:39:11', '2026-07-07 01:39:11'),
(57, 2, 'App\\Models\\RecurringService', 4, 'service_support_loaded', 'Recibo cargado para SRV-004 periodo 2026-07-03.', NULL, '2026-07-07 01:40:06', '2026-07-07 01:40:06'),
(58, 3, 'App\\Models\\RecurringService', 2, 'service_paid', 'Pago registrado para SRV-002 periodo 2026-07-03.', NULL, '2026-07-07 01:41:35', '2026-07-07 01:41:35'),
(59, 3, 'App\\Models\\RecurringService', 4, 'service_paid', 'Pago registrado para SRV-004 periodo 2026-07-03.', NULL, '2026-07-07 01:43:08', '2026-07-07 01:43:08'),
(60, 3, 'App\\Models\\PurchaseOrder', 4, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-07 01:49:20', '2026-07-07 01:49:20'),
(61, 3, 'App\\Models\\User', 6, 'user_created', 'Usuario compras.admon2107@gmail.com creado por Finanzas.', NULL, '2026-07-07 02:02:29', '2026-07-07 02:02:29'),
(62, 3, 'App\\Models\\PurchaseOrder', 6, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-07 03:05:16', '2026-07-07 03:05:16'),
(63, 2, 'App\\Models\\RecurringService', 3, 'service_support_loaded', 'Recibo cargado para SRV-003 periodo 2026-07-03.', NULL, '2026-07-07 03:55:17', '2026-07-07 03:55:17'),
(64, 2, 'App\\Models\\RecurringService', 5, 'service_support_loaded', 'Recibo cargado para SRV-005 periodo 2026-07-03.', NULL, '2026-07-07 03:56:43', '2026-07-07 03:56:43'),
(65, 2, 'App\\Models\\RecurringService', 6, 'service_support_loaded', 'Recibo cargado para SRV-006 periodo 2026-07-03.', NULL, '2026-07-07 03:57:43', '2026-07-07 03:57:43'),
(66, 2, 'App\\Models\\RecurringService', 7, 'service_support_loaded', 'Recibo cargado para SRV-007 periodo 2026-07-18.', NULL, '2026-07-07 03:58:28', '2026-07-07 03:58:28'),
(67, 3, 'App\\Models\\RecurringService', 3, 'service_paid', 'Pago registrado para SRV-003 periodo 2026-07-03.', NULL, '2026-07-07 03:58:54', '2026-07-07 03:58:54'),
(68, 3, 'App\\Models\\RecurringService', 5, 'service_paid', 'Pago registrado para SRV-005 periodo 2026-07-03.', NULL, '2026-07-07 03:59:35', '2026-07-07 03:59:35'),
(69, 3, 'App\\Models\\RecurringService', 6, 'service_paid', 'Pago registrado para SRV-006 periodo 2026-07-03.', NULL, '2026-07-07 04:00:09', '2026-07-07 04:00:09'),
(70, 3, 'App\\Models\\RecurringService', 7, 'service_paid', 'Pago registrado para SRV-007 periodo 2026-07-18.', NULL, '2026-07-07 04:00:55', '2026-07-07 04:00:55'),
(71, 3, 'App\\Models\\Company', 1, 'company_updated', 'Empresa Prodifem S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-07 06:35:32', '2026-07-07 06:35:32'),
(72, 3, 'App\\Models\\Company', 1, 'company_updated', 'Empresa Prodifem S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-07 06:35:32', '2026-07-07 06:35:32'),
(73, 2, 'App\\Models\\RecurringService', 8, 'service_created', 'Servicio SRV-008 dado de alta.', NULL, '2026-07-07 07:18:29', '2026-07-07 07:18:29'),
(74, 2, 'App\\Models\\RecurringService', 8, 'service_support_loaded', 'Recibo cargado para SRV-008 periodo 2026-07-29.', NULL, '2026-07-07 07:18:59', '2026-07-07 07:18:59'),
(75, 6, 'App\\Models\\Provider', 7, 'provider_created', 'Proveedor BARBARA ITZEL CABRERA DE DIOS dado de alta por comprador.', NULL, '2026-07-07 23:46:06', '2026-07-07 23:46:06'),
(76, 6, 'App\\Models\\PurchaseOrder', 7, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-07 23:50:51', '2026-07-07 23:50:51'),
(77, 6, 'App\\Models\\Provider', 8, 'provider_created', 'Proveedor PASE Servicios Electronicos SA DE CV dado de alta por comprador.', NULL, '2026-07-08 04:05:08', '2026-07-08 04:05:08'),
(78, 6, 'App\\Models\\PurchaseOrder', 8, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-08 04:06:29', '2026-07-08 04:06:29'),
(79, 6, 'App\\Models\\Provider', 9, 'provider_created', 'Proveedor HECTOR DANIEL OLALDE DELUIS dado de alta por comprador.', NULL, '2026-07-08 04:10:02', '2026-07-08 04:10:02'),
(80, 2, 'App\\Models\\RecurringService', 6, 'service_updated', 'Servicio SRV-006 actualizado.', NULL, '2026-07-08 05:32:44', '2026-07-08 05:32:44'),
(81, 2, 'App\\Models\\RecurringService', 2, 'service_status_updated', 'Estado del servicio actualizado a inactive.', NULL, '2026-07-08 05:33:00', '2026-07-08 05:33:00'),
(82, 2, 'App\\Models\\RecurringService', 3, 'service_status_updated', 'Estado del servicio actualizado a inactive.', NULL, '2026-07-08 05:33:09', '2026-07-08 05:33:09'),
(83, 2, 'App\\Models\\RecurringService', 4, 'service_status_updated', 'Estado del servicio actualizado a inactive.', NULL, '2026-07-08 05:33:13', '2026-07-08 05:33:13'),
(84, 2, 'App\\Models\\RecurringService', 5, 'service_status_updated', 'Estado del servicio actualizado a inactive.', NULL, '2026-07-08 05:33:18', '2026-07-08 05:33:18'),
(85, 2, 'App\\Models\\RecurringService', 6, 'service_status_updated', 'Estado del servicio actualizado a inactive.', NULL, '2026-07-08 05:33:22', '2026-07-08 05:33:22'),
(86, 2, 'App\\Models\\RecurringService', 1, 'service_updated', 'Servicio SRV-001 actualizado.', NULL, '2026-07-08 05:34:25', '2026-07-08 05:34:25'),
(87, 3, 'App\\Models\\PurchaseOrder', 8, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-08 06:16:29', '2026-07-08 06:16:29'),
(88, 3, 'App\\Models\\PurchaseOrder', 7, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-08 06:16:36', '2026-07-08 06:16:36'),
(89, 3, 'App\\Models\\PurchaseOrder', 8, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-08 06:17:52', '2026-07-08 06:17:52'),
(90, 6, 'App\\Models\\Provider', 10, 'provider_created', 'Proveedor Luis Carlos Damian Carmona dado de alta por comprador.', NULL, '2026-07-08 06:36:16', '2026-07-08 06:36:16'),
(91, 6, 'App\\Models\\Provider', 7, 'provider_updated', 'Proveedor Barbara  Itzel Cabrera de Dios actualizado por comprador.', NULL, '2026-07-08 06:36:46', '2026-07-08 06:36:46'),
(92, 6, 'App\\Models\\Provider', 9, 'provider_updated', 'Proveedor Hector Daniel Olalde DeLuis actualizado por comprador.', NULL, '2026-07-08 06:37:17', '2026-07-08 06:37:17'),
(93, 6, 'App\\Models\\PurchaseOrder', 9, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-08 06:39:30', '2026-07-08 06:39:30'),
(94, 6, 'App\\Models\\PurchaseOrder', 10, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-08 06:40:44', '2026-07-08 06:40:44'),
(95, 3, 'App\\Models\\PurchaseOrder', 10, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-08 07:01:55', '2026-07-08 07:01:55'),
(96, 3, 'App\\Models\\PurchaseOrder', 9, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-08 07:02:53', '2026-07-08 07:02:53'),
(97, 4, 'App\\Models\\Provider', 11, 'provider_created', 'Proveedor METROPOLITANA DE GASES Y SOLDADURA ( LUIS FERNANDO GOMEZ SUAREZ ) dado de alta por comprador.', NULL, '2026-07-08 22:00:54', '2026-07-08 22:00:54'),
(98, 4, 'App\\Models\\PurchaseOrder', 11, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(99, 3, 'App\\Models\\PurchaseOrder', 11, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-08 23:01:59', '2026-07-08 23:01:59'),
(100, 3, 'App\\Models\\PurchaseOrder', 11, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-08 23:08:12', '2026-07-08 23:08:12'),
(101, 3, 'App\\Models\\PurchaseOrder', 10, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-09 04:07:52', '2026-07-09 04:07:52'),
(102, 3, 'App\\Models\\PurchaseOrder', 9, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-09 04:11:08', '2026-07-09 04:11:08'),
(103, 5, 'App\\Models\\Provider', 12, 'provider_created', 'Proveedor ESPECIALYMEDIC S. de R.L. de C.V. dado de alta por comprador.', NULL, '2026-07-09 04:17:29', '2026-07-09 04:17:29'),
(104, 5, 'App\\Models\\PurchaseOrder', 12, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-09 04:18:43', '2026-07-09 04:18:43'),
(105, 3, 'App\\Models\\PurchaseOrder', 12, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-09 04:19:51', '2026-07-09 04:19:51'),
(106, 5, 'App\\Models\\PurchaseOrder', 13, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-09 04:21:31', '2026-07-09 04:21:31'),
(107, 5, 'App\\Models\\PurchaseOrder', 14, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-09 04:22:31', '2026-07-09 04:22:31'),
(108, 3, 'App\\Models\\PurchaseOrder', 13, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-09 04:38:33', '2026-07-09 04:38:33'),
(109, 3, 'App\\Models\\PurchaseOrder', 14, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-09 04:38:37', '2026-07-09 04:38:37'),
(110, 2, 'App\\Models\\RecurringService', 9, 'service_created', 'Servicio SRV-009 dado de alta.', NULL, '2026-07-09 07:15:10', '2026-07-09 07:15:10'),
(111, 2, 'App\\Models\\RecurringService', 9, 'service_updated', 'Servicio SRV-009 actualizado.', NULL, '2026-07-09 07:24:37', '2026-07-09 07:24:37'),
(112, 2, 'App\\Models\\RecurringService', 9, 'service_support_loaded', 'Recibo cargado para SRV-009 periodo 2026-07-15.', NULL, '2026-07-09 08:17:45', '2026-07-09 08:17:45'),
(113, 6, 'App\\Models\\Provider', 13, 'provider_created', 'Proveedor CONDOMINIO BC 245 dado de alta por comprador.', NULL, '2026-07-10 00:05:15', '2026-07-10 00:05:15'),
(114, 6, 'App\\Models\\PurchaseOrder', 15, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 00:06:26', '2026-07-10 00:06:26'),
(115, 6, 'App\\Models\\Provider', 14, 'provider_created', 'Proveedor Cyberpuerta S.A. de C.V. dado de alta por comprador.', NULL, '2026-07-10 00:12:13', '2026-07-10 00:12:13'),
(116, 6, 'App\\Models\\Provider', 13, 'provider_updated', 'Proveedor Condominio BC 245 actualizado por comprador.', NULL, '2026-07-10 00:12:45', '2026-07-10 00:12:45'),
(117, 6, 'App\\Models\\PurchaseOrder', 16, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 00:13:54', '2026-07-10 00:13:54'),
(118, 3, 'App\\Models\\Company', 10, 'company_created', 'Empresa Grilsa S.A. de C.V. creada por Finanzas.', NULL, '2026-07-10 00:24:24', '2026-07-10 00:24:24'),
(119, 6, 'App\\Models\\Provider', 15, 'provider_created', 'Proveedor Maria Magdalena Aviles Gonzalez dado de alta por comprador.', NULL, '2026-07-10 00:27:03', '2026-07-10 00:27:03'),
(120, 6, 'App\\Models\\PurchaseOrder', 17, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 00:28:26', '2026-07-10 00:28:26'),
(121, 3, 'App\\Models\\PurchaseOrder', 15, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 00:35:34', '2026-07-10 00:35:34'),
(122, 3, 'App\\Models\\PurchaseOrder', 16, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 00:35:42', '2026-07-10 00:35:42'),
(123, 3, 'App\\Models\\PurchaseOrder', 15, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-10 00:36:14', '2026-07-10 00:36:14'),
(124, 3, 'App\\Models\\RecurringService', 9, 'service_paid', 'Pago registrado para SRV-009 periodo 2026-07-15.', NULL, '2026-07-10 00:42:04', '2026-07-10 00:42:04'),
(125, 4, 'App\\Models\\Provider', 16, 'provider_created', 'Proveedor RODAMIENTOS CARRILLO ( JULIO CESAR CARRILO MORALES ) dado de alta por comprador.', NULL, '2026-07-10 00:47:57', '2026-07-10 00:47:57'),
(126, 4, 'App\\Models\\PurchaseOrder', 18, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 00:52:03', '2026-07-10 00:52:03'),
(127, 3, 'App\\Models\\PurchaseOrder', 17, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 00:54:27', '2026-07-10 00:54:27'),
(128, 3, 'App\\Models\\PurchaseOrder', 17, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-10 00:54:48', '2026-07-10 00:54:48'),
(129, 3, 'App\\Models\\PurchaseOrder', 16, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-10 01:25:07', '2026-07-10 01:25:07'),
(130, 3, 'App\\Models\\PurchaseOrder', 18, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 01:48:36', '2026-07-10 01:48:36'),
(131, 3, 'App\\Models\\PurchaseOrder', 18, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-10 01:48:59', '2026-07-10 01:48:59'),
(132, 5, 'App\\Models\\Provider', 17, 'provider_created', 'Proveedor Noe Pillado Cruz dado de alta por comprador.', NULL, '2026-07-10 02:46:14', '2026-07-10 02:46:14'),
(133, 5, 'App\\Models\\PurchaseOrder', 19, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 02:47:14', '2026-07-10 02:47:14'),
(134, 5, 'App\\Models\\Provider', 18, 'provider_created', 'Proveedor Distribución Especializada de Medicamento S.A de C.V. dado de alta por comprador.', NULL, '2026-07-10 02:50:48', '2026-07-10 02:50:48'),
(135, 5, 'App\\Models\\Provider', 19, 'provider_created', 'Proveedor COMERCIALIZADORA DE MEDICAMENTOS JASER dado de alta por comprador.', NULL, '2026-07-10 02:51:53', '2026-07-10 02:51:53'),
(136, 3, 'App\\Models\\PurchaseOrder', 19, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-10 02:54:28', '2026-07-10 02:54:28'),
(137, 3, 'App\\Models\\PurchaseOrder', 7, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-10 03:00:32', '2026-07-10 03:00:32'),
(138, 5, 'App\\Models\\PurchaseOrder', 20, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 03:05:09', '2026-07-10 03:05:09'),
(139, 3, 'App\\Models\\PurchaseOrder', 20, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-10 03:06:02', '2026-07-10 03:06:02'),
(140, 5, 'App\\Models\\PurchaseOrder', 21, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 03:32:16', '2026-07-10 03:32:16'),
(141, 3, 'App\\Models\\PurchaseOrder', 21, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 03:45:05', '2026-07-10 03:45:05'),
(142, 3, 'App\\Models\\PurchaseOrder', 21, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-10 03:49:09', '2026-07-10 03:49:09'),
(143, 5, 'App\\Models\\PurchaseOrder', 22, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 05:14:30', '2026-07-10 05:14:30'),
(144, 5, 'App\\Models\\PurchaseOrder', 23, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 05:19:39', '2026-07-10 05:19:39'),
(145, 5, 'App\\Models\\PurchaseOrder', 24, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 05:41:42', '2026-07-10 05:41:42'),
(146, 3, 'App\\Models\\PurchaseOrder', 23, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 06:27:52', '2026-07-10 06:27:52'),
(147, 3, 'App\\Models\\PurchaseOrder', 24, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 06:28:19', '2026-07-10 06:28:19'),
(148, 3, 'App\\Models\\PurchaseOrder', 22, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-10 06:29:04', '2026-07-10 06:29:04'),
(149, 3, 'App\\Models\\Company', 5, 'company_deleted', 'Empresa Alejandro Martinez Ruiz eliminada por Finanzas.', NULL, '2026-07-10 06:41:36', '2026-07-10 06:41:36'),
(150, 3, 'App\\Models\\Company', 11, 'company_created', 'Empresa DENISE MEDINA MEDINA creada por Finanzas.', NULL, '2026-07-10 06:56:25', '2026-07-10 06:56:25'),
(151, 3, 'App\\Models\\Company', 11, 'company_updated', 'Empresa Denise Medina Medina actualizada por Finanzas.', NULL, '2026-07-10 06:56:50', '2026-07-10 06:56:50'),
(152, 3, 'App\\Models\\Company', 11, 'company_updated', 'Empresa Denise Medina Medina actualizada por Finanzas.', NULL, '2026-07-10 06:56:55', '2026-07-10 06:56:55'),
(153, 6, 'App\\Models\\Provider', 20, 'provider_created', 'Proveedor Bertha Guadalupe Garcia Díaz dado de alta por comprador.', NULL, '2026-07-10 07:08:31', '2026-07-10 07:08:31'),
(154, 6, 'App\\Models\\Provider', 14, 'provider_updated', 'Proveedor Cyberpuerta S.A. de C.V. actualizado por comprador.', NULL, '2026-07-10 07:09:40', '2026-07-10 07:09:40'),
(155, 6, 'App\\Models\\PurchaseOrder', 25, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-10 07:11:31', '2026-07-10 07:11:31'),
(156, 3, 'App\\Models\\Company', 12, 'company_created', 'Empresa Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V. creada por Finanzas.', NULL, '2026-07-11 03:38:22', '2026-07-11 03:38:22'),
(157, 3, 'App\\Models\\Company', 13, 'company_created', 'Empresa Clínica Pediátrica de Endocrinología S.A. de C.V. creada por Finanzas.', NULL, '2026-07-11 03:40:54', '2026-07-11 03:40:54'),
(158, 3, 'App\\Models\\PurchaseOrder', 25, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-11 04:02:04', '2026-07-11 04:02:04'),
(159, 3, 'App\\Models\\Company', 14, 'company_created', 'Empresa Clínica Pediátrica Del Crecimiento S.A. de C.V. creada por Finanzas.', NULL, '2026-07-11 04:03:53', '2026-07-11 04:03:53'),
(160, 3, 'App\\Models\\Company', 15, 'company_created', 'Empresa Sociedad Pediátrica Denla S.A. de C.V. creada por Finanzas.', NULL, '2026-07-11 05:07:46', '2026-07-11 05:07:46'),
(161, 4, 'App\\Models\\Provider', 21, 'provider_created', 'Proveedor ANDAMIOS CONSTR-CAS ( BENJAMIN PERDIGON NAVA ) dado de alta por comprador.', NULL, '2026-07-13 21:11:36', '2026-07-13 21:11:36'),
(162, 4, 'App\\Models\\PurchaseOrder', 26, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-14 01:15:33', '2026-07-14 01:15:33'),
(163, 3, 'App\\Models\\PurchaseOrder', 26, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-14 01:26:44', '2026-07-14 01:26:44'),
(164, 3, 'App\\Models\\PurchaseOrder', 26, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-14 01:31:17', '2026-07-14 01:31:17'),
(165, 3, 'App\\Models\\PurchaseOrder', 25, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-14 01:51:31', '2026-07-14 01:51:31'),
(166, 3, 'App\\Models\\Company', 16, 'company_created', 'Empresa Biozig S.A. de C.V. creada por Finanzas.', NULL, '2026-07-14 01:52:53', '2026-07-14 01:52:53'),
(167, 3, 'App\\Models\\Company', 17, 'company_created', 'Empresa Distritur S.A. de C.V. creada por Finanzas.', NULL, '2026-07-14 01:54:48', '2026-07-14 01:54:48'),
(168, 2, 'App\\Models\\RecurringService', 10, 'service_created', 'Servicio SRV-010 dado de alta.', NULL, '2026-07-14 02:03:52', '2026-07-14 02:03:52'),
(169, 2, 'App\\Models\\RecurringService', 10, 'service_support_loaded', 'Recibo cargado para SRV-010 periodo 2026-07-10.', NULL, '2026-07-14 02:05:24', '2026-07-14 02:05:24'),
(170, 3, 'App\\Models\\Company', 18, 'company_created', 'Empresa Duprosa  S.A. de C.V. creada por Finanzas.', NULL, '2026-07-14 03:47:33', '2026-07-14 03:47:33'),
(171, 3, 'App\\Models\\Company', 19, 'company_created', 'Empresa Centro de Epilepsia S.C. creada por Finanzas.', NULL, '2026-07-14 03:51:29', '2026-07-14 03:51:29'),
(172, 3, 'App\\Models\\Company', 20, 'company_created', 'Empresa Distrivideo S.A. de C.V. creada por Finanzas.', NULL, '2026-07-14 03:54:34', '2026-07-14 03:54:34'),
(173, 4, 'App\\Models\\Provider', 22, 'provider_created', 'Proveedor LUIS ENRIQUE GONZALEZ CUADROS ( ESCOMBRO Y COSTALES ) dado de alta por comprador.', NULL, '2026-07-15 22:58:36', '2026-07-15 22:58:36'),
(174, 4, 'App\\Models\\PurchaseOrder', 27, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(175, 3, 'App\\Models\\PurchaseOrder', 27, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-16 00:29:33', '2026-07-16 00:29:33'),
(176, 3, 'App\\Models\\PurchaseOrder', 27, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-16 00:29:58', '2026-07-16 00:29:58'),
(177, 6, 'App\\Models\\PurchaseOrder', 28, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-16 05:56:56', '2026-07-16 05:56:56'),
(178, 6, 'App\\Models\\Provider', 23, 'provider_created', 'Proveedor Mayte Valdez Garcia dado de alta por comprador.', NULL, '2026-07-16 06:00:44', '2026-07-16 06:00:44'),
(179, 6, 'App\\Models\\PurchaseOrder', 29, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-16 06:06:40', '2026-07-16 06:06:40'),
(180, 3, 'App\\Models\\PurchaseOrder', 28, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-16 06:37:53', '2026-07-16 06:37:53'),
(181, 3, 'App\\Models\\PurchaseOrder', 28, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-16 06:40:04', '2026-07-16 06:40:04'),
(182, 6, 'App\\Models\\PurchaseOrder', 30, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-16 06:40:17', '2026-07-16 06:40:17'),
(183, 3, 'App\\Models\\PurchaseOrder', 30, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-16 06:41:50', '2026-07-16 06:41:50'),
(184, 3, 'App\\Models\\PurchaseOrder', 30, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-16 06:42:31', '2026-07-16 06:42:31'),
(185, 2, 'App\\Models\\RecurringService', 11, 'service_created', 'Servicio SRV-011 dado de alta.', NULL, '2026-07-16 06:43:25', '2026-07-16 06:43:25'),
(186, 2, 'App\\Models\\RecurringService', 12, 'service_created', 'Servicio SRV-012 dado de alta.', NULL, '2026-07-16 06:48:13', '2026-07-16 06:48:13'),
(187, 2, 'App\\Models\\RecurringService', 13, 'service_created', 'Servicio SRV-013 dado de alta.', NULL, '2026-07-16 06:56:14', '2026-07-16 06:56:14'),
(188, 2, 'App\\Models\\RecurringService', 10, 'service_updated', 'Servicio SRV-010 actualizado.', NULL, '2026-07-16 06:58:59', '2026-07-16 06:58:59'),
(189, 2, 'App\\Models\\RecurringService', 10, 'service_updated', 'Servicio SRV-010 actualizado.', NULL, '2026-07-16 06:59:38', '2026-07-16 06:59:38'),
(190, 2, 'App\\Models\\RecurringService', 10, 'service_updated', 'Servicio SRV-010 actualizado.', NULL, '2026-07-16 07:02:48', '2026-07-16 07:02:48'),
(191, 2, 'App\\Models\\RecurringService', 6, 'service_updated', 'Servicio SRV-006 actualizado.', NULL, '2026-07-16 07:05:28', '2026-07-16 07:05:28'),
(192, 2, 'App\\Models\\RecurringService', 14, 'service_created', 'Servicio SRV-014 dado de alta.', NULL, '2026-07-16 07:15:37', '2026-07-16 07:15:37'),
(193, 2, 'App\\Models\\RecurringService', 15, 'service_created', 'Servicio SRV-015 dado de alta.', NULL, '2026-07-16 07:23:38', '2026-07-16 07:23:38'),
(194, 6, 'App\\Models\\Provider', 24, 'provider_created', 'Proveedor Intercompras Comercio Electrónico S.A. de C.V. dado de alta por comprador.', NULL, '2026-07-16 23:26:39', '2026-07-16 23:26:39'),
(195, 6, 'App\\Models\\PurchaseOrder', 31, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-16 23:28:11', '2026-07-16 23:28:11'),
(196, 3, 'App\\Models\\PurchaseOrder', 29, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-17 01:18:47', '2026-07-17 01:18:47'),
(197, 3, 'App\\Models\\PurchaseOrder', 31, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-17 01:19:08', '2026-07-17 01:19:08'),
(198, 2, 'App\\Models\\RecurringService', 14, 'service_support_loaded', 'Recibo cargado para SRV-014 periodo 2026-07-07.', NULL, '2026-07-17 01:22:53', '2026-07-17 01:22:53'),
(199, 2, 'App\\Models\\RecurringService', 15, 'service_support_loaded', 'Recibo cargado para SRV-015 periodo 2026-07-10.', NULL, '2026-07-17 01:27:42', '2026-07-17 01:27:42'),
(200, 2, 'App\\Models\\RecurringService', 11, 'service_support_loaded', 'Recibo cargado para SRV-011 periodo 2026-07-30.', NULL, '2026-07-17 01:38:28', '2026-07-17 01:38:28'),
(201, 2, 'App\\Models\\RecurringService', 12, 'service_support_loaded', 'Recibo cargado para SRV-012 periodo 2026-07-30.', NULL, '2026-07-17 01:38:49', '2026-07-17 01:38:49'),
(202, 2, 'App\\Models\\RecurringService', 13, 'service_support_loaded', 'Recibo cargado para SRV-013 periodo 2026-07-30.', NULL, '2026-07-17 01:39:55', '2026-07-17 01:39:55'),
(203, 5, 'App\\Models\\Provider', 25, 'provider_created', 'Proveedor INDUSTRIAS PLASTICAS MEDICAS SA DE CV dado de alta por comprador.', NULL, '2026-07-17 02:46:30', '2026-07-17 02:46:30'),
(204, 5, 'App\\Models\\PurchaseOrder', 32, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-17 03:03:35', '2026-07-17 03:03:35'),
(205, 3, 'App\\Models\\PurchaseOrder', 29, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-17 03:10:48', '2026-07-17 03:10:48'),
(206, 3, 'App\\Models\\PurchaseOrder', 31, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-17 03:11:44', '2026-07-17 03:11:44'),
(207, 3, 'App\\Models\\PurchaseOrder', 32, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-17 03:13:32', '2026-07-17 03:13:32'),
(208, 3, 'App\\Models\\PurchaseOrder', 32, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-17 03:32:44', '2026-07-17 03:32:44'),
(209, 4, 'App\\Models\\PurchaseOrder', 33, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-17 21:32:22', '2026-07-17 21:32:22'),
(210, 4, 'App\\Models\\PurchaseOrder', 34, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-17 21:38:26', '2026-07-17 21:38:26'),
(211, 2, 'App\\Models\\RecurringService', 16, 'service_created', 'Servicio SRV-016 dado de alta.', NULL, '2026-07-17 23:31:06', '2026-07-17 23:31:06'),
(212, 2, 'App\\Models\\RecurringService', 16, 'service_support_loaded', 'Recibo cargado para SRV-016 periodo 2026-07-02.', NULL, '2026-07-17 23:31:39', '2026-07-17 23:31:39'),
(213, 6, 'App\\Models\\PurchaseOrder', 35, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-18 00:09:28', '2026-07-18 00:09:28'),
(214, 2, 'App\\Models\\RecurringService', 17, 'service_created', 'Servicio SRV-017 dado de alta.', NULL, '2026-07-18 00:35:39', '2026-07-18 00:35:39'),
(215, 2, 'App\\Models\\RecurringService', 17, 'service_support_loaded', 'Recibo cargado para SRV-017 periodo 2026-07-02.', NULL, '2026-07-18 00:41:42', '2026-07-18 00:41:42'),
(216, 4, 'App\\Models\\Provider', 21, 'provider_updated', 'Proveedor ANDAMIOS CONSTR-CAS ( SOTA & EMP S DE RL DE CV ) actualizado por comprador.', NULL, '2026-07-18 01:34:02', '2026-07-18 01:34:02'),
(217, 4, 'App\\Models\\PurchaseOrder', 36, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-18 01:36:18', '2026-07-18 01:36:18'),
(218, 2, 'App\\Models\\RecurringService', 18, 'service_created', 'Servicio SRV-018 dado de alta.', NULL, '2026-07-18 02:08:46', '2026-07-18 02:08:46'),
(219, 2, 'App\\Models\\RecurringService', 18, 'service_support_loaded', 'Recibo cargado para SRV-018 periodo 2026-07-19.', NULL, '2026-07-18 02:09:21', '2026-07-18 02:09:21'),
(220, 3, 'App\\Models\\Company', 21, 'company_created', 'Empresa Centro Pediátrico Gune S.C. creada por Finanzas.', NULL, '2026-07-18 02:14:50', '2026-07-18 02:14:50'),
(221, 2, 'App\\Models\\RecurringService', 19, 'service_created', 'Servicio SRV-019 dado de alta.', NULL, '2026-07-18 02:17:35', '2026-07-18 02:17:35'),
(222, 2, 'App\\Models\\RecurringService', 19, 'service_support_loaded', 'Recibo cargado para SRV-019 periodo 2026-07-30.', NULL, '2026-07-18 02:19:12', '2026-07-18 02:19:12'),
(223, 3, 'App\\Models\\Company', 22, 'company_created', 'Empresa Tric S.A. de C.V. creada por Finanzas.', NULL, '2026-07-18 03:17:15', '2026-07-18 03:17:15'),
(224, 2, 'App\\Models\\RecurringService', 20, 'service_created', 'Servicio SRV-020 dado de alta.', NULL, '2026-07-18 03:20:22', '2026-07-18 03:20:22'),
(225, 2, 'App\\Models\\RecurringService', 19, 'service_updated', 'Servicio SRV-019 actualizado.', NULL, '2026-07-18 03:21:13', '2026-07-18 03:21:13'),
(226, 2, 'App\\Models\\RecurringService', 18, 'service_updated', 'Servicio SRV-018 actualizado.', NULL, '2026-07-18 03:21:58', '2026-07-18 03:21:58'),
(227, 2, 'App\\Models\\RecurringService', 17, 'service_updated', 'Servicio SRV-017 actualizado.', NULL, '2026-07-18 03:22:33', '2026-07-18 03:22:33'),
(228, 2, 'App\\Models\\RecurringService', 20, 'service_support_loaded', 'Recibo cargado para SRV-020 periodo 2026-07-16.', NULL, '2026-07-18 03:23:44', '2026-07-18 03:23:44'),
(229, 1, 'App\\Models\\PurchaseOrder', 2, 'payment_replaced', 'Comprobante de pago reemplazado por Finanzas.', NULL, '2026-07-20 13:27:35', '2026-07-20 13:27:35'),
(230, 3, 'App\\Models\\PurchaseOrder', 33, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-20 23:16:42', '2026-07-20 23:16:42'),
(231, 3, 'App\\Models\\PurchaseOrder', 33, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-20 23:18:47', '2026-07-20 23:18:47'),
(232, 3, 'App\\Models\\PurchaseOrder', 34, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-20 23:18:53', '2026-07-20 23:18:53'),
(233, 3, 'App\\Models\\PurchaseOrder', 34, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-20 23:20:48', '2026-07-20 23:20:48'),
(234, 3, 'App\\Models\\PurchaseOrder', 35, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-20 23:20:57', '2026-07-20 23:20:57'),
(235, 3, 'App\\Models\\PurchaseOrder', 35, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-20 23:21:24', '2026-07-20 23:21:24'),
(236, 3, 'App\\Models\\PurchaseOrder', 36, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-20 23:21:28', '2026-07-20 23:21:28'),
(237, 3, 'App\\Models\\PurchaseOrder', 36, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-20 23:22:03', '2026-07-20 23:22:03'),
(238, 3, 'App\\Models\\RecurringService', 16, 'service_paid', 'Pago registrado para SRV-016 periodo 2026-07-02.', NULL, '2026-07-20 23:26:17', '2026-07-20 23:26:17'),
(239, 3, 'App\\Models\\RecurringService', 17, 'service_paid', 'Pago registrado para SRV-017 periodo 2026-07-02.', NULL, '2026-07-20 23:27:03', '2026-07-20 23:27:03'),
(240, 3, 'App\\Models\\RecurringService', 14, 'service_paid', 'Pago registrado para SRV-014 periodo 2026-07-07.', NULL, '2026-07-20 23:28:08', '2026-07-20 23:28:08'),
(241, 3, 'App\\Models\\RecurringService', 10, 'service_paid', 'Pago registrado para SRV-010 periodo 2026-07-10.', NULL, '2026-07-20 23:31:08', '2026-07-20 23:31:08'),
(242, 3, 'App\\Models\\RecurringService', 15, 'service_paid', 'Pago registrado para SRV-015 periodo 2026-07-10.', NULL, '2026-07-20 23:31:43', '2026-07-20 23:31:43'),
(243, 2, 'App\\Models\\RecurringService', 20, 'service_updated', 'Servicio SRV-020 actualizado.', NULL, '2026-07-20 23:41:51', '2026-07-20 23:41:51'),
(244, 2, 'App\\Models\\RecurringService', 19, 'service_updated', 'Servicio SRV-019 actualizado.', NULL, '2026-07-20 23:42:31', '2026-07-20 23:42:31'),
(245, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-20 23:44:38', '2026-07-20 23:44:38'),
(246, 2, 'App\\Models\\RecurringService', 17, 'service_updated', 'Servicio SRV-017 actualizado.', NULL, '2026-07-21 00:32:12', '2026-07-21 00:32:12'),
(247, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-21 00:33:00', '2026-07-21 00:33:00'),
(248, 2, 'App\\Models\\RecurringService', 17, 'service_updated', 'Servicio SRV-017 actualizado.', NULL, '2026-07-21 00:33:52', '2026-07-21 00:33:52'),
(249, 3, 'App\\Models\\RecurringService', 20, 'service_paid', 'Pago registrado para SRV-020 periodo 2026-07-16.', NULL, '2026-07-21 00:34:19', '2026-07-21 00:34:19'),
(250, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-21 00:34:28', '2026-07-21 00:34:28'),
(251, 3, 'App\\Models\\PurchaseOrder', 25, 'payment_replaced', 'Comprobante de pago reemplazado por Finanzas.', NULL, '2026-07-21 00:53:20', '2026-07-21 00:53:20'),
(252, 3, 'App\\Models\\PurchaseOrder', 22, 'rejected', 'OC rechazada: No cumple criterios de autorizacion.', NULL, '2026-07-21 01:32:23', '2026-07-21 01:32:23'),
(253, 6, 'App\\Models\\Provider', 26, 'provider_created', 'Proveedor Aceros Galaxy SA de CV dado de alta por comprador.', NULL, '2026-07-21 01:35:24', '2026-07-21 01:35:24'),
(254, 6, 'App\\Models\\PurchaseOrder', 37, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 01:37:22', '2026-07-21 01:37:22'),
(255, 5, 'App\\Models\\PurchaseOrder', 38, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 01:38:41', '2026-07-21 01:38:41'),
(256, 5, 'App\\Models\\PurchaseOrder', 39, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 01:52:13', '2026-07-21 01:52:13'),
(257, 5, 'App\\Models\\PurchaseOrder', 40, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 01:59:06', '2026-07-21 01:59:06'),
(258, 5, 'App\\Models\\PurchaseOrder', 41, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 02:02:53', '2026-07-21 02:02:53'),
(259, 5, 'App\\Models\\PurchaseOrder', 42, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 02:07:43', '2026-07-21 02:07:43'),
(260, 5, 'App\\Models\\PurchaseOrder', 43, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 02:15:47', '2026-07-21 02:15:47'),
(261, 5, 'App\\Models\\PurchaseOrder', 44, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 02:24:17', '2026-07-21 02:24:17'),
(262, 2, 'App\\Models\\RecurringService', 21, 'service_created', 'Servicio SRV-021 dado de alta.', NULL, '2026-07-21 02:40:03', '2026-07-21 02:40:03'),
(263, 2, 'App\\Models\\RecurringService', 22, 'service_created', 'Servicio SRV-022 dado de alta.', NULL, '2026-07-21 02:41:35', '2026-07-21 02:41:35'),
(264, 2, 'App\\Models\\RecurringService', 23, 'service_created', 'Servicio SRV-023 dado de alta.', NULL, '2026-07-21 02:42:29', '2026-07-21 02:42:29'),
(265, 2, 'App\\Models\\RecurringService', 17, 'service_updated', 'Servicio SRV-017 actualizado.', NULL, '2026-07-21 02:52:14', '2026-07-21 02:52:14'),
(266, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-21 03:01:22', '2026-07-21 03:01:22'),
(267, 3, 'App\\Models\\PurchaseOrder', 37, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-21 03:01:46', '2026-07-21 03:01:46'),
(268, 3, 'App\\Models\\PurchaseOrder', 37, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-21 03:02:17', '2026-07-21 03:02:17'),
(269, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-21 03:02:53', '2026-07-21 03:02:53'),
(270, 2, 'App\\Models\\RecurringService', 16, 'service_updated', 'Servicio SRV-016 actualizado.', NULL, '2026-07-21 03:03:43', '2026-07-21 03:03:43'),
(271, 2, 'App\\Models\\RecurringService', 24, 'service_created', 'Servicio SRV-024 dado de alta.', NULL, '2026-07-21 03:42:24', '2026-07-21 03:42:24'),
(272, 4, 'App\\Models\\PurchaseOrder', 45, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 22:39:50', '2026-07-21 22:39:50'),
(273, 3, 'App\\Models\\PurchaseOrder', 45, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-21 23:09:40', '2026-07-21 23:09:40'),
(274, 3, 'App\\Models\\PurchaseOrder', 45, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-21 23:09:54', '2026-07-21 23:09:54'),
(275, 6, 'App\\Models\\PurchaseOrder', 46, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-21 23:27:11', '2026-07-21 23:27:11'),
(276, 3, 'App\\Models\\PurchaseOrder', 46, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-22 00:13:06', '2026-07-22 00:13:06'),
(277, 3, 'App\\Models\\PurchaseOrder', 46, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-22 00:15:29', '2026-07-22 00:15:29'),
(278, 2, 'App\\Models\\RecurringService', 25, 'service_created', 'Servicio SRV-025 dado de alta.', NULL, '2026-07-22 01:51:31', '2026-07-22 01:51:31'),
(279, 2, 'App\\Models\\RecurringService', 26, 'service_created', 'Servicio SRV-026 dado de alta.', NULL, '2026-07-22 01:55:11', '2026-07-22 01:55:11'),
(280, 2, 'App\\Models\\RecurringService', 26, 'service_support_loaded', 'Recibo cargado para SRV-026 periodo 2026-07-04.', NULL, '2026-07-22 03:09:51', '2026-07-22 03:09:51'),
(281, 3, 'App\\Models\\User', 7, 'user_created', 'Usuario recepcion.0918@gmail.com creado por Finanzas.', NULL, '2026-07-22 07:25:13', '2026-07-22 07:25:13'),
(282, 3, 'App\\Models\\RecurringService', 18, 'service_paid', 'Pago registrado para SRV-018 periodo 2026-07-19.', NULL, '2026-07-22 07:29:25', '2026-07-22 07:29:25'),
(283, 6, 'App\\Models\\PurchaseOrder', 47, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-22 22:37:31', '2026-07-22 22:37:31'),
(284, 3, 'App\\Models\\User', 7, 'user_status_updated', 'Usuario recepcion.0918@gmail.com desactivado.', NULL, '2026-07-22 23:15:56', '2026-07-22 23:15:56'),
(285, 3, 'App\\Models\\User', 7, 'user_status_updated', 'Usuario recepcion.0918@gmail.com activado.', NULL, '2026-07-22 23:16:56', '2026-07-22 23:16:56'),
(286, 3, 'App\\Models\\User', 7, 'user_status_updated', 'Usuario recepcion.0918@gmail.com desactivado.', NULL, '2026-07-22 23:18:09', '2026-07-22 23:18:09'),
(287, 3, 'App\\Models\\User', 7, 'user_status_updated', 'Usuario recepcion.0918@gmail.com activado.', NULL, '2026-07-22 23:19:07', '2026-07-22 23:19:07'),
(288, 3, 'App\\Models\\PurchaseOrder', 47, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-23 00:50:41', '2026-07-23 00:50:41'),
(289, 3, 'App\\Models\\PurchaseOrder', 47, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-23 00:50:53', '2026-07-23 00:50:53'),
(290, 2, 'App\\Models\\RecurringService', 25, 'service_updated', 'Servicio SRV-025 actualizado.', NULL, '2026-07-23 01:03:20', '2026-07-23 01:03:20'),
(291, 2, 'App\\Models\\RecurringService', 27, 'service_created', 'Servicio SRV-027 dado de alta.', NULL, '2026-07-23 01:05:40', '2026-07-23 01:05:40'),
(292, 2, 'App\\Models\\RecurringService', 27, 'service_updated', 'Servicio SRV-027 actualizado.', NULL, '2026-07-23 01:05:50', '2026-07-23 01:05:50'),
(293, 2, 'App\\Models\\RecurringService', 27, 'service_support_loaded', 'Recibo cargado para SRV-027 periodo 2026-07-18.', NULL, '2026-07-23 01:07:28', '2026-07-23 01:07:28'),
(294, 7, 'App\\Models\\PurchaseOrder', 32, 'receipt_completed', 'Recepcion completada: cantidades recibidas coinciden con la OC.', NULL, '2026-07-23 01:07:47', '2026-07-23 01:07:47'),
(295, 2, 'App\\Models\\RecurringService', 25, 'service_support_loaded', 'Recibo cargado para SRV-025 periodo 2026-07-16.', NULL, '2026-07-23 01:08:10', '2026-07-23 01:08:10'),
(296, 2, 'App\\Models\\RecurringService', 28, 'service_created', 'Servicio SRV-028 dado de alta.', NULL, '2026-07-23 01:10:55', '2026-07-23 01:10:55'),
(297, 2, 'App\\Models\\RecurringService', 28, 'service_support_loaded', 'Recibo cargado para SRV-028 periodo 2026-07-02.', NULL, '2026-07-23 01:11:36', '2026-07-23 01:11:36'),
(298, 2, 'App\\Models\\RecurringService', 29, 'service_created', 'Servicio SRV-029 dado de alta.', NULL, '2026-07-23 01:12:53', '2026-07-23 01:12:53'),
(299, 2, 'App\\Models\\RecurringService', 29, 'service_support_loaded', 'Recibo cargado para SRV-029 periodo 2026-07-02.', NULL, '2026-07-23 01:13:24', '2026-07-23 01:13:24'),
(300, 3, 'App\\Models\\Company', 23, 'company_created', 'Empresa Durexa, S.A. de C.V. creada por Finanzas.', NULL, '2026-07-23 01:28:18', '2026-07-23 01:28:18'),
(301, 2, 'App\\Models\\RecurringService', 30, 'service_created', 'Servicio SRV-030 dado de alta.', NULL, '2026-07-23 01:31:53', '2026-07-23 01:31:53'),
(302, 2, 'App\\Models\\RecurringService', 30, 'service_support_loaded', 'Recibo cargado para SRV-030 periodo 2026-07-02.', NULL, '2026-07-23 01:32:37', '2026-07-23 01:32:37'),
(303, 2, 'App\\Models\\RecurringService', 31, 'service_created', 'Servicio SRV-031 dado de alta.', NULL, '2026-07-23 01:34:22', '2026-07-23 01:34:22'),
(304, 2, 'App\\Models\\RecurringService', 31, 'service_support_loaded', 'Recibo cargado para SRV-031 periodo 2026-07-08.', NULL, '2026-07-23 01:34:57', '2026-07-23 01:34:57'),
(305, 2, 'App\\Models\\RecurringService', 32, 'service_created', 'Servicio SRV-032 dado de alta.', NULL, '2026-07-23 01:36:11', '2026-07-23 01:36:11'),
(306, 2, 'App\\Models\\RecurringService', 32, 'service_support_loaded', 'Recibo cargado para SRV-032 periodo 2026-07-29.', NULL, '2026-07-23 01:36:40', '2026-07-23 01:36:40'),
(307, 2, 'App\\Models\\RecurringService', 33, 'service_created', 'Servicio SRV-033 dado de alta.', NULL, '2026-07-23 01:37:59', '2026-07-23 01:37:59'),
(308, 2, 'App\\Models\\RecurringService', 33, 'service_support_loaded', 'Recibo cargado para SRV-033 periodo 2026-07-22.', NULL, '2026-07-23 01:38:29', '2026-07-23 01:38:29'),
(309, 2, 'App\\Models\\RecurringService', 34, 'service_created', 'Servicio SRV-034 dado de alta.', NULL, '2026-07-23 01:39:23', '2026-07-23 01:39:23'),
(310, 2, 'App\\Models\\RecurringService', 34, 'service_support_loaded', 'Recibo cargado para SRV-034 periodo 2026-07-28.', NULL, '2026-07-23 01:40:07', '2026-07-23 01:40:07'),
(311, 2, 'App\\Models\\RecurringService', 24, 'service_support_loaded', 'Recibo cargado para SRV-024 periodo 2026-07-27.', NULL, '2026-07-23 01:40:40', '2026-07-23 01:40:40'),
(312, 2, 'App\\Models\\RecurringService', 35, 'service_created', 'Servicio SRV-035 dado de alta.', NULL, '2026-07-23 01:41:36', '2026-07-23 01:41:36');
INSERT INTO `audit_logs` (`id`, `user_id`, `auditable_type`, `auditable_id`, `action`, `description`, `metadata`, `created_at`, `updated_at`) VALUES
(313, 2, 'App\\Models\\RecurringService', 35, 'service_support_loaded', 'Recibo cargado para SRV-035 periodo 2026-07-11.', NULL, '2026-07-23 01:42:03', '2026-07-23 01:42:03'),
(314, 2, 'App\\Models\\RecurringService', 36, 'service_created', 'Servicio SRV-036 dado de alta.', NULL, '2026-07-23 01:44:08', '2026-07-23 01:44:08'),
(315, 2, 'App\\Models\\RecurringService', 36, 'service_support_loaded', 'Recibo cargado para SRV-036 periodo 2026-07-30.', NULL, '2026-07-23 01:44:38', '2026-07-23 01:44:38'),
(316, 2, 'App\\Models\\RecurringService', 36, 'service_support_loaded', 'Recibo cargado para SRV-036 periodo 2026-07-30.', NULL, '2026-07-23 01:44:41', '2026-07-23 01:44:41'),
(317, 2, 'App\\Models\\RecurringService', 37, 'service_created', 'Servicio SRV-037 dado de alta.', NULL, '2026-07-23 01:45:47', '2026-07-23 01:45:47'),
(318, 2, 'App\\Models\\RecurringService', 37, 'service_support_loaded', 'Recibo cargado para SRV-037 periodo 2026-07-03.', NULL, '2026-07-23 01:46:21', '2026-07-23 01:46:21'),
(319, 3, 'App\\Models\\Company', 24, 'company_created', 'Empresa Consurent, S.A. de C.V. creada por Finanzas.', NULL, '2026-07-23 02:07:36', '2026-07-23 02:07:36'),
(320, 2, 'App\\Models\\RecurringService', 38, 'service_created', 'Servicio SRV-038 dado de alta.', NULL, '2026-07-23 02:09:36', '2026-07-23 02:09:36'),
(321, 2, 'App\\Models\\RecurringService', 38, 'service_support_loaded', 'Recibo cargado para SRV-038 periodo 2026-07-28.', NULL, '2026-07-23 02:10:00', '2026-07-23 02:10:00'),
(322, 2, 'App\\Models\\RecurringService', 39, 'service_created', 'Servicio SRV-039 dado de alta.', NULL, '2026-07-23 02:11:47', '2026-07-23 02:11:47'),
(323, 2, 'App\\Models\\RecurringService', 39, 'service_support_loaded', 'Recibo cargado para SRV-039 periodo 2026-07-26.', NULL, '2026-07-23 02:12:47', '2026-07-23 02:12:47'),
(324, 2, 'App\\Models\\RecurringService', 38, 'service_updated', 'Servicio SRV-038 actualizado.', NULL, '2026-07-23 02:13:56', '2026-07-23 02:13:56'),
(325, 3, 'App\\Models\\RecurringService', 35, 'service_paid', 'Pago registrado para SRV-035 periodo 2026-07-11.', NULL, '2026-07-23 02:14:20', '2026-07-23 02:14:20'),
(326, 2, 'App\\Models\\RecurringService', 38, 'service_updated', 'Servicio SRV-038 actualizado.', NULL, '2026-07-23 02:15:47', '2026-07-23 02:15:47'),
(327, 2, 'App\\Models\\RecurringService', 40, 'service_created', 'Servicio SRV-040 dado de alta.', NULL, '2026-07-23 02:16:59', '2026-07-23 02:16:59'),
(328, 3, 'App\\Models\\RecurringService', 28, 'service_paid', 'Pago registrado para SRV-028 periodo 2026-07-02.', NULL, '2026-07-23 02:18:19', '2026-07-23 02:18:19'),
(329, 2, 'App\\Models\\RecurringService', 40, 'service_updated', 'Servicio SRV-040 actualizado.', NULL, '2026-07-23 04:14:13', '2026-07-23 04:14:13'),
(330, 2, 'App\\Models\\RecurringService', 39, 'service_updated', 'Servicio SRV-039 actualizado.', NULL, '2026-07-23 04:15:12', '2026-07-23 04:15:12'),
(331, 2, 'App\\Models\\RecurringService', 38, 'service_updated', 'Servicio SRV-038 actualizado.', NULL, '2026-07-23 04:16:52', '2026-07-23 04:16:52'),
(332, 2, 'App\\Models\\RecurringService', 37, 'service_updated', 'Servicio SRV-037 actualizado.', NULL, '2026-07-23 04:17:26', '2026-07-23 04:17:26'),
(333, 2, 'App\\Models\\RecurringService', 36, 'service_updated', 'Servicio SRV-036 actualizado.', NULL, '2026-07-23 04:17:52', '2026-07-23 04:17:52'),
(334, 2, 'App\\Models\\RecurringService', 36, 'service_updated', 'Servicio SRV-036 actualizado.', NULL, '2026-07-23 04:18:17', '2026-07-23 04:18:17'),
(335, 2, 'App\\Models\\RecurringService', 35, 'service_updated', 'Servicio SRV-035 actualizado.', NULL, '2026-07-23 04:18:42', '2026-07-23 04:18:42'),
(336, 2, 'App\\Models\\RecurringService', 34, 'service_updated', 'Servicio SRV-034 actualizado.', NULL, '2026-07-23 04:19:06', '2026-07-23 04:19:06'),
(337, 2, 'App\\Models\\RecurringService', 33, 'service_updated', 'Servicio SRV-033 actualizado.', NULL, '2026-07-23 04:19:22', '2026-07-23 04:19:22'),
(338, 2, 'App\\Models\\RecurringService', 33, 'service_updated', 'Servicio SRV-033 actualizado.', NULL, '2026-07-23 04:19:39', '2026-07-23 04:19:39'),
(339, 2, 'App\\Models\\RecurringService', 33, 'service_updated', 'Servicio SRV-033 actualizado.', NULL, '2026-07-23 04:19:51', '2026-07-23 04:19:51'),
(340, 2, 'App\\Models\\RecurringService', 32, 'service_updated', 'Servicio SRV-032 actualizado.', NULL, '2026-07-23 04:20:43', '2026-07-23 04:20:43'),
(341, 2, 'App\\Models\\RecurringService', 31, 'service_updated', 'Servicio SRV-031 actualizado.', NULL, '2026-07-23 04:21:13', '2026-07-23 04:21:13'),
(342, 2, 'App\\Models\\RecurringService', 30, 'service_updated', 'Servicio SRV-030 actualizado.', NULL, '2026-07-23 04:21:58', '2026-07-23 04:21:58'),
(343, 2, 'App\\Models\\RecurringService', 29, 'service_updated', 'Servicio SRV-029 actualizado.', NULL, '2026-07-23 04:22:35', '2026-07-23 04:22:35'),
(344, 2, 'App\\Models\\RecurringService', 28, 'service_updated', 'Servicio SRV-028 actualizado.', NULL, '2026-07-23 04:23:23', '2026-07-23 04:23:23'),
(345, 2, 'App\\Models\\RecurringService', 27, 'service_updated', 'Servicio SRV-027 actualizado.', NULL, '2026-07-23 04:24:00', '2026-07-23 04:24:00'),
(346, 2, 'App\\Models\\RecurringService', 28, 'service_updated', 'Servicio SRV-028 actualizado.', NULL, '2026-07-23 04:24:15', '2026-07-23 04:24:15'),
(347, 2, 'App\\Models\\RecurringService', 1, 'service_updated', 'Servicio SRV-001 actualizado.', NULL, '2026-07-23 04:28:29', '2026-07-23 04:28:29'),
(348, 2, 'App\\Models\\RecurringService', 24, 'service_updated', 'Servicio SRV-024 actualizado.', NULL, '2026-07-23 04:30:27', '2026-07-23 04:30:27'),
(349, 2, 'App\\Models\\RecurringService', 25, 'service_updated', 'Servicio SRV-025 actualizado.', NULL, '2026-07-23 04:32:49', '2026-07-23 04:32:49'),
(350, 2, 'App\\Models\\RecurringService', 24, 'service_updated', 'Servicio SRV-024 actualizado.', NULL, '2026-07-23 04:33:15', '2026-07-23 04:33:15'),
(351, 2, 'App\\Models\\RecurringService', 23, 'service_updated', 'Servicio SRV-023 actualizado.', NULL, '2026-07-23 04:34:32', '2026-07-23 04:34:32'),
(352, 2, 'App\\Models\\RecurringService', 22, 'service_updated', 'Servicio SRV-022 actualizado.', NULL, '2026-07-23 04:35:01', '2026-07-23 04:35:01'),
(353, 2, 'App\\Models\\RecurringService', 21, 'service_updated', 'Servicio SRV-021 actualizado.', NULL, '2026-07-23 04:35:21', '2026-07-23 04:35:21'),
(354, 2, 'App\\Models\\RecurringService', 21, 'service_updated', 'Servicio SRV-021 actualizado.', NULL, '2026-07-23 04:36:15', '2026-07-23 04:36:15'),
(355, 2, 'App\\Models\\RecurringService', 19, 'service_updated', 'Servicio SRV-019 actualizado.', NULL, '2026-07-23 04:37:05', '2026-07-23 04:37:05'),
(356, 2, 'App\\Models\\RecurringService', 15, 'service_updated', 'Servicio SRV-015 actualizado.', NULL, '2026-07-23 04:38:36', '2026-07-23 04:38:36'),
(357, 2, 'App\\Models\\RecurringService', 17, 'service_updated', 'Servicio SRV-017 actualizado.', NULL, '2026-07-23 04:39:13', '2026-07-23 04:39:13'),
(358, 2, 'App\\Models\\RecurringService', 12, 'service_updated', 'Servicio SRV-012 actualizado.', NULL, '2026-07-23 04:43:21', '2026-07-23 04:43:21'),
(359, 2, 'App\\Models\\RecurringService', 13, 'service_updated', 'Servicio SRV-013 actualizado.', NULL, '2026-07-23 04:44:11', '2026-07-23 04:44:11'),
(360, 2, 'App\\Models\\RecurringService', 11, 'service_updated', 'Servicio SRV-011 actualizado.', NULL, '2026-07-23 04:45:15', '2026-07-23 04:45:15'),
(361, 3, 'App\\Models\\Company', 25, 'company_created', 'Empresa Distrilux, S.A. de C.V. creada por Finanzas.', NULL, '2026-07-23 04:48:37', '2026-07-23 04:48:37'),
(362, 2, 'App\\Models\\RecurringService', 41, 'service_created', 'Servicio SRV-041 dado de alta.', NULL, '2026-07-23 04:56:18', '2026-07-23 04:56:18'),
(363, 2, 'App\\Models\\RecurringService', 42, 'service_created', 'Servicio SRV-042 dado de alta.', NULL, '2026-07-23 04:56:18', '2026-07-23 04:56:18'),
(364, 2, 'App\\Models\\RecurringService', 41, 'service_support_loaded', 'Recibo cargado para SRV-041 periodo 2026-07-27.', NULL, '2026-07-23 04:57:08', '2026-07-23 04:57:08'),
(365, 3, 'App\\Models\\Company', 26, 'company_created', 'Empresa Sorem, S.A. de C.V. creada por Finanzas.', NULL, '2026-07-23 05:04:40', '2026-07-23 05:04:40'),
(366, 2, 'App\\Models\\RecurringService', 41, 'service_updated', 'Servicio SRV-041 actualizado.', NULL, '2026-07-23 05:07:34', '2026-07-23 05:07:34'),
(367, 2, 'App\\Models\\RecurringService', 42, 'service_updated', 'Servicio SRV-042 actualizado.', NULL, '2026-07-23 05:07:44', '2026-07-23 05:07:44'),
(368, 2, 'App\\Models\\RecurringService', 41, 'service_support_loaded', 'Recibo cargado para SRV-041 periodo 2026-07-27.', NULL, '2026-07-23 05:08:33', '2026-07-23 05:08:33'),
(369, 2, 'App\\Models\\RecurringService', 42, 'service_support_loaded', 'Recibo cargado para SRV-042 periodo 2026-07-27.', NULL, '2026-07-23 05:09:05', '2026-07-23 05:09:05'),
(370, 2, 'App\\Models\\RecurringService', 40, 'service_support_loaded', 'Recibo cargado para SRV-040 periodo 2026-07-28.', NULL, '2026-07-23 05:10:48', '2026-07-23 05:10:48'),
(371, 2, 'App\\Models\\RecurringService', 41, 'service_updated', 'Servicio SRV-041 actualizado.', NULL, '2026-07-23 05:11:44', '2026-07-23 05:11:44'),
(372, 6, 'App\\Models\\PurchaseOrder', 48, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(373, 6, 'App\\Models\\Provider', 27, 'provider_created', 'Proveedor Daniel García Vergara dado de alta por comprador.', NULL, '2026-07-23 07:15:40', '2026-07-23 07:15:40'),
(374, 6, 'App\\Models\\PurchaseOrder', 49, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-23 07:17:03', '2026-07-23 07:17:03'),
(375, 2, 'App\\Models\\RecurringService', 43, 'service_created', 'Servicio SRV-043 dado de alta.', NULL, '2026-07-23 07:24:33', '2026-07-23 07:24:33'),
(376, 2, 'App\\Models\\RecurringService', 44, 'service_created', 'Servicio SRV-044 dado de alta.', NULL, '2026-07-23 07:29:47', '2026-07-23 07:29:47'),
(377, 2, 'App\\Models\\RecurringService', 43, 'service_support_loaded', 'Recibo cargado para SRV-043 periodo 2026-07-23.', NULL, '2026-07-23 07:33:08', '2026-07-23 07:33:08'),
(378, 2, 'App\\Models\\RecurringService', 45, 'service_created', 'Servicio SRV-045 dado de alta.', NULL, '2026-07-23 07:43:43', '2026-07-23 07:43:43'),
(379, 3, 'App\\Models\\RecurringService', 42, 'service_paid', 'Pago registrado para SRV-042 periodo 2026-07-27.', NULL, '2026-07-23 23:20:01', '2026-07-23 23:20:01'),
(380, 2, 'App\\Models\\RecurringService', 10, 'service_updated', 'Servicio SRV-010 actualizado.', NULL, '2026-07-24 00:14:58', '2026-07-24 00:14:58'),
(381, 2, 'App\\Models\\RecurringService', 18, 'service_updated', 'Servicio SRV-018 actualizado.', NULL, '2026-07-24 00:15:27', '2026-07-24 00:15:27'),
(382, 2, 'App\\Models\\RecurringService', 20, 'service_updated', 'Servicio SRV-020 actualizado.', NULL, '2026-07-24 00:15:51', '2026-07-24 00:15:51'),
(383, 2, 'App\\Models\\RecurringService', 46, 'service_created', 'Servicio SRV-046 dado de alta.', NULL, '2026-07-24 00:28:39', '2026-07-24 00:28:39'),
(384, 2, 'App\\Models\\RecurringService', 46, 'service_support_loaded', 'Recibo cargado para SRV-046 periodo 2026-07-16.', NULL, '2026-07-24 00:29:01', '2026-07-24 00:29:01'),
(385, 5, 'App\\Models\\Provider', 28, 'provider_created', 'Proveedor SALUCOM S.A DE C.V. dado de alta por comprador.', NULL, '2026-07-24 00:47:11', '2026-07-24 00:47:11'),
(386, 5, 'App\\Models\\PurchaseOrder', 50, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-24 00:48:09', '2026-07-24 00:48:09'),
(387, 2, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 00:48:14', '2026-07-24 00:48:14'),
(388, 2, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 00:49:38', '2026-07-24 00:49:38'),
(389, 2, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 00:50:01', '2026-07-24 00:50:01'),
(390, 1, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 00:50:15', '2026-07-24 00:50:15'),
(391, 2, 'App\\Models\\RecurringService', 20, 'service_updated', 'Servicio SRV-020 actualizado.', NULL, '2026-07-24 00:53:17', '2026-07-24 00:53:17'),
(392, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:39', '2026-07-24 00:54:39'),
(393, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:40', '2026-07-24 00:54:40'),
(394, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:40', '2026-07-24 00:54:40'),
(395, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:40', '2026-07-24 00:54:40'),
(396, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:42', '2026-07-24 00:54:42'),
(397, 1, 'App\\Models\\User', 5, 'superadmin_user_updated', 'Usuario gcortesm@prodifem.com.mx actualizado por Super Administrador.', NULL, '2026-07-24 00:54:43', '2026-07-24 00:54:43'),
(398, 1, 'App\\Models\\User', 7, 'superadmin_user_updated', 'Usuario recepcion.0918@gmail.com actualizado por Super Administrador.', NULL, '2026-07-24 00:55:17', '2026-07-24 00:55:17'),
(399, 6, 'App\\Models\\PurchaseOrder', 51, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-24 01:04:30', '2026-07-24 01:04:30'),
(400, 5, 'App\\Models\\PurchaseOrder', 52, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-24 01:08:34', '2026-07-24 01:08:34'),
(401, 2, 'App\\Models\\RecurringService', 47, 'service_created', 'Servicio SRV-047 dado de alta.', NULL, '2026-07-24 01:28:52', '2026-07-24 01:28:52'),
(402, 2, 'App\\Models\\RecurringService', 48, 'service_created', 'Servicio SRV-048 dado de alta.', NULL, '2026-07-24 01:38:06', '2026-07-24 01:38:06'),
(403, 2, 'App\\Models\\RecurringService', 48, 'service_support_loaded', 'Recibo cargado para SRV-048 periodo 2026-07-14.', NULL, '2026-07-24 01:38:25', '2026-07-24 01:38:25'),
(404, 5, 'App\\Models\\Provider', 29, 'provider_created', 'Proveedor OVERPHARMA dado de alta por comprador.', NULL, '2026-07-24 01:41:25', '2026-07-24 01:41:25'),
(405, 5, 'App\\Models\\PurchaseOrder', 53, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-24 01:43:42', '2026-07-24 01:43:42'),
(406, 5, 'App\\Models\\Provider', 30, 'provider_created', 'Proveedor FRESENIUS KABI MEXICO S.A. DE C.V. dado de alta por comprador.', NULL, '2026-07-24 02:21:01', '2026-07-24 02:21:01'),
(407, 2, 'App\\Models\\RecurringService', 49, 'service_created', 'Servicio SRV-049 dado de alta.', NULL, '2026-07-24 03:44:35', '2026-07-24 03:44:35'),
(408, 1, 'App\\Models\\RecurringService', 48, 'service_updated', 'Servicio SRV-048 actualizado.', NULL, '2026-07-24 03:44:49', '2026-07-24 03:44:49'),
(409, 2, 'App\\Models\\RecurringService', 49, 'service_support_loaded', 'Recibo cargado para SRV-049 periodo 2026-07-28.', NULL, '2026-07-24 03:45:15', '2026-07-24 03:45:15'),
(410, 2, 'App\\Models\\RecurringService', 44, 'service_support_loaded', 'Recibo cargado para SRV-044 periodo 2026-07-23.', NULL, '2026-07-24 03:46:16', '2026-07-24 03:46:16'),
(411, 1, 'App\\Models\\RecurringService', 49, 'service_updated', 'Servicio SRV-049 actualizado.', NULL, '2026-07-24 03:49:52', '2026-07-24 03:49:52'),
(412, 1, 'App\\Models\\RecurringService', 49, 'service_updated', 'Servicio SRV-049 actualizado.', NULL, '2026-07-24 03:49:59', '2026-07-24 03:49:59'),
(413, 3, 'App\\Models\\PurchaseOrder', 50, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:41:48', '2026-07-24 04:41:48'),
(414, 3, 'App\\Models\\PurchaseOrder', 49, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:41:56', '2026-07-24 04:41:56'),
(415, 3, 'App\\Models\\PurchaseOrder', 52, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:42:01', '2026-07-24 04:42:01'),
(416, 3, 'App\\Models\\PurchaseOrder', 53, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:42:05', '2026-07-24 04:42:05'),
(417, 3, 'App\\Models\\PurchaseOrder', 48, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:42:12', '2026-07-24 04:42:12'),
(418, 3, 'App\\Models\\PurchaseOrder', 51, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 04:42:18', '2026-07-24 04:42:18'),
(419, 3, 'App\\Models\\PurchaseOrder', 50, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:42:54', '2026-07-24 04:42:54'),
(420, 3, 'App\\Models\\PurchaseOrder', 52, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:43:24', '2026-07-24 04:43:24'),
(421, 3, 'App\\Models\\PurchaseOrder', 53, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:43:44', '2026-07-24 04:43:44'),
(422, 3, 'App\\Models\\PurchaseOrder', 48, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:44:10', '2026-07-24 04:44:10'),
(423, 3, 'App\\Models\\PurchaseOrder', 51, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:48:34', '2026-07-24 04:48:34'),
(424, 3, 'App\\Models\\PurchaseOrder', 49, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 04:51:33', '2026-07-24 04:51:33'),
(425, 1, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 05:05:40', '2026-07-24 05:05:40'),
(426, 1, 'App\\Models\\Company', 2, 'company_updated', 'Empresa Farmasoma S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-24 05:11:23', '2026-07-24 05:11:23'),
(427, 1, 'App\\Models\\Company', 2, 'company_updated', 'Empresa Farmasoma S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-24 05:16:45', '2026-07-24 05:16:45'),
(428, 5, 'App\\Models\\PurchaseOrder', 54, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-24 05:22:23', '2026-07-24 05:22:23'),
(429, 3, 'App\\Models\\RecurringService', 27, 'service_paid', 'Pago registrado para SRV-027 periodo 2026-07-18.', NULL, '2026-07-24 06:38:29', '2026-07-24 06:38:29'),
(430, 3, 'App\\Models\\RecurringService', 48, 'service_paid', 'Pago registrado para SRV-048 periodo 2026-07-14.', NULL, '2026-07-24 06:40:32', '2026-07-24 06:40:32'),
(431, 3, 'App\\Models\\RecurringService', 34, 'service_paid', 'Pago registrado para SRV-034 periodo 2026-07-28.', NULL, '2026-07-24 06:45:07', '2026-07-24 06:45:07'),
(432, 3, 'App\\Models\\RecurringService', 29, 'service_paid', 'Pago registrado para SRV-029 periodo 2026-07-02.', NULL, '2026-07-24 06:46:05', '2026-07-24 06:46:05'),
(433, 3, 'App\\Models\\RecurringService', 30, 'service_paid', 'Pago registrado para SRV-030 periodo 2026-07-02.', NULL, '2026-07-24 06:46:57', '2026-07-24 06:46:57'),
(434, 3, 'App\\Models\\RecurringService', 37, 'service_paid', 'Pago registrado para SRV-037 periodo 2026-07-03.', NULL, '2026-07-24 06:47:32', '2026-07-24 06:47:32'),
(435, 3, 'App\\Models\\RecurringService', 25, 'service_paid', 'Pago registrado para SRV-025 periodo 2026-07-16.', NULL, '2026-07-24 06:48:32', '2026-07-24 06:48:32'),
(436, 3, 'App\\Models\\RecurringService', 33, 'service_paid', 'Pago registrado para SRV-033 periodo 2026-07-22.', NULL, '2026-07-24 06:49:23', '2026-07-24 06:49:23'),
(437, 3, 'App\\Models\\RecurringService', 43, 'service_paid', 'Pago registrado para SRV-043 periodo 2026-07-23.', NULL, '2026-07-24 06:49:58', '2026-07-24 06:49:58'),
(438, 3, 'App\\Models\\RecurringService', 39, 'service_paid', 'Pago registrado para SRV-039 periodo 2026-07-26.', NULL, '2026-07-24 06:50:48', '2026-07-24 06:50:48'),
(439, 3, 'App\\Models\\RecurringService', 38, 'service_paid', 'Pago registrado para SRV-038 periodo 2026-07-28.', NULL, '2026-07-24 06:52:01', '2026-07-24 06:52:01'),
(440, 3, 'App\\Models\\RecurringService', 40, 'service_paid', 'Pago registrado para SRV-040 periodo 2026-07-28.', NULL, '2026-07-24 06:52:59', '2026-07-24 06:52:59'),
(441, 3, 'App\\Models\\RecurringService', 8, 'service_paid', 'Pago registrado para SRV-008 periodo 2026-07-29.', NULL, '2026-07-24 06:53:58', '2026-07-24 06:53:58'),
(442, 3, 'App\\Models\\RecurringService', 36, 'service_paid', 'Pago registrado para SRV-036 periodo 2026-07-30.', NULL, '2026-07-24 06:56:54', '2026-07-24 06:56:54'),
(443, 2, 'App\\Models\\RecurringService', 49, 'service_updated', 'Servicio SRV-049 actualizado.', NULL, '2026-07-24 07:17:23', '2026-07-24 07:17:23'),
(444, 3, 'App\\Models\\PurchaseOrder', 54, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-24 07:44:12', '2026-07-24 07:44:12'),
(445, 2, 'App\\Models\\RecurringService', 7, 'service_updated', 'Servicio SRV-007 actualizado.', NULL, '2026-07-24 07:46:32', '2026-07-24 07:46:32'),
(446, 3, 'App\\Models\\PurchaseOrder', 54, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-24 07:48:09', '2026-07-24 07:48:09'),
(447, 2, 'App\\Models\\RecurringService', 21, 'service_support_loaded', 'Recibo cargado para SRV-021 periodo 2026-07-30.', NULL, '2026-07-24 07:49:22', '2026-07-24 07:49:22'),
(448, 2, 'App\\Models\\RecurringService', 22, 'service_support_loaded', 'Recibo cargado para SRV-022 periodo 2026-07-30.', NULL, '2026-07-24 07:50:55', '2026-07-24 07:50:55'),
(449, 2, 'App\\Models\\RecurringService', 23, 'service_support_loaded', 'Recibo cargado para SRV-023 periodo 2026-07-30.', NULL, '2026-07-24 07:52:25', '2026-07-24 07:52:25'),
(450, 2, 'App\\Models\\RecurringService', 27, 'service_updated', 'Servicio SRV-027 actualizado.', NULL, '2026-07-24 07:53:22', '2026-07-24 07:53:22'),
(451, 2, 'App\\Models\\RecurringService', 27, 'service_updated', 'Servicio SRV-027 actualizado.', NULL, '2026-07-24 07:54:25', '2026-07-24 07:54:25'),
(452, 2, 'App\\Models\\RecurringService', 49, 'service_updated', 'Servicio SRV-049 actualizado.', NULL, '2026-07-24 07:55:07', '2026-07-24 07:55:07'),
(453, 2, 'App\\Models\\RecurringService', 48, 'service_updated', 'Servicio SRV-048 actualizado.', NULL, '2026-07-24 07:55:24', '2026-07-24 07:55:24'),
(454, 2, 'App\\Models\\RecurringService', 47, 'service_updated', 'Servicio SRV-047 actualizado.', NULL, '2026-07-24 07:57:26', '2026-07-24 07:57:26'),
(455, 2, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 07:57:41', '2026-07-24 07:57:41'),
(456, 2, 'App\\Models\\RecurringService', 46, 'service_updated', 'Servicio SRV-046 actualizado.', NULL, '2026-07-24 07:58:00', '2026-07-24 07:58:00'),
(457, 2, 'App\\Models\\RecurringService', 45, 'service_updated', 'Servicio SRV-045 actualizado.', NULL, '2026-07-24 07:58:38', '2026-07-24 07:58:38'),
(458, 2, 'App\\Models\\RecurringService', 45, 'service_updated', 'Servicio SRV-045 actualizado.', NULL, '2026-07-24 08:03:05', '2026-07-24 08:03:05'),
(459, 2, 'App\\Models\\RecurringService', 45, 'service_updated', 'Servicio SRV-045 actualizado.', NULL, '2026-07-24 08:03:20', '2026-07-24 08:03:20'),
(460, 2, 'App\\Models\\RecurringService', 44, 'service_updated', 'Servicio SRV-044 actualizado.', NULL, '2026-07-24 08:03:44', '2026-07-24 08:03:44'),
(461, 2, 'App\\Models\\RecurringService', 43, 'service_updated', 'Servicio SRV-043 actualizado.', NULL, '2026-07-24 08:04:10', '2026-07-24 08:04:10'),
(462, 2, 'App\\Models\\RecurringService', 41, 'service_updated', 'Servicio SRV-041 actualizado.', NULL, '2026-07-24 08:04:37', '2026-07-24 08:04:37'),
(463, 2, 'App\\Models\\RecurringService', 42, 'service_updated', 'Servicio SRV-042 actualizado.', NULL, '2026-07-24 08:04:48', '2026-07-24 08:04:48'),
(464, 2, 'App\\Models\\RecurringService', 40, 'service_updated', 'Servicio SRV-040 actualizado.', NULL, '2026-07-24 08:05:03', '2026-07-24 08:05:03'),
(465, 2, 'App\\Models\\RecurringService', 39, 'service_updated', 'Servicio SRV-039 actualizado.', NULL, '2026-07-24 08:05:20', '2026-07-24 08:05:20'),
(466, 2, 'App\\Models\\RecurringService', 38, 'service_updated', 'Servicio SRV-038 actualizado.', NULL, '2026-07-24 08:05:30', '2026-07-24 08:05:30'),
(467, 2, 'App\\Models\\RecurringService', 37, 'service_updated', 'Servicio SRV-037 actualizado.', NULL, '2026-07-24 08:05:45', '2026-07-24 08:05:45'),
(468, 3, 'App\\Models\\RecurringService', 49, 'service_paid', 'Pago registrado para SRV-049 periodo 2026-07-28.', NULL, '2026-07-24 08:07:48', '2026-07-24 08:07:48'),
(469, 3, 'App\\Models\\RecurringService', 21, 'service_paid', 'Pago registrado para SRV-021 periodo 2026-07-30.', NULL, '2026-07-24 08:11:14', '2026-07-24 08:11:14'),
(470, 3, 'App\\Models\\RecurringService', 22, 'service_paid', 'Pago registrado para SRV-022 periodo 2026-07-30.', NULL, '2026-07-24 08:11:36', '2026-07-24 08:11:36'),
(471, 3, 'App\\Models\\RecurringService', 23, 'service_paid', 'Pago registrado para SRV-023 periodo 2026-07-30.', NULL, '2026-07-24 08:12:20', '2026-07-24 08:12:20'),
(472, 2, 'App\\Models\\RecurringService', 44, 'service_updated', 'Servicio SRV-044 actualizado.', NULL, '2026-07-24 22:42:05', '2026-07-24 22:42:05'),
(473, 2, 'App\\Models\\RecurringService', 42, 'service_support_loaded', 'Recibo cargado para SRV-042 periodo 2026-07-05.', NULL, '2026-07-24 23:13:39', '2026-07-24 23:13:39'),
(474, 3, 'App\\Models\\PurchaseOrder', 52, 'payment_replaced', 'Comprobante de pago reemplazado por Finanzas.', NULL, '2026-07-24 23:19:33', '2026-07-24 23:19:33'),
(475, 4, 'App\\Models\\Provider', 31, 'provider_created', 'Proveedor NOE FLORES VARGAS dado de alta por comprador.', NULL, '2026-07-25 01:09:43', '2026-07-25 01:09:43'),
(476, 3, 'App\\Models\\RecurringService', 24, 'service_paid', 'Pago registrado para SRV-024 periodo 2026-07-27.', NULL, '2026-07-25 01:13:03', '2026-07-25 01:13:03'),
(477, 4, 'App\\Models\\PurchaseOrder', 55, 'sent', 'OC enviada a Finanzas para revision.', NULL, '2026-07-25 01:13:36', '2026-07-25 01:13:36'),
(478, 4, 'App\\Models\\Provider', 31, 'provider_updated', 'Proveedor NOE FLORES VARGAS actualizado por comprador.', NULL, '2026-07-25 01:15:39', '2026-07-25 01:15:39'),
(479, 3, 'App\\Models\\PurchaseOrder', 55, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.', NULL, '2026-07-25 01:25:30', '2026-07-25 01:25:30'),
(480, 3, 'App\\Models\\User', 7, 'user_updated', 'Usuario recepcion.0918@gmail.com actualizado por Finanzas.', NULL, '2026-07-25 01:50:35', '2026-07-25 01:50:35'),
(481, 3, 'App\\Models\\RecurringService', 32, 'service_paid', 'Pago registrado para SRV-032 periodo 2026-07-29.', NULL, '2026-07-25 01:55:06', '2026-07-25 01:55:06'),
(482, 3, 'App\\Models\\RecurringService', 12, 'service_paid', 'Pago registrado para SRV-012 periodo 2026-07-30.', NULL, '2026-07-25 01:58:02', '2026-07-25 01:58:02'),
(483, 3, 'App\\Models\\PurchaseOrder', 55, 'paid', 'Pago registrado con archivo adjunto.', NULL, '2026-07-25 03:21:24', '2026-07-25 03:21:24'),
(484, 2, 'App\\Models\\RecurringService', 44, 'service_support_loaded', 'Recibo cargado para SRV-044 periodo 2026-07-01.', NULL, '2026-07-25 03:24:24', '2026-07-25 03:24:24'),
(485, 2, 'App\\Models\\RecurringService', 46, 'service_support_loaded', 'Recibo cargado para SRV-046 periodo 2026-07-05.', NULL, '2026-07-25 03:25:33', '2026-07-25 03:25:33'),
(486, 2, 'App\\Models\\RecurringService', 7, 'service_updated', 'Servicio SRV-007 actualizado.', NULL, '2026-07-25 03:26:00', '2026-07-25 03:26:00'),
(487, 2, 'App\\Models\\RecurringService', 27, 'service_support_loaded', 'Recibo cargado para SRV-027 periodo 2026-07-05.', NULL, '2026-07-25 03:26:52', '2026-07-25 03:26:52'),
(488, 2, 'App\\Models\\RecurringService', 37, 'service_support_loaded', 'Recibo cargado para SRV-037 periodo 2026-07-05.', NULL, '2026-07-25 03:28:06', '2026-07-25 03:28:06'),
(489, 2, 'App\\Models\\RecurringService', 38, 'service_support_loaded', 'Recibo cargado para SRV-038 periodo 2026-07-05.', NULL, '2026-07-25 03:29:23', '2026-07-25 03:29:23'),
(490, 2, 'App\\Models\\RecurringService', 39, 'service_support_loaded', 'Recibo cargado para SRV-039 periodo 2026-07-05.', NULL, '2026-07-25 03:33:20', '2026-07-25 03:33:20'),
(491, 2, 'App\\Models\\RecurringService', 43, 'service_support_loaded', 'Recibo cargado para SRV-043 periodo 2026-07-05.', NULL, '2026-07-25 03:33:45', '2026-07-25 03:33:45'),
(492, 2, 'App\\Models\\RecurringService', 41, 'service_support_loaded', 'Recibo cargado para SRV-041 periodo 2026-07-05.', NULL, '2026-07-25 03:35:29', '2026-07-25 03:35:29'),
(493, 2, 'App\\Models\\RecurringService', 45, 'service_support_loaded', 'Recibo cargado para SRV-045 periodo 2026-07-05.', NULL, '2026-07-25 03:37:05', '2026-07-25 03:37:05'),
(494, 2, 'App\\Models\\RecurringService', 47, 'service_support_loaded', 'Recibo cargado para SRV-047 periodo 2026-07-05.', NULL, '2026-07-25 03:37:32', '2026-07-25 03:37:32'),
(495, 2, 'App\\Models\\RecurringService', 48, 'service_support_loaded', 'Recibo cargado para SRV-048 periodo 2026-07-05.', NULL, '2026-07-25 03:38:48', '2026-07-25 03:38:48'),
(496, 1, 'App\\Models\\Company', 1, 'company_updated', 'Empresa Prodifem S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-25 04:42:03', '2026-07-25 04:42:03'),
(497, 1, 'App\\Models\\Company', 2, 'company_updated', 'Empresa Farmasoma S.A. de C.V. actualizada por Finanzas.', NULL, '2026-07-25 04:43:49', '2026-07-25 04:43:49'),
(498, 1, 'App\\Models\\Company', 9, 'company_updated', 'Empresa Sandra Paola Camacho Fonseca actualizada por Finanzas.', NULL, '2026-07-25 04:46:59', '2026-07-25 04:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfc` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `purchase_order_notes` text COLLATE utf8mb4_unicode_ci,
  `warehouses` json DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `rfc`, `address`, `purchase_order_notes`, `warehouses`, `logo_path`, `created_at`, `updated_at`) VALUES
(1, 'Prodifem S.A. de C.V.', 'PRO170214P96', 'San Francisco 524 C, Colonia del Valle, Alcaldia Benito Juarez, Ciudad de Mexico, 03100', NULL, '[{\"name\": \"San Francisco 524\", \"short_name\": \"\"}]', 'company-logos/eLgjKPQgGJXjj1owqIuM5BZT4Y1OFFvUWEY09uaF.png', '2026-07-03 04:14:24', '2026-07-25 09:29:45'),
(2, 'Farmasoma S.A. de C.V.', 'FAR190905BM4', 'Concepcion Beistegui 113-B, Colonia del Valle, Alcaldia Benito Juarez, Ciudad de Mexico, 03100', NULL, '[{\"name\": \"Concepcion Beistegui 113\", \"short_name\": \"\"}, {\"name\": \"Almacen Central (San Francisco 516)\", \"short_name\": \"\"}]', 'company-logos/sLXgv6pNXuCcxToisZg1IfGxu1QEGjsNnisfgghP.png', '2026-07-03 04:35:52', '2026-07-25 09:29:45'),
(3, 'Vidicron S.A. de C.V.', 'VID050728CT6', 'Carretera Mexico Toluca 3056, Colonia Cuajimalpa, Alcaldia Cuajimalpa, Ciudad de Mexico, 03100', NULL, NULL, NULL, '2026-07-03 04:37:23', '2026-07-03 04:37:23'),
(4, 'Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.', 'CBT131030NB7', 'San Francisco 524, Colonia del Valle, Alcaldia Benito Juarez, Ciudad de Mexico, 03100', NULL, NULL, NULL, '2026-07-03 04:38:55', '2026-07-03 04:38:55'),
(6, 'Gustavo Diaz Martinez', 'BIMG610413389', 'Dr. Atl 254, Colonia Santa Maria la Ribera, Alcaldia Cuauhtemoc, Ciudad de Mexico, 06400', NULL, NULL, NULL, '2026-07-03 04:42:03', '2026-07-03 04:42:03'),
(7, 'Findelz S.A. de C.V.', 'FIN201214F90', 'Carretera Mexico Toluca 3054, Colonia Cuajimalpa, Alcaldia Cuajimalpa Ciudad de Mexico, 05000', NULL, NULL, NULL, '2026-07-03 04:44:26', '2026-07-03 04:44:26'),
(8, 'Brimak S.A. de C.V.', 'BRI000912I77', 'Prolongacion Reforma 2752, Colonia Bosques de las Lomas, Cuajimalpa de Morelos, Ciudad de Mexico, 05120', NULL, NULL, NULL, '2026-07-03 04:45:31', '2026-07-03 04:45:49'),
(9, 'Sandra Paola Camacho Fonseca', 'CAFS040201C18', 'Secretaria de la Marina 538, Colonia Lomas del Chamizal, Alcaldia Cuajimalpa, Ciudad de Mexico, 05129', NULL, '[{\"name\": \"Adolfo Gurrion\", \"short_name\": \"\"}, {\"name\": \"Almacen Central (San Francisco 516)\", \"short_name\": \"\"}, {\"name\": \"Secretaria de Marina\", \"short_name\": \"\"}, {\"name\": \"Carretera Mexico Toluca\", \"short_name\": \"\"}, {\"name\": \"Lerma\", \"short_name\": \"\"}]', NULL, '2026-07-03 05:19:44', '2026-07-25 09:29:45'),
(10, 'Grilsa S.A. de C.V.', 'GRI000524QC9', 'Calle: Dr. Atl. No. 254, Col. Santa María la Ribera, CP: 06400, Ciudad de México,\r\nCuauhtémoc, Ciudad de México, México', NULL, NULL, NULL, '2026-07-10 00:24:24', '2026-07-10 00:24:24'),
(11, 'Denise Medina Medina', 'MEMD780716RR2', 'Carretera México Toluca #3056, Col. Cuajimalpa, Cuajimalpa de Morelos, CDMX, c.p. 05000', NULL, NULL, NULL, '2026-07-10 06:56:25', '2026-07-10 06:56:50'),
(12, 'Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V.', 'SSM250307LY1', 'Concepción Béistegui #113, Col. Del Valle, Benito Juárez, CDMX, c.p. 03100', NULL, NULL, NULL, '2026-07-11 03:38:22', '2026-07-11 03:38:22'),
(13, 'Clínica Pediátrica de Endocrinología S.A. de C.V.', 'CPE110304N65', 'Guanajuato #131 int. 2, Col. Roma Norte, Cuauhtémoc, CDMX, c.p.06700', NULL, NULL, NULL, '2026-07-11 03:40:54', '2026-07-11 03:40:54'),
(14, 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'CPC0708308X1', 'Alabama 153 casa 1, Col. Nápoles, Benito Juárez, CDMX, c.p.03810', NULL, NULL, NULL, '2026-07-11 04:03:53', '2026-07-11 04:03:53'),
(15, 'Sociedad Pediátrica Denla S.A. de C.V.', 'PDE081017849', 'Indiana #137, Col. Napoles Ampliación, Benito juárez, CDMX, c.p.03810', NULL, NULL, NULL, '2026-07-11 05:07:46', '2026-07-11 05:07:46'),
(16, 'Biozig S.A. de C.V.', 'BIO240408HR2', 'Concepción Beístegui #113 A, Col. Del Valle, Benito Juárez, CDMX, c.p. 03100.', NULL, NULL, NULL, '2026-07-14 01:52:53', '2026-07-14 01:52:53'),
(17, 'Distritur S.A. de C.V.', 'DIS030319SZ8', 'Guanajuato #131 int. 103, Col. Roma Norte, Cuauhtémoc, CDMX , c.p. 03100.', NULL, NULL, NULL, '2026-07-14 01:54:48', '2026-07-14 01:54:48'),
(18, 'Duprosa  S.A. de C.V.', 'DUPROSA', 'Guanajuato #131 int 103, Col. Roma Norte , Cuauhtémoc, CDMX, c.p. 06700.', NULL, NULL, NULL, '2026-07-14 03:47:33', '2026-07-14 03:47:33'),
(19, 'Centro de Epilepsia S.C.', 'CEP160722A61', 'San Francisco #524, PA consultorio 11, Col. Del Valle, Benito Juárez, CDMX, c.p.03100.', NULL, NULL, NULL, '2026-07-14 03:51:29', '2026-07-14 03:51:29'),
(20, 'Distrivideo S.A. de C.V.', 'DIS0204045N1', 'Guanajuato #131 int 103, Col. Roma Norte, Cuauhtémoc, CDMX, c.p.06700', NULL, NULL, NULL, '2026-07-14 03:54:34', '2026-07-14 03:54:34'),
(21, 'Centro Pediátrico Gune S.C.', 'CPG130110TR4', 'Guanajuato #131 - 103, Col Roma Norte, Cuauhtémoc, CDMX, c.p. 06700', NULL, NULL, NULL, '2026-07-18 02:14:50', '2026-07-18 02:14:50'),
(22, 'Tric S.A. de C.V.', 'TRI000801DA1', 'Alabama 153 casa 1, Col. Nápoles, Benito Juárez, CDMX, c.p.03810', NULL, NULL, NULL, '2026-07-18 03:17:15', '2026-07-18 03:17:15'),
(23, 'Durexa, S.A. de C.V.', 'DUR071214AT8', 'Secretaria de Marina #538, Col. Lomas del Chamizal, Cuajimalpa de Morelos, CDMX,05129', NULL, NULL, NULL, '2026-07-23 01:28:18', '2026-07-23 01:28:18'),
(24, 'Consurent, S.A. de C.V.', 'CON120917TI7', 'Guanajuato #131 int 2, Col. Roma Norte, Cuauhtémoc, CDMX, c.p.06700', NULL, NULL, NULL, '2026-07-23 02:07:36', '2026-07-23 02:07:36'),
(25, 'Distrilux, S.A. de C.V.', 'DIS251030862', 'Dr. Atl 254, Col. Santa Maria la Ribera, Cuauhtémoc, CDMX, c.p. 06400', NULL, NULL, NULL, '2026-07-23 04:48:37', '2026-07-23 04:48:37'),
(26, 'Sorem, S.A. de C.V.', 'SOR030429J56', 'Calle 25 #185, Col. Pro Hogar, Azcapotzalco, CDMX, c.p.02600', NULL, NULL, NULL, '2026-07-23 05:04:40', '2026-07-23 05:04:40');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_15_000001_add_operational_fields_to_users_table', 1),
(5, '2026_06_15_000002_create_purchase_operations_tables', 1),
(6, '2026_07_01_000001_add_amount_to_recurring_service_receipts_table', 1),
(7, '2026_07_01_000001_add_amount_to_recurring_service_receipts_table', 1),
(8, '2026_07_02_000001_add_is_domiciled_to_recurring_services_table', 2),
(9, '2026_07_02_000002_add_credit_fields_to_purchase_orders_table', 3),
(10, '2026_07_02_000003_add_purchase_order_notes_to_companies_table', 4),
(11, '2026_07_07_000001_add_reference_to_providers_and_payment_fields_to_purchase_orders', 5),
(12, '2026_07_07_000002_add_plain_password_to_users_table', 5),
(13, '2026_07_08_000001_create_provider_business_lines_table', 6),
(14, '2026_07_09_000002_add_observations_to_purchase_orders_table', 7),
(15, '2026_07_10_000001_add_due_days_after_cutoff_to_recurring_services_table', 7),
(16, '2026_07_10_000002_add_quote_file_to_purchase_orders_table', 7),
(17, '2026_07_22_000001_add_warehouses_to_companies_table', 8),
(18, '2026_07_22_000002_add_warehouse_to_purchase_orders_table', 8),
(19, '2026_07_23_000001_add_cutoff_day_to_recurring_services_table', 9),
(20, '2026_07_23_000002_add_branch_to_recurring_services_table', 9),
(21, '2026_07_23_000003_convert_warehouses_to_objects_table', 10),
(22, '2026_07_24_000001_add_cutoff_month_to_recurring_services_table', 10),
(23, '2026_07_24_000002_add_cutoff_year_to_recurring_services_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfc` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_line` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_business_line_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bank` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clabe` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `buyer_id`, `business_name`, `rfc`, `business_line`, `provider_business_line_id`, `bank`, `account_number`, `clabe`, `reference`, `created_at`, `updated_at`) VALUES
(1, 4, 'HED Distribuidora Farmaceutica S.A. de C.V.', 'DUR071214AT8', 'Medicamentos', 1, 'Bancomer', '1573303475', '012100015733034750', NULL, '2026-07-03 05:01:08', '2026-07-03 05:01:08'),
(2, 5, 'Grupo Unimedical Soluciones S.A. de C.V.', 'GUS060712I53', 'Medicamentos', 1, 'Afirme', '00159108151', '062580001591091519', NULL, '2026-07-03 05:31:16', '2026-07-03 05:31:16'),
(3, 4, 'LEONCIO GONZALEZ MARTINEZ', 'GOML841008911', 'Otros', 4, 'BANORTE', '1296026242', '072180012960262426', NULL, '2026-07-06 21:49:06', '2026-07-06 22:11:45'),
(4, 4, 'PLOMECSA ( PLOMERIA MEXICANA DEL CENTRO, SA DE CV. )', 'PMC100429IH9', 'Materiales de obra', 3, 'BANCOMER', '0174321227', '012180001743212279', NULL, '2026-07-06 23:35:28', '2026-07-06 23:35:28'),
(5, 5, 'BAXTER HEALTHCARE MEXICO', 'PSE001026IZ6', 'Medicamentos', 1, 'Banamex', '70170724036', '124180701707240365', NULL, '2026-07-07 01:06:19', '2026-07-07 01:06:19'),
(6, 4, 'INDUSTRIAS NOVACERAMIC, S.A. DE C.V.', 'INO141203SN3', 'Materiales de obra', 3, 'SANTANDER', '65504775207', '014832655047752072', NULL, '2026-07-07 01:19:32', '2026-07-07 01:19:32'),
(7, 6, 'Barbara  Itzel Cabrera de Dios', 'CADB111119Z9', 'Otros', 4, 'BANORTE', 'BBVA', '012180015002526769', NULL, '2026-07-07 23:46:06', '2026-07-08 06:36:46'),
(8, 6, 'PASE Servicios Electronicos SA DE CV', 'ISD950921HE5', 'Otros', 4, 'BB', 'STP', '684180069000697622', 'IMDM 281532399 JUAN CARLOS', '2026-07-08 04:05:08', '2026-07-08 04:05:08'),
(9, 6, 'Hector Daniel Olalde DeLuis', 'OADH810321QQ7', 'Materiales de obra', 3, 'HSBC', '021180040662051479', '021180040662051479', NULL, '2026-07-08 04:10:02', '2026-07-08 06:37:17'),
(10, 6, 'Luis Carlos Damian Carmona', 'DATL951224MY2', 'Otros', 4, 'BBVA', '012180029655968156', '012180029655968156', NULL, '2026-07-08 06:36:16', '2026-07-08 06:36:16'),
(11, 4, 'METROPOLITANA DE GASES Y SOLDADURA ( LUIS FERNANDO GOMEZ SUAREZ )', 'GOSL950720S28', 'Materiales de obra', 3, 'BANAMEX', '7855375', '002180701678553753', NULL, '2026-07-08 22:00:54', '2026-07-08 22:00:54'),
(12, 5, 'ESPECIALYMEDIC S. de R.L. de C.V.', 'ESP210422KC3', 'Medicamentos', 1, 'INBURSA', '50060285267', '036180500602852671', NULL, '2026-07-09 04:17:29', '2026-07-09 04:17:29'),
(13, 6, 'Condominio BC 245', 'CBD1712219I8', 'Otros', 4, 'BANORTE', '0440355580201', '030180900040238929', NULL, '2026-07-10 00:05:15', '2026-07-10 00:12:45'),
(14, 6, 'Cyberpuerta S.A. de C.V.', 'CYB080602JSA', 'Otros', 4, 'BBVA', '012914002012350367', '012914002012350367', '6289854132', '2026-07-10 00:12:13', '2026-07-10 07:09:40'),
(15, 6, 'Maria Magdalena Aviles Gonzalez', 'AIGM920216B25', 'Otros', 4, 'BANAMEX', '002448701233535421', '002448701233535421', NULL, '2026-07-10 00:27:03', '2026-07-10 00:27:03'),
(16, 4, 'RODAMIENTOS CARRILLO ( JULIO CESAR CARRILO MORALES )', 'CAMJ901129G14', 'Materiales de obra', 3, 'SANTANDER', '60603283159', '014180606032831599', NULL, '2026-07-10 00:47:57', '2026-07-10 00:47:57'),
(17, 5, 'Noe Pillado Cruz', 'PICN860808U44', 'Otros', 4, 'BBVA', '0469925376', '012180004699253761', NULL, '2026-07-10 02:46:14', '2026-07-10 02:46:14'),
(18, 5, 'Distribución Especializada de Medicamento S.A de C.V.', 'DEM940426E32', 'Medicamentos', 1, 'Banamex', '7034841', '002580700370348414', 'referencia: C1037224', '2026-07-10 02:50:48', '2026-07-10 02:50:48'),
(19, 5, 'COMERCIALIZADORA DE MEDICAMENTOS JASER', 'CMJ230810SK8', 'Medicamentos', 1, 'BBVA BANCOMER', '0120998230', '012180001209982302', NULL, '2026-07-10 02:51:53', '2026-07-10 02:51:53'),
(20, 6, 'Bertha Guadalupe Garcia Díaz', 'GADB850316733', 'Otros', 4, 'HSBC', '021451040619328693', '021451040619328693', NULL, '2026-07-10 07:08:31', '2026-07-10 07:08:31'),
(21, 4, 'ANDAMIOS CONSTR-CAS ( SOTA & EMP S DE RL DE CV )', 'SIC111019LP4', 'Materiales de obra', 3, 'BANORTE', '1094696270', '072180010946962700', NULL, '2026-07-13 21:11:36', '2026-07-18 01:34:02'),
(22, 4, 'LUIS ENRIQUE GONZALEZ CUADROS ( ESCOMBRO Y COSTALES )', 'GOCL7508121S3', 'Materiales de obra', 3, 'BANAMEX', '70152736485', '002180701527364857', NULL, '2026-07-15 22:58:35', '2026-07-15 22:58:35'),
(23, 6, 'Mayte Valdez Garcia', 'VAGM771031T50', 'Otros', 4, 'INBURSA', '036180500548886467', '036180500548886467', 'ETIQUETAS', '2026-07-16 06:00:44', '2026-07-16 06:00:44'),
(24, 6, 'Intercompras Comercio Electrónico S.A. de C.V.', 'INT070927D47', 'Otros', 4, 'BANAMEX', '002760025454833625', '002760025454833625', NULL, '2026-07-16 23:26:39', '2026-07-16 23:26:39'),
(25, 5, 'INDUSTRIAS PLASTICAS MEDICAS SA DE CV', 'IPM670824S94', 'Otros', 4, 'SANTANDER', '22000747476', '014542220007474769', 'D0005514 Aplica a todos los bancos, anotarlo en el campo “Concepto de pago” obligatoriamente y en mayúsculas.', '2026-07-17 02:46:29', '2026-07-17 02:46:29'),
(26, 6, 'Aceros Galaxy SA de CV', 'AGA0911129D6', 'Materiales de obra', 3, 'BANAMEX', '002180051663614108', '002180051663614108', NULL, '2026-07-21 01:35:24', '2026-07-21 01:35:24'),
(27, 6, 'Daniel García Vergara', 'XXX00000XX', 'Otros', 4, 'MIFEL', '042180016002801001', '042180016002801001', 'LLANTAS', '2026-07-23 07:15:40', '2026-07-23 07:15:40'),
(28, 5, 'SALUCOM S.A DE C.V.', 'BOD060112BY0', 'Medicamentos', 1, 'CBM', '41910006957', '124180419100069574', '92106550', '2026-07-24 00:47:11', '2026-07-24 00:47:11'),
(29, 5, 'OVERPHARMA', 'OVE210622A70', 'Otros', 4, 'BANCOMER', '012 705 2030', '012180001270520308', NULL, '2026-07-24 01:41:25', '2026-07-24 01:41:25'),
(30, 5, 'FRESENIUS KABI MEXICO S.A. DE C.V.', 'FKM4801155G8', 'Medicamentos', 1, 'SANTANDER', '65500402891', '014320655004028918', '52106205', '2026-07-24 02:21:01', '2026-07-24 02:21:01'),
(31, 4, 'NOE FLORES VARGAS', 'FOVN870514MM9', 'Otros', 4, 'BANORTE', '1301428414', '072650013014284148', NULL, '2026-07-25 01:09:43', '2026-07-25 01:15:39');

-- --------------------------------------------------------

--
-- Table structure for table `provider_business_lines`
--

CREATE TABLE `provider_business_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_business_lines`
--

INSERT INTO `provider_business_lines` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Medicamentos', 1, '2026-07-09 23:51:04', '2026-07-09 23:51:04'),
(2, 'Servicios farmaceuticos', 1, '2026-07-09 23:51:04', '2026-07-09 23:51:04'),
(3, 'Materiales de obra', 1, '2026-07-09 23:51:04', '2026-07-09 23:51:04'),
(4, 'Otros', 1, '2026-07-09 23:51:04', '2026-07-09 23:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `created_on` date NOT NULL,
  `due_date` date NOT NULL,
  `is_credit` tinyint(1) NOT NULL DEFAULT '0',
  `credit_days` smallint(5) UNSIGNED DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_concept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `quote_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote_original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `receipt_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `folio`, `buyer_id`, `company_id`, `warehouse`, `provider_id`, `created_on`, `due_date`, `is_credit`, `credit_days`, `reference`, `payment_concept`, `observations`, `quote_file_path`, `quote_original_name`, `delivery_date`, `status`, `receipt_status`, `total`, `created_at`, `updated_at`) VALUES
(1, 'OC-401', 4, 3, NULL, 1, '2026-07-02', '2026-07-03', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-03', 'paid', 'pending', 12000.00, '2026-07-03 05:06:12', '2026-07-03 05:16:26'),
(2, 'OC-402', 5, 1, NULL, 2, '2026-07-02', '2026-07-03', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-03', 'paid', 'pending', 12000.00, '2026-07-03 05:34:07', '2026-07-03 05:37:33'),
(3, 'OC-403', 4, 9, NULL, 3, '2026-07-06', '0026-07-06', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0026-07-06', 'paid', 'pending', 400.00, '2026-07-06 21:52:34', '2026-07-07 00:39:32'),
(4, 'OC-404', 4, 9, NULL, 4, '2026-07-06', '2026-07-06', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0026-07-06', 'paid', 'pending', 3947.89, '2026-07-07 00:20:09', '2026-07-07 01:49:20'),
(5, 'OC-405', 5, 2, NULL, 5, '2026-07-06', '2026-08-11', 1, 30, NULL, NULL, NULL, NULL, NULL, '2026-07-11', 'approved', 'pending', 160578.00, '2026-07-07 01:08:00', '2026-07-07 01:34:53'),
(6, 'OC-406', 4, 9, NULL, 6, '2026-07-06', '2026-07-06', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0026-07-06', 'paid', 'pending', 55697.82, '2026-07-07 01:28:01', '2026-07-07 03:05:16'),
(7, 'OC-407', 6, 8, NULL, 7, '2026-07-07', '2026-07-10', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-07', 'rejected', 'pending', 0.00, '2026-07-07 23:50:51', '2026-07-10 03:00:32'),
(8, 'OC-408', 6, 2, NULL, 8, '2026-07-07', '2026-07-07', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'PASE JUAN CARLOS', NULL, NULL, NULL, '2026-07-07', 'paid', 'pending', 1500.00, '2026-07-08 04:06:29', '2026-07-08 06:17:52'),
(9, 'OC-409', 6, 1, NULL, 10, '2026-07-08', '2026-07-10', 0, NULL, 'sellos cm', 'sellos cm', NULL, NULL, NULL, '2026-07-10', 'paid', 'pending', 2651.76, '2026-07-08 06:39:30', '2026-07-09 04:11:08'),
(10, 'OC-410', 6, 2, NULL, 10, '2026-07-08', '2026-07-10', 0, NULL, 'Toner Cf360 generico', 'Toner CF360 generico', NULL, NULL, NULL, '2026-07-10', 'paid', 'pending', 3960.00, '2026-07-08 06:40:44', '2026-07-09 04:07:52'),
(11, 'OC-411', 4, 9, NULL, 11, '2026-07-08', '2026-07-08', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-08', 'paid', 'pending', 12950.99, '2026-07-08 22:04:59', '2026-07-08 23:08:12'),
(12, 'OC-412', 5, 2, NULL, 12, '2026-07-08', '2026-08-08', 1, 30, NULL, NULL, NULL, NULL, NULL, '2026-07-08', 'rejected', 'pending', 358000.00, '2026-07-09 04:18:43', '2026-07-09 04:19:51'),
(13, 'OC-413', 5, 1, NULL, 12, '2026-07-08', '2026-08-08', 1, 30, NULL, NULL, NULL, NULL, NULL, '2026-07-08', 'approved', 'pending', 358000.00, '2026-07-09 04:21:31', '2026-07-09 04:38:33'),
(14, 'OC-414', 5, 1, NULL, 12, '2026-07-08', '2026-08-08', 1, 30, NULL, NULL, NULL, NULL, NULL, '2026-07-08', 'approved', 'pending', 96000.00, '2026-07-09 04:22:31', '2026-07-09 04:38:37'),
(15, 'OC-415', 6, 4, NULL, 13, '2026-07-09', '2026-07-10', 0, NULL, 'Factura c3576', 'Mtto Julio 2026', NULL, NULL, NULL, '2026-07-10', 'paid', 'pending', 1890.39, '2026-07-10 00:06:26', '2026-07-10 00:36:14'),
(16, 'OC-416', 6, 4, NULL, 14, '2026-07-09', '2026-07-10', 0, NULL, NULL, 'PEDIDO AB6669858', NULL, NULL, NULL, '2026-07-10', 'paid', 'pending', 6072.00, '2026-07-10 00:13:54', '2026-07-10 01:25:07'),
(17, 'OC-417', 6, 10, NULL, 15, '2026-07-09', '2026-07-10', 0, NULL, NULL, 'Semana 11 y 12 julio 2026', NULL, NULL, NULL, '2026-07-10', 'paid', 'pending', 2552.00, '2026-07-10 00:28:26', '2026-07-10 00:54:48'),
(18, 'OC-418', 4, 9, NULL, 16, '2026-07-09', '0026-07-09', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0026-07-09', 'paid', 'pending', 2320.00, '2026-07-10 00:52:03', '2026-07-10 01:48:59'),
(19, 'OC-419', 5, 1, NULL, 17, '2026-07-09', '2026-07-09', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-16', 'rejected', 'pending', 12616.80, '2026-07-10 02:47:14', '2026-07-10 02:54:28'),
(20, 'OC-420', 5, 1, NULL, 17, '2026-07-09', '2026-07-09', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-16', 'rejected', 'pending', 1219.64, '2026-07-10 03:05:09', '2026-07-10 03:06:02'),
(21, 'OC-421', 5, 1, NULL, 17, '2026-07-09', '2026-07-09', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-16', 'paid', 'pending', 14635.20, '2026-07-10 03:32:16', '2026-07-10 03:49:09'),
(22, 'OC-422', 5, 1, NULL, 18, '2026-07-09', '2026-08-09', 1, 30, 'referencia: C1037224', NULL, NULL, NULL, NULL, '2026-07-09', 'rejected', 'pending', 59997.00, '2026-07-10 05:14:30', '2026-07-21 01:32:23'),
(23, 'OC-423', 5, 1, NULL, 19, '2026-07-09', '2026-08-24', 1, 45, NULL, NULL, NULL, NULL, NULL, '2026-07-09', 'approved', 'pending', 130000.00, '2026-07-10 05:19:39', '2026-07-10 06:27:52'),
(24, 'OC-424', 5, 1, NULL, 12, '2026-07-09', '2026-08-09', 1, 30, NULL, NULL, NULL, NULL, NULL, '2026-07-09', 'approved', 'pending', 96000.00, '2026-07-10 05:41:42', '2026-07-10 06:28:19'),
(25, 'OC-425', 6, 1, NULL, 20, '2026-07-10', '2026-07-17', 0, NULL, NULL, 'Recolección RPBI', NULL, NULL, NULL, '2026-07-17', 'paid', 'pending', 4897.34, '2026-07-10 07:11:31', '2026-07-14 01:51:31'),
(26, 'OC-426', 4, 9, NULL, 21, '2026-07-13', '0026-07-13', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0026-07-13', 'paid', 'pending', 8120.00, '2026-07-14 01:15:33', '2026-07-14 01:31:17'),
(27, 'OC-427', 4, 9, NULL, 22, '2026-07-15', '2026-07-15', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-15', 'paid', 'pending', 15008.00, '2026-07-15 23:07:37', '2026-07-16 00:29:58'),
(28, 'OC-428', 6, 8, NULL, 8, '2026-07-15', '2026-07-15', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'PASE JUAN CARLOS', NULL, NULL, NULL, '2026-07-15', 'rejected', 'pending', 2000.00, '2026-07-16 05:56:56', '2026-07-16 06:40:04'),
(29, 'OC-429', 6, 1, NULL, 23, '2026-07-16', '2026-07-17', 0, NULL, 'ETIQUETAS', 'COT S13972', NULL, NULL, NULL, '2026-07-15', 'paid', 'pending', 1839.00, '2026-07-16 06:06:40', '2026-07-17 03:10:48'),
(30, 'OC-430', 6, 2, NULL, 8, '2026-07-16', '2026-07-15', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'Recarga', NULL, NULL, NULL, '2026-07-15', 'paid', 'pending', 2000.00, '2026-07-16 06:40:17', '2026-07-16 06:42:31'),
(31, 'OC-431', 6, 1, NULL, 24, '2026-07-16', '2026-07-17', 0, NULL, '1067287', '1067287', NULL, NULL, NULL, '2026-07-16', 'paid', 'pending', 9997.00, '2026-07-16 23:28:11', '2026-07-17 03:11:44'),
(32, 'OC-432', 5, 1, NULL, 25, '2026-07-16', '2026-07-16', 0, NULL, 'D0005514 Aplica a todos los bancos, anotarlo en el campo “Concepto de pago” obligatoriamente y en mayúsculas.', 'D0005514', NULL, NULL, NULL, '2026-07-21', 'paid', 'completed', 68766.00, '2026-07-17 03:03:35', '2026-07-23 01:07:47'),
(33, 'OC-433', 4, 9, NULL, 11, '2026-07-17', '2026-07-17', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-17', 'paid', 'pending', 3015.00, '2026-07-17 21:32:22', '2026-07-20 23:18:47'),
(34, 'OC-434', 4, 9, NULL, 3, '2026-07-17', '2026-07-17', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-17', 'paid', 'pending', 400.00, '2026-07-17 21:38:26', '2026-07-20 23:20:48'),
(35, 'OC-435', 6, 2, NULL, 8, '2026-07-17', '2026-07-17', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'IMDM281532399', NULL, NULL, NULL, '2026-07-17', 'paid', 'pending', 1500.00, '2026-07-18 00:09:28', '2026-07-20 23:21:24'),
(36, 'OC-436', 4, 9, NULL, 21, '2026-07-17', '2026-07-17', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-17', 'paid', 'pending', 4640.00, '2026-07-18 01:36:18', '2026-07-20 23:22:03'),
(37, 'OC-437', 6, 9, NULL, 26, '2026-07-20', '2026-07-21', 0, NULL, 'COTIZACION 0727', '0', NULL, 'purchase-order-quotes/nMn2MEVdeiS82BNlghB2diwKZL1z9KNeddgpzU1X.jpg', 'WhatsApp Image 2026-07-20 at 12.42.44 PM.jpeg', '2026-07-21', 'paid', 'pending', 28000.00, '2026-07-21 01:37:22', '2026-07-21 03:02:17'),
(38, 'OC-438', 5, 1, NULL, 18, '2026-07-20', '2026-08-14', 1, 30, 'referencia: C1037224', NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* PRESENTACION COMERCIAL\r\n* FECHA DE ENTREGA:  INMEDIATA\r\n* PLAZO DE PAGO:   CREDITO 30 DIAS A PARTIR FECHA DE SOLICITUD', NULL, NULL, '2026-07-14', 'sent', 'pending', 59973.00, '2026-07-21 01:38:41', '2026-07-21 01:38:41'),
(39, 'OC-439', 5, 1, NULL, 12, '2026-07-20', '2026-08-09', 1, 30, NULL, NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* FECHA DE ENTREGA:  INMEDIATA\r\n* PLAZO DE PAGO: 30 DIAS', NULL, NULL, '2026-07-10', 'sent', 'pending', 358000.00, '2026-07-21 01:52:13', '2026-07-21 01:52:13'),
(40, 'OC-440', 5, 1, NULL, 19, '2026-07-20', '2026-08-24', 1, 45, NULL, NULL, NULL, NULL, NULL, '2026-07-10', 'sent', 'pending', 9660.00, '2026-07-21 01:59:06', '2026-07-21 01:59:06'),
(41, 'OC-441', 5, 1, NULL, 12, '2026-07-20', '2026-08-10', 1, 30, NULL, NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* PRESENTACION COMERCIAL\r\n* FECHA DE ENTREGA:  INMEDIATA\r\n* PLAZO DE PAGO: 30 DIAS', NULL, NULL, '2026-07-11', 'sent', 'pending', 91000.00, '2026-07-21 02:02:53', '2026-07-21 02:02:53'),
(42, 'OC-442', 5, 1, NULL, 19, '2026-07-20', '2026-08-27', 1, 45, NULL, NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* PLAZO DE PAGO:   CREDITO 45 DIAS', NULL, NULL, '2026-07-13', 'sent', 'pending', 88000.00, '2026-07-21 02:07:43', '2026-07-21 02:07:43'),
(43, 'OC-443', 5, 1, NULL, 18, '2026-07-20', '2026-08-12', 1, 30, 'referencia: C1037224', NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* PRESENTACION COMERCIAL\r\n* FECHA DE ENTREGA:  INMEDIATA\r\n* PLAZO DE PAGO:   CREDITO 30 DIAS A PARTIR FECHA DE SOLICITUD', NULL, NULL, '2026-07-13', 'sent', 'pending', 32242.00, '2026-07-21 02:15:47', '2026-07-21 02:15:47'),
(44, 'OC-444', 5, 1, NULL, 12, '2026-07-20', '2026-08-15', 1, 30, NULL, NULL, '* CADUCIDAD 12 MESES (EN CASO DE NO CONTAR CON ESTA CADUCIDAD, FAVOR DE ENTREGAR CARTA COMPROMISO CANJE)\r\n* ENTREGA DE CERTIFICADO ANALITICO\r\n* COPIA DEL REGISTRO SANITARIO VIGENTE Y/O EN SU CASO SI CUENTA CON PRORROGA\r\n* PRESENTACION COMERCIAL\r\n* FECHA DE ENTREGA:  INMEDIATA\r\n* PLAZO DE PAGO: 30 DIAS', NULL, NULL, '2026-07-16', 'sent', 'pending', 179000.00, '2026-07-21 02:24:17', '2026-07-21 02:24:17'),
(45, 'OC-445', 4, 9, NULL, 22, '2026-07-21', '2026-07-21', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-21', 'paid', 'pending', 13440.00, '2026-07-21 22:39:50', '2026-07-21 23:09:54'),
(46, 'OC-446', 6, 1, NULL, 10, '2026-07-21', '2026-07-21', 0, NULL, 'TONER 58A', 'toner genérico 58a', NULL, NULL, NULL, '2026-07-21', 'paid', 'pending', 5490.00, '2026-07-21 23:27:11', '2026-07-22 00:15:29'),
(47, 'OC-447', 6, 2, NULL, 8, '2026-07-22', '2026-07-22', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'PASE JUAN CARLOS', NULL, NULL, NULL, '2026-07-22', 'paid', 'pending', 2000.00, '2026-07-22 22:37:31', '2026-07-23 00:50:53'),
(48, 'OC-448', 6, 1, NULL, 23, '2026-07-22', '2026-07-24', 0, NULL, 'ETIQUETAS', 'Etiquetas', NULL, 'purchase-order-quotes/GcW0tDLOWk1tOr4RFZmfCCUBIfYCauqkxIAmIq5n.pdf', 'Gmail - COTIZACION ETIQUETAS Y RIBBONS.pdf', '2026-07-24', 'paid', 'pending', 16717.74, '2026-07-23 05:31:37', '2026-07-24 04:44:10'),
(49, 'OC-449', 6, 2, NULL, 27, '2026-07-23', '2026-07-23', 0, NULL, 'LLANTAS KANGOO', 'LLANTAS KANGOO', NULL, NULL, NULL, '2026-07-23', 'paid', 'pending', 3753.76, '2026-07-23 07:17:03', '2026-07-24 04:51:33'),
(50, 'OC-450', 5, 1, NULL, 28, '2026-07-23', '2026-07-23', 0, NULL, '92106550', NULL, NULL, NULL, NULL, '2026-07-24', 'paid', 'pending', 105496.70, '2026-07-24 00:48:09', '2026-07-24 04:42:54'),
(51, 'OC-451', 6, 2, NULL, 8, '2026-07-23', '2026-07-24', 0, NULL, 'IMDM 281532399 JUAN CARLOS', 'PASE JUAN CARLOS', NULL, NULL, NULL, '2026-07-24', 'paid', 'pending', 1000.00, '2026-07-24 01:04:30', '2026-07-24 04:48:34'),
(52, 'OC-452', 5, 1, NULL, 25, '2026-07-23', '2026-07-23', 0, NULL, 'D0005514 Aplica a todos los bancos, anotarlo en el campo “Concepto de pago” obligatoriamente y en mayúsculas.', NULL, NULL, NULL, NULL, '2026-07-24', 'paid', 'pending', 153137.40, '2026-07-24 01:08:34', '2026-07-24 04:43:24'),
(53, 'OC-453', 5, 1, NULL, 29, '2026-07-23', '2026-07-23', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-20', 'paid', 'pending', 4758.96, '2026-07-24 01:43:42', '2026-07-24 04:43:44'),
(54, 'OC-454', 5, 2, 'Concepcion', 30, '2026-07-23', '2026-07-24', 0, NULL, '52106205', NULL, NULL, NULL, NULL, '2026-07-30', 'paid', 'pending', 240030.00, '2026-07-24 05:22:23', '2026-07-24 07:48:09'),
(55, 'OC-455', 4, 9, NULL, 31, '2026-07-24', '2026-07-24', 0, NULL, NULL, NULL, 'PAGO DE GASTOS', NULL, NULL, '2026-07-24', 'paid', 'pending', 428.00, '2026-07-25 01:13:36', '2026-07-25 03:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `article` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_price` decimal(14,2) NOT NULL,
  `line_total` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `article`, `quantity`, `unit_price`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 'Trastuzumab 500 mg', 1.00, 12000.00, 12000.00, '2026-07-03 05:06:12', '2026-07-03 05:06:12'),
(2, 2, 'Trastuzumab 500 mg', 1.00, 12000.00, 12000.00, '2026-07-03 05:34:07', '2026-07-03 05:34:07'),
(3, 3, 'PAGO DE GASOLINA', 1.00, 400.00, 400.00, '2026-07-06 21:52:34', '2026-07-06 21:52:34'),
(4, 4, 'DISCO CORTE METAL AUSTROMEX EASY CUT  7\" X 3.2 MM', 40.00, 58.00, 2320.00, '2026-07-07 00:20:09', '2026-07-07 00:20:09'),
(5, 4, 'DISCO CORTE METAL PLANO AUSTROMEX  4 1/2\" X 1.6 MM', 50.00, 19.97, 998.50, '2026-07-07 00:20:09', '2026-07-07 00:20:09'),
(6, 4, 'PRENSA IUSA TOTAL NODULAR 5\" 618884', 3.00, 169.22, 507.66, '2026-07-07 00:20:09', '2026-07-07 00:20:09'),
(7, 4, 'LLAVES IUSA TOTAL JUEGO 9 PZAS ALLEN MIX P.BOLA', 1.00, 121.73, 121.73, '2026-07-07 00:20:09', '2026-07-07 00:20:09'),
(8, 5, 'Primene 250 ml', 300.00, 535.26, 160578.00, '2026-07-07 01:08:00', '2026-07-07 01:08:00'),
(9, 6, 'TABIMAX (12) (24X12X12 cm)*', 6912.00, 6.78, 46863.36, '2026-07-07 01:28:01', '2026-07-07 01:28:01'),
(10, 6, 'TARIMA 1000 X 1000 (4 TABLAS) NOVABLOCKS', 24.00, 48.00, 1152.00, '2026-07-07 01:28:01', '2026-07-07 01:28:01'),
(11, 6, 'PRECIO TORTON (IVA 16 %)', 1.00, 7682.46, 7682.46, '2026-07-07 01:28:01', '2026-07-07 01:28:01'),
(12, 7, 'Renta Glorieta Santa Fe', 1.00, 0.00, 0.00, '2026-07-07 23:50:51', '2026-07-07 23:50:51'),
(13, 8, 'PASE', 1.00, 1500.00, 1500.00, '2026-07-08 04:06:29', '2026-07-08 04:06:29'),
(14, 9, 'sellos cm', 3.00, 883.92, 2651.76, '2026-07-08 06:39:30', '2026-07-08 06:39:30'),
(15, 10, 'Toner CF360', 4.00, 990.00, 3960.00, '2026-07-08 06:40:44', '2026-07-08 06:40:44'),
(16, 11, 'ACETILENO 5.0 KG CARGA ACETILENO 5.0 KG', 2.00, 1584.05, 3168.10, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(17, 11, 'BOQUILLA D/CORTE OXI-ACE #1', 2.00, 175.86, 351.72, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(18, 11, 'BOQUILLA D/CORTE OXI-ACE #2', 2.00, 175.86, 351.72, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(19, 11, 'CEPILLO D/ALAMBRE CIRCULAR T PARA LIMPIEZA DE SUPERFICIES METÁLICAS Y REMOCIÓN DE SOLDADURA', 30.00, 200.00, 6000.00, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(20, 11, 'OXIGENO INDUSTRIAL 9.5 M3 CARGA EN CILINDRO DE OXIGENO INDUSTRIAL 9.5 M3', 2.00, 646.55, 1293.10, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(21, 11, 'IVA', 1.00, 1786.35, 1786.35, '2026-07-08 22:04:59', '2026-07-08 22:04:59'),
(22, 12, 'Pembrolizumab KEYTRUDA 100mg frascos ampula', 4.00, 89500.00, 358000.00, '2026-07-09 04:18:43', '2026-07-09 04:18:43'),
(23, 13, 'Pembrolizumab KEYTRUDA 100mg frascos ampula', 4.00, 89500.00, 358000.00, '2026-07-09 04:21:31', '2026-07-09 04:21:31'),
(24, 14, 'Blinatumumab BLINCYTO 35MCG frasco ampula', 3.00, 32000.00, 96000.00, '2026-07-09 04:22:31', '2026-07-09 04:22:31'),
(25, 15, 'mtto', 1.00, 1890.39, 1890.39, '2026-07-10 00:06:26', '2026-07-10 00:06:26'),
(26, 16, 'Tarjeta de Video  RTX 5050, 8 GB', 1.00, 6072.00, 6072.00, '2026-07-10 00:13:54', '2026-07-10 00:13:54'),
(27, 17, 'pago publicidad animada plaza', 1.00, 2552.00, 2552.00, '2026-07-10 00:28:26', '2026-07-10 00:28:26'),
(28, 18, 'PATESCA 4 TON', 1.00, 2320.00, 2320.00, '2026-07-10 00:52:03', '2026-07-10 00:52:03'),
(29, 19, 'Caldo de hidrolizado de caseína de soja (TSB) en frasco con tapa a rosca y septum.', 48.00, 262.85, 12616.80, '2026-07-10 02:47:14', '2026-07-10 02:47:14'),
(30, 20, 'aldo de hidrolizado de caseína de soja (TSB) en frasco con tapa a rosca y septum.', 4.00, 304.91, 1219.64, '2026-07-10 03:05:09', '2026-07-10 03:05:09'),
(31, 21, 'Caldo de hidrolizado de caseína de soja (TSB) en frasco con tapa a rosca y septum', 48.00, 304.90, 14635.20, '2026-07-10 03:32:16', '2026-07-10 03:32:16'),
(32, 22, 'EFFIVIA 100mg/4mL SOL INY CAJA C/FRASCO', 3.00, 3499.00, 10497.00, '2026-07-10 05:14:30', '2026-07-10 05:14:30'),
(33, 22, 'EFFIVIA 400mg/16mL SOL INY CAJA C/FRASCO 16mL', 5.00, 9900.00, 49500.00, '2026-07-10 05:14:30', '2026-07-10 05:14:30'),
(34, 23, 'Blinatumumab BLINCYTO 35MCG frasco ampula', 4.00, 32500.00, 130000.00, '2026-07-10 05:19:39', '2026-07-10 05:19:39'),
(35, 24, 'Blinatumumab BLINCYTO 35MCG frasco ampula', 3.00, 32000.00, 96000.00, '2026-07-10 05:41:42', '2026-07-10 05:41:42'),
(36, 25, 'Recolección RPBI', 1.00, 4897.34, 4897.34, '2026-07-10 07:11:31', '2026-07-10 07:11:31'),
(37, 26, 'MARTILLO DEMOLEDOR DEWALT DE 30 KG CON 2 PULCETAS DE PUNTA ( RENTA )', 1.00, 7000.00, 7000.00, '2026-07-14 01:15:33', '2026-07-14 01:15:33'),
(38, 26, 'IVA', 1.00, 1120.00, 1120.00, '2026-07-14 01:15:33', '2026-07-14 01:15:33'),
(39, 27, 'VIAJES DE ESCOMBRO LH-24-029 ( 08/07/26)', 1.00, 2500.00, 2500.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(40, 27, 'VIAJES DE ESCOMBRO LH-24-029 ( 09/07/26)', 1.00, 2500.00, 2500.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(41, 27, 'COSTALES DE ARENA (09-07-26)', 200.00, 18.32, 3664.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(42, 27, 'COSTALES DE VACIOS  (09-07-26)', 200.00, 18.32, 3664.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(43, 27, 'IMPUESTOS TRASLADADOS ( IVA 16 %)', 1.00, 2144.00, 2144.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(44, 27, 'IVA 16 %', 1.00, 536.00, 536.00, '2026-07-15 23:07:37', '2026-07-15 23:07:37'),
(45, 28, 'Recarga de TAG', 1.00, 2000.00, 2000.00, '2026-07-16 05:56:56', '2026-07-16 05:56:56'),
(46, 29, 'Ribbon zebra', 10.00, 183.90, 1839.00, '2026-07-16 06:06:40', '2026-07-16 06:06:40'),
(47, 30, 'Recarga de TAG', 1.00, 2000.00, 2000.00, '2026-07-16 06:40:17', '2026-07-16 06:40:17'),
(48, 31, 'Lenovo idea Tab 11in - 8gb ram -12gb', 2.00, 4998.50, 9997.00, '2026-07-16 23:28:11', '2026-07-16 23:28:11'),
(49, 32, 'Bolsa EVA de 250 ml', 600.00, 114.61, 68766.00, '2026-07-17 03:03:35', '2026-07-17 03:03:35'),
(50, 33, 'CEPILLO DE MANO ACERO INOXID', 5.00, 71.55, 357.75, '2026-07-17 21:32:22', '2026-07-17 21:32:22'),
(51, 33, 'OXIGENO INDUSTRIAL 6.0 M3', 2.00, 474.14, 948.28, '2026-07-17 21:32:22', '2026-07-17 21:32:22'),
(52, 33, 'OXIGENO INDUSTRIAL 9.5 M3', 2.00, 646.55, 1293.10, '2026-07-17 21:32:22', '2026-07-17 21:32:22'),
(53, 33, 'IVA', 1.00, 415.87, 415.87, '2026-07-17 21:32:22', '2026-07-17 21:32:22'),
(54, 34, 'PAGO DE GASOLINA', 1.00, 400.00, 400.00, '2026-07-17 21:38:26', '2026-07-17 21:38:26'),
(55, 35, 'Recarga de TAG', 1.00, 1500.00, 1500.00, '2026-07-18 00:09:28', '2026-07-18 00:09:28'),
(56, 36, 'RENTA DE VIBRADOR CON CHICOTE', 1.00, 4000.00, 4000.00, '2026-07-18 01:36:18', '2026-07-18 01:36:18'),
(57, 36, 'IVA', 1.00, 640.00, 640.00, '2026-07-18 01:36:18', '2026-07-18 01:36:18'),
(58, 37, 'Angulos 4 X 5/8', 5.00, 5000.00, 25000.00, '2026-07-21 01:37:22', '2026-07-21 01:37:22'),
(59, 37, 'Envio', 1.00, 3000.00, 3000.00, '2026-07-21 01:37:22', '2026-07-21 01:37:22'),
(60, 38, 'EFFIVIA 100mg/4mL SOL INY CAJA C/FRASCO', 3.00, 3491.00, 10473.00, '2026-07-21 01:38:41', '2026-07-21 01:38:41'),
(61, 38, 'EFFIVIA 400mg/16mL SOL INY CAJA C/FRASCO 16mL', 5.00, 9900.00, 49500.00, '2026-07-21 01:38:41', '2026-07-21 01:38:41'),
(62, 39, 'Pembrolizumab KEYTRUDA 100mg frascos ampula', 4.00, 89500.00, 358000.00, '2026-07-21 01:52:13', '2026-07-21 01:52:13'),
(63, 40, 'Idarubicina LARECIN 5mg/5ml', 6.00, 1610.00, 9660.00, '2026-07-21 01:59:06', '2026-07-21 01:59:06'),
(64, 41, 'Daratumumab Darzalex 400mg fco amp', 2.00, 36000.00, 72000.00, '2026-07-21 02:02:53', '2026-07-21 02:02:53'),
(65, 41, 'Daratumumab Darzalex 100mg fco amp', 2.00, 9500.00, 19000.00, '2026-07-21 02:02:53', '2026-07-21 02:02:53'),
(66, 42, 'Perjeta 420mg/14ml frasco ampula', 1.00, 88000.00, 88000.00, '2026-07-21 02:07:43', '2026-07-21 02:07:43'),
(67, 43, 'Trazimera 440mg sol iny c/1pza', 2.00, 16121.00, 32242.00, '2026-07-21 02:15:47', '2026-07-21 02:15:47'),
(68, 44, 'Pembrolizumab KEYTRUDA 100mg frascos ampula', 2.00, 89500.00, 179000.00, '2026-07-21 02:24:17', '2026-07-21 02:24:17'),
(69, 45, 'VIAJE DE ESCOMBRO LH-24-029 (130726)', 1.00, 2500.00, 2500.00, '2026-07-21 22:39:50', '2026-07-21 22:39:50'),
(70, 45, 'VIAJE DE ESCOMBRO LH-24-029 (160726)', 1.00, 2500.00, 2500.00, '2026-07-21 22:39:50', '2026-07-21 22:39:50'),
(71, 45, 'COSTALES DE ARENA (160726)', 200.00, 30.20, 6040.00, '2026-07-21 22:39:50', '2026-07-21 22:39:50'),
(72, 45, 'IVA', 1.00, 2400.00, 2400.00, '2026-07-21 22:39:50', '2026-07-21 22:39:50'),
(73, 46, 'Toner 58a', 10.00, 549.00, 5490.00, '2026-07-21 23:27:11', '2026-07-21 23:27:11'),
(74, 47, 'Recarga de TAG', 1.00, 2000.00, 2000.00, '2026-07-22 22:37:31', '2026-07-22 22:37:31'),
(75, 48, 'Etiqueta 10010034 Zebra TD 4x6', 358.17, 22.00, 7879.74, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(76, 48, 'Etiqueta TT 102x152 mm', 3249.00, 1.00, 3249.00, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(77, 48, 'Etiqueta TT 76x51 mm', 2649.00, 1.00, 2649.00, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(78, 48, 'Etiqueta Zebra Ribbon Resina 110x74', 299.00, 9.00, 2691.00, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(79, 48, 'Servicio de Envio', 249.00, 1.00, 249.00, '2026-07-23 05:31:37', '2026-07-23 05:31:37'),
(80, 49, 'LLANTAS', 4.00, 938.44, 3753.76, '2026-07-23 07:17:03', '2026-07-23 07:17:03'),
(81, 50, 'VITAFUSIN  PEDIATRICO FAM', 575.00, 176.80, 101660.00, '2026-07-24 00:48:09', '2026-07-24 00:48:09'),
(82, 50, 'GLUCONATO CAL 10% 10ML C/100 AMP', 3.00, 1278.90, 3836.70, '2026-07-24 00:48:09', '2026-07-24 00:48:09'),
(83, 51, 'PASE', 1.00, 1000.00, 1000.00, '2026-07-24 01:04:30', '2026-07-24 01:04:30'),
(84, 52, '5022 Bolsa EVA de 250 ml', 600.00, 114.61, 68764.80, '2026-07-24 01:08:34', '2026-07-24 01:08:34'),
(85, 52, '5006 Bolsa EVA de 500 ml', 500.00, 120.64, 60320.00, '2026-07-24 01:08:34', '2026-07-24 01:08:34'),
(86, 52, '5012 Bolsa EVA de 1000 ml', 80.00, 139.49, 11159.20, '2026-07-24 01:08:34', '2026-07-24 01:08:34'),
(87, 52, '5003 Bolsa EVA de 3000 ml', 90.00, 143.26, 12893.40, '2026-07-24 01:08:34', '2026-07-24 01:08:34'),
(88, 53, 'Equipo de infusión intravenosa para bomba (TERUFUSION) ambar. Terumo', 20.00, 237.95, 4758.96, '2026-07-24 01:43:42', '2026-07-24 01:43:42'),
(89, 54, '52106205MX2195091 SMOFlipid ® 20% 500 ml', 150.00, 1533.00, 229950.00, '2026-07-24 05:22:23', '2026-07-24 05:22:23'),
(90, 54, '521062055127BP Cloruro de sodio 0.9% 100ml', 800.00, 12.60, 10080.00, '2026-07-24 05:22:23', '2026-07-24 05:22:23'),
(91, 55, 'PAGO DE GASOLINA', 1.00, 300.00, 300.00, '2026-07-25 01:13:36', '2026-07-25 01:13:36'),
(92, 55, 'COPLES PARA EL OXIGENO', 1.00, 128.00, 128.00, '2026-07-25 01:13:36', '2026-07-25 01:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payments`
--

CREATE TABLE `purchase_order_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `paid_by` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_on` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_payments`
--

INSERT INTO `purchase_order_payments` (`id`, `purchase_order_id`, `paid_by`, `file_path`, `original_name`, `paid_on`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'payments/Td0075TMyOfBP8Kl3zuEpAsU9AHku6fv7STrkALT.pdf', 'IZZI_JUNIO2026_PRODIFEM.pdf', '2026-07-02', '2026-07-03 05:16:26', '2026-07-03 05:16:26'),
(2, 2, 1, 'payments/4OR1Qb2pdH0czwRwJTo1z2oNGGWf8c01nzGQ0yVQ.pdf', 'recibobancariodepagodecontribuciones,productosyaprovechamientosfederales-26-06-26 (6).pdf', '2026-07-02', '2026-07-03 05:37:33', '2026-07-20 13:27:35'),
(3, 3, 3, 'payments/B0xc0OruD1ZvxACmgV67Kpnw2iVJjrC6m4G9eX03.pdf', 'OC403 LEONCIO GLEZ M GASOLINA 060726.pdf', '2026-07-06', '2026-07-07 00:39:32', '2026-07-07 00:39:32'),
(4, 4, 3, 'payments/3qaurwzQjBTJFxfPbi5qoVAOAV6xIAFF62fBDEM6.pdf', 'OC404 PLOMERIA MEX DEL CENTRO SA DE CV DISCOS DE CORTE VAR.pdf', '2026-07-06', '2026-07-07 01:49:20', '2026-07-07 01:49:20'),
(5, 6, 3, 'payments/r8ensorcnJnzszYk4ZzSs8woIm8Ghsv1aQ2G7OOM.pdf', 'OC406 INDUSTRIAS NOVACERAMIC SA DE CV TARIMA  060726.pdf', '2026-07-06', '2026-07-07 03:05:16', '2026-07-07 03:05:16'),
(6, 8, 3, 'payments/EAq9xYknc8hTlhXcYFr2TiUTpSm0aY6VKYAIxclG.pdf', 'PAGO_PASE JUAN CARLOS_ FARMASOMA_070726.pdf', '2026-07-08', '2026-07-08 06:17:52', '2026-07-08 06:17:52'),
(7, 11, 3, 'payments/td13hidzWDOcRgUZP3RoFesCtTcvmaHofFCHMqtx.pdf', 'OC411 LUIS FERNANDO GOMEZ S RECARGA OXIGENO Y ACETILENO, BOQUILLA 080726.pdf', '2026-07-08', '2026-07-08 23:08:12', '2026-07-08 23:08:12'),
(8, 10, 3, 'payments/cmrOGpzufq7rRaKwtZUIZgpZAKfUNWaxS5TdjJQB.pdf', 'pago luis carlos damian tostado 4 pz toner CF360 080726.pdf', '2026-07-08', '2026-07-09 04:07:52', '2026-07-09 04:07:52'),
(9, 9, 3, 'payments/bSyJIDie52aYmPTXuHDSrw1yq0ko7KF2Vo0OGAuU.pdf', 'PAGO OC409 TONER_LUIS CARLOS DAMIAN_3 SELLOS ESPECIALES CM PRODIFEM_080726.pdf', '2026-07-08', '2026-07-09 04:11:08', '2026-07-09 04:11:08'),
(10, 15, 3, 'payments/QHhNJvS2Bg9FE89Szs0OxkshvKikIJOAAJiDQSlG.pdf', 'PAGO MANTTO DE CONDOMINIO BC 245-605 julio26.pdf', '2026-07-09', '2026-07-10 00:36:14', '2026-07-10 00:36:14'),
(11, 17, 3, 'payments/WxSqu5bYCiKNJOUN2O8U1e32tRtdFDC1OknJafWG.pdf', 'pago servicio de animación publicitaria toluca 11 y 12  jul26 090726.pdf', '2026-07-09', '2026-07-10 00:54:48', '2026-07-10 00:54:48'),
(12, 16, 3, 'payments/t68UM6EhfeXO8KHv2skHQYujFFLkctFPUNoy8Cb1.pdf', 'PEDIDO AB6669858 CYBERPUERTA TARJEA DE VIDEO NVIDIA 090726.pdf', '2026-07-09', '2026-07-10 01:25:07', '2026-07-10 01:25:07'),
(13, 18, 3, 'payments/UnmjZqUomb70stvxNhLtpLVA4cGVz9iRmw2aUusK.pdf', 'OC418 JULIO CESAR CARRILLO MOR RODAMIENTOS 090726.pdf', '2026-07-09', '2026-07-10 01:48:59', '2026-07-10 01:48:59'),
(14, 21, 3, 'payments/OafxRAssfH3ggHrK66ZtitEDsvIFxMEjBaS6WZ9o.pdf', 'PAGO OC419 NOE PILLADO C CALDO HIDROLIZADO DE CASEINA DE SOJA 090726.pdf', '2026-07-09', '2026-07-10 03:49:09', '2026-07-10 03:49:09'),
(15, 26, 3, 'payments/9fXd8idzSeOLMAZDuJQZWwgc45KRQYeIBtGQXV9j.pdf', 'OC426 BENJAMIN PERDIGON N MATILLO DEMOLEDOR 30 KG 0130726.pdf', '2026-07-13', '2026-07-14 01:31:17', '2026-07-14 01:31:17'),
(16, 25, 3, 'payments/qwVeldnDj37aUMCKiQrrn2NuSZgF7sdSHMnfifEE.pdf', 'pago fact OC425 F- bertha guadalupe garcia rpbi 130726.pdf', '2026-07-13', '2026-07-14 01:51:31', '2026-07-21 00:53:20'),
(17, 27, 3, 'payments/dXy2a3DVp04A9dOXhqpdcNUovNzhJlXIaxRrD6fx.pdf', 'BD5B34 LUIS ENRIQUE GLEZ C  flete de mdicamentos 150726.pdf', '2026-07-15', '2026-07-16 00:29:58', '2026-07-16 00:29:58'),
(18, 30, 3, 'payments/Qtcrr0C45MIGIGJig4NEOv5qXbGVwZ53IOyl74Fu.pdf', 'PASE_ JUANCARLOS_FARMASOMA_150726.pdf', '2026-07-16', '2026-07-16 06:42:31', '2026-07-16 06:42:31'),
(19, 29, 3, 'payments/EkrMYhMueAxVbUXPF7Z2D4AgJelo0YAwcAB5zXGy.pdf', 'PAGO MAYTE VALDEZ _CINTA RIBBON ZEBRA  ZEBRA_13972_OC429_PRODIFEM_160726.pdf', '2026-07-16', '2026-07-17 03:10:48', '2026-07-17 03:10:48'),
(20, 31, 3, 'payments/M5uLeMSh8CJqiudXAO2QD9pD4ZI5UKGfhBqCVZyW.pdf', 'PEDIDO 1067287 OC431 INTERCOMPRAS 2TABLET 8GB RAM 128 GB LENOVO.pdf', '2026-07-16', '2026-07-17 03:11:44', '2026-07-17 03:11:44'),
(21, 32, 3, 'payments/0leeG6xPkW8pdMXEQsoJjHF03JAEFT4MUto6sYI6.pdf', 'PAGO OC432 IND PLATICAS MED BOLSA EVA 600 250ML 160726.pdf', '2026-07-16', '2026-07-17 03:32:44', '2026-07-17 03:32:44'),
(22, 33, 3, 'payments/s63MBCgMNgRVXiDMI3ggUj0kCzGLFCCJLrEqmcRj.pdf', 'PAGO_LUIS FERNANDO GOMEZ_OC433_170726.pdf', '2026-07-20', '2026-07-20 23:18:47', '2026-07-20 23:18:47'),
(23, 34, 3, 'payments/aKRv8BV4jrTUKJzuLYHQWoAYaw7DsX2ZK17uo7zE.pdf', 'PAGO_LEONCIO GONZALEZ_ OC434_170726.pdf', '2026-07-20', '2026-07-20 23:20:48', '2026-07-20 23:20:48'),
(24, 35, 3, 'payments/EdqBvlCcKs9bJfHGIPJTxeY4ohkDy7KCaOgpdfUP.pdf', 'PASE TAG JUAN CARLOS AVEO  1,500 170926.pdf', '2026-07-20', '2026-07-20 23:21:24', '2026-07-20 23:21:24'),
(25, 36, 3, 'payments/6iR9WudbvwBHS70xCC108rcCFcUBdqAIpvwRJtmQ.pdf', 'OC436 SOTA & EMP S DE RL DE CV RENTA VIBRADOR C CHICOTE 170726.pdf', '2026-07-20', '2026-07-20 23:22:03', '2026-07-20 23:22:03'),
(26, 37, 3, 'payments/W8OCcCZH7bxhxrLMHUB1gMeLay1vmUx7TsFiZ47o.pdf', 'OC437 ACEROS GALAXY SA DE CV ANGULOS 4X5-8 200726.pdf', '2026-07-20', '2026-07-21 03:02:17', '2026-07-21 03:02:17'),
(27, 45, 3, 'payments/xpLISPlmYIunMQ8w2l24efJ3tVU8hIf0lhmO0OQm.pdf', 'OC445LUIS ENRIQUE GLEZ C  flete de mdicamentos 210726.pdf', '2026-07-21', '2026-07-21 23:09:54', '2026-07-21 23:09:54'),
(28, 46, 3, 'payments/TzMCVe7VSjpmf3Jcf76TMLmDH55xefjjlCYAf0mV.pdf', 'PAGO TONER_LUIS CARLOS DAMIAN_10 PZAS 58A PRODIFEM_210726.pdf', '2026-07-21', '2026-07-22 00:15:29', '2026-07-22 00:15:29'),
(29, 47, 3, 'payments/Q5yoZjsJecyJl8YO4VwRRYWaxVebHpfnDXLWXUm0.pdf', 'PASE TAG JUAN CARLOS AVEO $2,000 220726.pdf', '2026-07-22', '2026-07-23 00:50:53', '2026-07-23 00:50:53'),
(30, 50, 3, 'payments/GaxicrHM0JVG4wYZrNKejbs5dlcTbdAJPjUJnDOO.pdf', 'pago OC450 SALUCOM SA VITAFUSIN PEDIATRICO Y GLUCONATO 230723.pdf', '2026-07-23', '2026-07-24 04:42:54', '2026-07-24 04:42:54'),
(31, 52, 3, 'payments/6Ww3M5i77fhvEs5y4L377qWMmWuFW16U1poFZ7rx.pdf', 'PAGO OC452 IND PLATICAS MED BOLSA EVA MED VAR 240726.pdf', '2026-07-23', '2026-07-24 04:43:24', '2026-07-24 23:19:33'),
(32, 53, 3, 'payments/hiQLeVIIf3lW7AlHvJ7RFV3KnNRrAUJhYEgRfQ1U.pdf', 'pago OC453 OVERPHARMA equipo de infusion intra venosa 230726.pdf', '2026-07-23', '2026-07-24 04:43:44', '2026-07-24 04:43:44'),
(33, 48, 3, 'payments/pLzdZPzPd0vokAa4I9w78aSwXN3ZMdYUhVYyyKdw.pdf', 'PAGO MAYTE VALDEZ _ETIQUETAS ZEBRA_OC448_PRODIFEM_230726.pdf', '2026-07-23', '2026-07-24 04:44:10', '2026-07-24 04:44:10'),
(34, 51, 3, 'payments/CU2qXw12AbzgjEyj1w4unMpbPXbmd7yJVMOHb9b2.pdf', 'PASE TAG JUAN CARLOS AVEO $1,000.00 230726.pdf', '2026-07-23', '2026-07-24 04:48:34', '2026-07-24 04:48:34'),
(35, 49, 3, 'payments/oxAZQZiOHGnuOgSno2UxT2L7t5kWDt5cOeWTOx2d.pdf', 'PAGO DANIEL GARCIA VERGARA LLANTAS KANGOO 4 230726.pdf', '2026-07-23', '2026-07-24 04:51:33', '2026-07-24 04:51:33'),
(36, 54, 3, 'payments/PTS3HfWRUGbIBsXaiQolyYl4mo5AD6D4djxTye86.pdf', 'OC454  FRESENIUS KABI smoflipid 500 ml,cloruro de sodio 230726.pdf', '2026-07-24', '2026-07-24 07:48:09', '2026-07-24 07:48:09'),
(37, 55, 3, 'payments/oT9aYM6CwiMHIphWeWYo9Bvmt8giZn5FBQo5BsA6.pdf', 'OC455 NOE FLORES VARGAS PAGO GASONIA 240726.pdf', '2026-07-24', '2026-07-25 03:21:24', '2026-07-25 03:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_receipts`
--

CREATE TABLE `purchase_order_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `received_on` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_receipts`
--

INSERT INTO `purchase_order_receipts` (`id`, `purchase_order_id`, `received_by`, `file_path`, `original_name`, `invoice_number`, `received_on`, `created_at`, `updated_at`) VALUES
(1, 32, 7, 'receipts/nOB6EJENLdwxQyqbCcDbBB6Yl7rqal7bHCaA7gjb.pdf', 'OC432_IPM_FAC 29340.pdf', '29340', '2026-07-22', '2026-07-23 01:07:47', '2026-07-23 01:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_receipt_items`
--

CREATE TABLE `purchase_order_receipt_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_receipt_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED NOT NULL,
  `received_quantity` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_receipt_items`
--

INSERT INTO `purchase_order_receipt_items` (`id`, `purchase_order_receipt_id`, `purchase_order_item_id`, `received_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 49, 600.00, '2026-07-23 01:07:47', '2026-07-23 01:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_services`
--

CREATE TABLE `recurring_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_account` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(14,2) NOT NULL,
  `validity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_interval_days` int(10) UNSIGNED NOT NULL DEFAULT '30',
  `due_days_after_cutoff` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `is_domiciled` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `cutoff_day` smallint(5) UNSIGNED DEFAULT NULL,
  `cutoff_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `cutoff_year` smallint(5) UNSIGNED DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recurring_services`
--

INSERT INTO `recurring_services` (`id`, `folio`, `holder`, `company_name`, `bank`, `payer_account`, `branch`, `service_name`, `provider`, `service_number`, `category`, `cost`, `validity`, `payment_interval_days`, `due_days_after_cutoff`, `is_domiciled`, `start_date`, `cutoff_day`, `cutoff_month`, `cutoff_year`, `reference`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SRV-001', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'AMEX', 'DUP AMEX', NULL, 'IZZI', 'IZZI', '45665994', 'Telefonia Fija', 389.00, 'Indefinido', 30, 0, 1, '2026-06-13', NULL, NULL, NULL, 'San Francisco 524-C', NULL, 'active', 2, '2026-07-03 04:27:09', '2026-07-23 04:28:29'),
(2, 'SRV-002', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'BB', '0201', NULL, 'Toner Genérico', 'LUIS CARLOS DAMIAN CARMONA', '5968156', 'Otros', 5490.00, 'Indefinido', 30, 0, 0, '2026-07-03', NULL, NULL, NULL, 'TONER 58A', NULL, 'inactive', 2, '2026-07-04 00:53:37', '2026-07-08 05:33:00'),
(3, 'SRV-003', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'bb', '0201', NULL, 'Jarcicam S.A. de C.V.', 'JARCICAM SA DE CV', '955311', 'Otros', 846.00, 'Indefinido', 30, 0, 0, '2026-07-03', NULL, NULL, NULL, 'cot 4820', NULL, 'inactive', 2, '2026-07-04 00:55:48', '2026-07-08 05:33:09'),
(4, 'SRV-004', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'BB', '0201', NULL, 'Polietilenos Charly S.A. de C.V.', 'POLIETILENOS CHARLY SA DE CV', '193818', 'Otros', 1000.00, 'Indefinido', 30, 0, 0, '2026-07-03', NULL, NULL, NULL, '59035', NULL, 'inactive', 2, '2026-07-04 00:57:28', '2026-07-08 05:33:13'),
(5, 'SRV-005', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'BB', '0201', NULL, 'Papeleria', 'PROVEEDORA DE OFICINAS Y DESPACHOS', '33335', 'Otros', 5657.25, 'Indefinido', 30, 0, 0, '2026-07-03', NULL, NULL, NULL, 'cot 592214', NULL, 'inactive', 2, '2026-07-04 00:59:30', '2026-07-08 05:33:18'),
(6, 'SRV-006', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'BB', '0201', NULL, 'Recolección RPBI', 'BERTHA GUADALUPE GARCIA DIAZ', '28693', 'Otros', 5063.73, 'Indefinido', 30, 0, 0, '2026-07-03', NULL, NULL, NULL, 'F 1909', NULL, 'inactive', 2, '2026-07-04 01:02:55', '2026-07-16 07:05:28'),
(7, 'SRV-007', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'BANORTE', '00', NULL, 'Telmex', 'TELMEX', '5556694024', 'Telefonia Fija', 217.00, 'Indefinido', 30, 30, 0, '2026-01-02', 2, NULL, NULL, 'Farmacia CPC Sn Fco 516', NULL, 'active', 2, '2026-07-04 01:05:45', '2026-07-25 03:26:00'),
(8, 'SRV-008', 'Gustavo Diaz Martinez', 'Gustavo Diaz Martinez', 'BB', '171228700201', NULL, 'Telmex', 'TELMEX', '5510563077', 'Telefonia Fija', 399.00, 'Indefinido', 30, 0, 0, '2026-06-29', NULL, NULL, NULL, '55105630770000399003/DV4', NULL, 'active', 2, '2026-07-07 07:18:29', '2026-07-07 07:18:29'),
(9, 'SRV-009', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '0268029260201', NULL, 'AT&T', 'AT&T', '628963680', 'Telefonia Celular', 3257.15, 'Indefinido', 30, 0, 0, '2026-07-15', NULL, NULL, NULL, 'Junio,2026', NULL, 'active', 2, '2026-07-09 07:15:10', '2026-07-09 07:24:37'),
(10, 'SRV-010', 'Biozig S.A. de C.V.', 'Biozig S.A. de C.V.', 'BB', '0268029260201', NULL, 'Total Play', 'TOTAL PLAY', '0107095769 / 5590795326', 'Telefonia Fija', 608.00, 'Indefinido', 30, 10, 0, '2026-07-10', NULL, NULL, NULL, '0', NULL, 'active', 2, '2026-07-14 02:03:52', '2026-07-24 00:14:58'),
(11, 'SRV-011', 'Grilsa S.A. de C.V.', 'Grilsa S.A. de C.V.', 'BB', '03018900014799627', NULL, 'Telmex', 'TELMEX', '5521241071 / DV 8', 'Telefonia Fija', 389.00, 'Indefinido', 30, 14, 0, '2026-06-30', NULL, NULL, NULL, 'Dr. Atl 254', NULL, 'active', 2, '2026-07-16 06:43:25', '2026-07-23 04:45:15'),
(12, 'SRV-012', 'Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V.', 'Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V.', 'BB', '030180900045065689', NULL, 'Telmex', 'TELMEX', '5511139413 / DV 7', 'Telefonia Fija', 399.00, 'Indefinido', 30, 22, 0, '2026-06-30', NULL, NULL, NULL, 'Concepción Beistegui 113', NULL, 'active', 2, '2026-07-16 06:48:13', '2026-07-23 04:43:21'),
(13, 'SRV-013', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '030180900026930364', NULL, 'Telmex', 'TELMEX', '5551294269 / DV 2', 'Telefonia Fija', 549.00, 'Indefinido', 30, 19, 0, '2026-06-30', NULL, NULL, NULL, 'Concepción Beistegui 113', NULL, 'active', 2, '2026-07-16 06:56:14', '2026-07-23 04:44:11'),
(14, 'SRV-014', 'Denise Medina Medina', 'Denise Medina Medina', 'BB', '222987580014', NULL, 'CFE', 'COMISIÓN FEDERAL DE ELECTRICIDAD', '512110100761', 'Luz', 98.00, 'Indefinido', 60, 16, 0, '2026-07-07', NULL, NULL, NULL, '015121101007612607230000000989', NULL, 'active', 2, '2026-07-16 07:15:37', '2026-07-16 07:15:37'),
(15, 'SRV-015', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '0268029260201', NULL, 'Telmex', 'TELMEX', '772688149 / DV2', 'Telefonia Fija', 399.00, 'Indefinido', 30, 10, 0, '2026-07-10', NULL, NULL, NULL, 'Hidalgo', NULL, 'active', 2, '2026-07-16 07:23:38', '2026-07-23 04:38:36'),
(16, 'SRV-016', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'BB', '465969790201', NULL, 'Telcel', 'Radiomovil Dipsa S.A. de C.V.', '1320111000', 'Telefonia Celular', 538.00, 'Indefinido', 30, 20, 0, '2026-07-02', NULL, NULL, NULL, '0', NULL, 'active', 2, '2026-07-17 23:31:06', '2026-07-21 03:03:43'),
(17, 'SRV-017', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'BB', '175335060201', NULL, 'Telcel seq puebla', 'Radiomovil Dipsa S.A. de C.V.', '96557970', 'Telefonia Celular', 12801.76, 'Indefinido', 30, 30, 0, '2026-07-02', NULL, NULL, NULL, 'Seq Puebla', NULL, 'active', 2, '2026-07-18 00:35:39', '2026-07-23 04:39:13'),
(18, 'SRV-018', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'BB', '465969790201', NULL, 'Clinica Pediatrica del Crecimiento S. de C.V.', 'TOTAL PLAY', '0140508339 / 2201775601', 'Telefonia Fija', 230.00, 'Indefinido', 30, 30, 0, '2026-07-19', NULL, NULL, NULL, '0', 'Sequoia Puebla', 'active', 2, '2026-07-18 02:08:46', '2026-07-24 00:15:27'),
(19, 'SRV-019', 'Centro Pediátrico Gune S.C.', 'Centro Pediátrico Gune S.C.', 'BB', '5591279018', NULL, 'Telmex', 'TELMEX', '5591279018 / DV 6', 'Telefonia Fija', 958.00, 'Indefinido', 30, 30, 0, '2026-06-30', NULL, NULL, NULL, 'Seq. Santa Fe', NULL, 'active', 2, '2026-07-18 02:17:35', '2026-07-23 04:37:05'),
(20, 'SRV-020', 'Tric S.A. de C.V.', 'Tric S.A. de C.V.', 'BB', '00', NULL, 'Total Play', 'TOTAL PLAY', '0107298233 / 7229382168', 'Telefonia Fija', 690.00, 'Indefinido', 30, 30, 0, '2026-06-16', NULL, NULL, NULL, 'Toluca', NULL, 'active', 2, '2026-07-18 03:20:22', '2026-07-24 00:53:17'),
(21, 'SRV-021', 'Sociedad Pediátrica Denla S.A. de C.V.', 'Sociedad Pediátrica Denla S.A. de C.V.', 'BANORTE', '072180000141269661', NULL, 'Telmex', 'TELMEX', '5555434079 / DV 7', 'Telefonia Fija', 389.00, 'Indefinido', 30, 3, 0, '2026-07-30', NULL, NULL, NULL, 'Sn Fco 516 / L2', NULL, 'active', 2, '2026-07-21 02:40:03', '2026-07-23 04:36:15'),
(22, 'SRV-022', 'Sociedad Pediátrica Denla S.A. de C.V.', 'Sociedad Pediátrica Denla S.A. de C.V.', 'BANORTE', '072180000141269661', NULL, 'Telmex', 'TELMEX', '5555231099 / DV 3', 'Telefonia Fija', 279.00, 'Indefinido', 30, 3, 0, '2026-07-30', NULL, NULL, NULL, 'Sn Fco 516 / L1', NULL, 'active', 2, '2026-07-21 02:41:35', '2026-07-23 04:35:01'),
(23, 'SRV-023', 'Sociedad Pediátrica Denla S.A. de C.V.', 'Sociedad Pediátrica Denla S.A. de C.V.', 'BANORTE', '072180000141269661', NULL, 'Telmex', 'TELMEX', '5555366906 / DV 2', 'Telefonia Fija', 188.00, 'Indefinido', 30, 3, 0, '2026-07-30', NULL, NULL, NULL, 'SN FCO 516 / L3', NULL, 'active', 2, '2026-07-21 02:42:29', '2026-07-23 04:34:32'),
(24, 'SRV-024', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'LINEA', '030180900020097973', NULL, 'El Águila Compañía de Seguros S.A. de C.V.', 'El Águila Compañía de Seguros S.A. de C.V.', 'Póliza RC 51103880282', 'Otros', 15475.42, 'Anual', 365, 365, 0, '2026-07-27', NULL, NULL, NULL, 'Póliza RC / kangoo 25-26', NULL, 'active', 2, '2026-07-21 03:42:24', '2026-07-23 04:33:15'),
(25, 'SRV-025', 'Distritur S.A. de C.V.', 'Distritur S.A. de C.V.', 'BANORTE', '072180000144305412', NULL, 'CFE', 'COMISIÓN FEDERAL DE ELECTRICIDAD', '147500200015', 'Luz', 2461.00, 'Indefinido', 30, 30, 0, '2026-06-16', NULL, NULL, NULL, 'Rep chile 34', NULL, 'active', 2, '2026-07-22 01:51:31', '2026-07-23 04:32:49'),
(26, 'SRV-026', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '030180900020097973', NULL, 'CFE', 'COMISIÓN FEDERAL DE ELECTRICIDAD', '969000001083', 'Luz', 6269.00, 'Indefinido', 60, 60, 0, '2026-05-05', NULL, NULL, NULL, '01 969000001083 260724 000006269 4', NULL, 'active', 2, '2026-07-22 01:55:11', '2026-07-22 01:55:11'),
(27, 'SRV-027', 'Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.', 'Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.', 'BANORTE', '00', 'Sn Fco 524,', 'CFE', 'COMISIÓN FEDERAL DE ELECTRICIDAD', '977160806625', 'Luz', 31364.00, 'Indefinido', 30, 30, 0, '2026-07-18', 5, NULL, NULL, 'Sn Fco 524', NULL, 'active', 2, '2026-07-23 01:05:40', '2026-07-24 07:54:25'),
(28, 'SRV-028', 'Denise Medina Medina', 'Denise Medina Medina', 'BB', '00', NULL, 'Telmex', 'TELMEX', '5556527665 / DV 5', 'Telefonia Fija', 785.00, 'Indefinido', 30, 30, 0, '2026-07-02', NULL, NULL, NULL, 'Fuentes del Pedregal', NULL, 'active', 2, '2026-07-23 01:10:55', '2026-07-23 04:24:15'),
(29, 'SRV-029', 'Findelz S.A. de C.V.', 'Findelz S.A. de C.V.', 'BB', '00', NULL, 'Telmex', 'TELMEX', '5558129349 / DV 5', 'Telefonia Fija', 399.00, 'Indefinido', 30, 30, 0, '2026-07-02', NULL, NULL, NULL, 'Carr Mex-Tol 3054', NULL, 'active', 2, '2026-07-23 01:12:53', '2026-07-23 04:22:35'),
(30, 'SRV-030', 'Durexa, S.A. de C.V.', 'Durexa, S.A. de C.V.', 'BANREGIO', '00', NULL, 'Telmex', 'TELMEX', '5558132375 / DV 4', 'Telefonia Fija', 399.00, 'Indefinido', 30, 30, 0, '2026-07-02', NULL, NULL, NULL, 'Sria Marina 536-A', NULL, 'active', 2, '2026-07-23 01:31:53', '2026-07-23 04:21:58'),
(31, 'SRV-031', 'Sociedad Pediátrica Denla S.A. de C.V.', 'Sociedad Pediátrica Denla S.A. de C.V.', 'BANORTE', '00', NULL, 'Telmex', 'TELMEX', '5555367756 / DV 5', 'Telefonia Fija', 1399.00, 'Indefinido', 30, 30, 0, '2026-07-08', NULL, NULL, NULL, 'SEQ VENTAS', NULL, 'active', 2, '2026-07-23 01:34:22', '2026-07-23 04:21:13'),
(32, 'SRV-032', 'Gustavo Diaz Martinez', 'Gustavo Diaz Martinez', 'BB', '00', NULL, 'Telmex', 'TELMEX', '5510563077 /DV 4', 'Telefonia Fija', 798.00, 'Indefinido', 30, 30, 0, '2026-06-29', NULL, NULL, NULL, 'Pedro Moreno 101', NULL, 'active', 2, '2026-07-23 01:36:11', '2026-07-23 04:20:43'),
(33, 'SRV-033', 'Gustavo Diaz Martinez', 'Gustavo Diaz Martinez', 'BB', '00', NULL, 'Telmex', 'TELMEX', '5559191342 / DV 4', 'Telefonia Fija', 237.00, 'Indefinido', 30, 30, 0, '2026-06-22', NULL, NULL, NULL, 'Rio Nilo 84', NULL, 'active', 2, '2026-07-23 01:37:59', '2026-07-23 04:19:51'),
(34, 'SRV-034', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '00', NULL, 'Telmex', 'TELMEX', '8110985210 / DV 7', 'Telefonia Fija', 399.00, 'Indefinido', 30, 30, 0, '2026-06-28', NULL, NULL, NULL, 'Monterrey', NULL, 'active', 2, '2026-07-23 01:39:23', '2026-07-23 04:19:05'),
(35, 'SRV-035', 'Vidicron S.A. de C.V.', 'Vidicron S.A. de C.V.', 'BB', '00', NULL, 'Telmex', 'TELMEX', '5515492955 / DV 1', 'Telefonia Fija', 236.00, 'Indefinido', 30, 30, 0, '2026-06-11', NULL, NULL, NULL, 'Sec Marina 538', NULL, 'active', 2, '2026-07-23 01:41:36', '2026-07-23 04:18:42'),
(36, 'SRV-036', 'Distritur S.A. de C.V.', 'Distritur S.A. de C.V.', 'BANORTE', '00', NULL, 'Telmex', 'TELMEX', '5555104412 / DV3', 'Telefonia Fija', 799.00, 'Indefinido', 30, 30, 0, '2026-06-30', NULL, NULL, NULL, 'Rep de Chile 34', NULL, 'active', 2, '2026-07-23 01:44:08', '2026-07-23 04:18:17'),
(37, 'SRV-037', 'Distrivideo S.A. de C.V.', 'Distrivideo S.A. de C.V.', 'BB', '00', '16 de Sep', 'Telmex', 'TELMEX', '5555102829 / DV9', 'Telefonia Fija', 798.00, 'Indefinido', 30, 30, 0, '2026-06-03', 5, NULL, NULL, '16 de Sep', NULL, 'active', 2, '2026-07-23 01:45:47', '2026-07-24 08:05:45'),
(38, 'SRV-038', 'Consurent, S.A. de C.V.', 'Consurent, S.A. de C.V.', 'BANORTE', '00', 'Sn Fco 524', 'Agua', 'TESORERIA', '1939574572010005', 'Agua', 786.00, 'Indefinido', 60, 60, 0, '2026-05-29', 5, NULL, NULL, 'Sn Fco 524 / 3 BIM 2026', NULL, 'active', 2, '2026-07-23 02:09:36', '2026-07-24 08:05:30'),
(39, 'SRV-039', 'Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V.', 'Semex Seguros Mexicanos, Agente de Seguros y de Fianzas S.A. de C.V.', 'BANORTE', '00', 'Concepción Beistegui', 'Agua', 'TESORERIA', '1939532486010008', 'Agua', 1094.00, 'Indefinido', 60, 60, 0, '2026-05-27', 5, NULL, NULL, 'Concepción Beistegui / 3 BIM 2026', NULL, 'active', 2, '2026-07-23 02:11:47', '2026-07-24 08:05:20'),
(40, 'SRV-040', 'Sociedad Pediátrica Denla S.A. de C.V.', 'Sociedad Pediátrica Denla S.A. de C.V.', 'BANORTE', '00', 'Sn Fco 516', 'Agua', 'TESORERIA', '1939581602010004', 'Agua', 2004.00, 'Indefinido', 60, 60, 1, '2026-05-29', 5, NULL, NULL, 'Sn Fco 516 /3 BIM 2026', NULL, 'active', 2, '2026-07-23 02:16:59', '2026-07-24 08:05:03'),
(41, 'SRV-041', 'Sorem, S.A. de C.V.', 'Sorem, S.A. de C.V.', 'BANREGIO', '00', 'Euclides / 3 BIM 26', 'Agua', 'TESORERIA', '184440941001000', 'Agua', 4171.00, 'Indefinido', 60, 60, 0, '2026-05-28', 5, NULL, NULL, 'Euclides / 3 BIM 26', NULL, 'active', 2, '2026-07-23 04:56:18', '2026-07-24 08:04:37'),
(42, 'SRV-042', 'Distrilux, S.A. de C.V.', 'Distrilux, S.A. de C.V.', 'BANREGIO', '00', 'Dr. Atl 254', 'Agua', 'TESORERIA', '2146188351010008', 'Agua', 78.00, 'Indefinido', 60, 60, 0, '2026-05-28', 5, NULL, NULL, 'Dr. Atl 254', NULL, 'active', 2, '2026-07-23 04:56:18', '2026-07-24 08:04:48'),
(43, 'SRV-043', 'Gustavo Diaz Martinez', 'Gustavo Diaz Martinez', 'BB', '00', 'Circunvalación 306', 'Total Play', 'TOTAL PLAY', '0117105969 / 5589906571', 'Telefonia Fija', 550.00, 'Indefinido', 30, 30, 0, '2026-07-23', 5, NULL, NULL, 'Circunvalación 306', NULL, 'active', 2, '2026-07-23 07:24:33', '2026-07-24 08:04:10'),
(44, 'SRV-044', 'Gustavo Diaz Martinez', 'Gustavo Diaz Martinez', 'BB', '00', 'Reforma 2752', 'Telmex', 'TELMEX', '5552597876 / DV1', 'Telefonia Fija', 754.00, 'Indefinido', 30, 30, 0, '2026-04-01', 1, NULL, NULL, 'Reforma 2752', NULL, 'active', 2, '2026-07-23 07:29:47', '2026-07-24 22:42:05'),
(45, 'SRV-045', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '00', 'Concepción Beistegui113-C', 'Total Play', 'TOTAL PLAY', '5596337639 / 0134258830', 'Telefonia Fija', 640.00, 'Indefinido', 30, 30, 0, '2026-06-26', 5, NULL, NULL, '26 junio -25 Julio 2026', NULL, 'active', 2, '2026-07-23 07:43:43', '2026-07-24 08:03:20'),
(46, 'SRV-046', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'Clínica Pediátrica Del Crecimiento S.A. de C.V.', 'BB', '00', 'Santa Fe', 'CFE', 'CFE', '13806602281', 'Luz', 7629.00, 'Indefinido', 60, 60, 0, '2026-05-17', 5, NULL, NULL, '18may-17jul26', NULL, 'active', 2, '2026-07-24 00:28:39', '2026-07-24 07:58:00'),
(47, 'SRV-047', 'Prodifem S.A. de C.V.', 'Prodifem S.A. de C.V.', 'BB', '00', 'Fumigación CM', 'Beatriz Habib Hadad', 'Total Control', 'Certificados de fumigación', 'Otros', 1334.00, 'Indefinido', 30, 30, 0, '2026-06-26', 5, NULL, NULL, 'certificados', NULL, 'active', 2, '2026-07-24 01:28:52', '2026-07-24 07:57:26'),
(48, 'SRV-048', 'Denise Medina Medina', 'Denise Medina Medina', 'BB', '00', 'B. California 245', 'CFE', 'CFE', '972930200486', 'Luz', 125.00, 'Indefinido', 30, 30, 0, '2026-05-15', 5, NULL, NULL, 'B. CALIFORNIA 245', NULL, 'active', 2, '2026-07-24 01:38:06', '2026-07-24 07:55:24'),
(49, 'SRV-049', 'Farmasoma S.A. de C.V.', 'Farmasoma S.A. de C.V.', 'BB', '00', 'Ikon / Juan Avilés', 'SEGURO RC', 'El Águila Compañía de Seguros S.A. de C.V.', 'Póliza RC 51103880274', 'Otros', 6004.81, 'Anual', 365, 365, 0, '2026-07-28', 28, NULL, NULL, 'Póliza RC / Ikon /', NULL, 'active', 2, '2026-07-24 03:44:35', '2026-07-24 07:55:07');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_service_receipts`
--

CREATE TABLE `recurring_service_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recurring_service_id` bigint(20) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `period_start` date DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `support_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_on` date DEFAULT NULL,
  `payment_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_paid_on` date DEFAULT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recurring_service_receipts`
--

INSERT INTO `recurring_service_receipts` (`id`, `recurring_service_id`, `due_date`, `period_start`, `amount`, `support_file_path`, `support_original_name`, `support_on`, `payment_file_path`, `payment_original_name`, `payment_paid_on`, `paid_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-07-13', '2026-06-13', NULL, 'service-supports/sT5wEuF803Yh3pw91rw9STOWozgCbJ9Vh70R5C7n.pdf', 'IZZI_JUNIO2026_PRODIFEM.pdf', '2026-07-02', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-03 04:30:39', '2026-07-03 04:30:39'),
(2, 4, '2026-07-03', '2026-06-03', NULL, 'service-supports/XBEzZ9XK2g9g5XHX1ULm8SGHmxSolcbTtw4UcZ0c.pdf', 'FolioFiscal1888d5f0e93d4288a75f1c4e1998326c.pdf', '2026-07-06', 'service-payments/oFhvgwrydNNdWoq8IZfuqyotUsLsLkStiGR9NW3F.pdf', 'pago polietilenos charly sa de cv bolsa para CM 2 ROLLOS DE 20X30 BOLSA.pdf', '2026-07-06', 3, 'paid', '2026-07-07 01:38:13', '2026-07-07 01:43:08'),
(3, 2, '2026-07-03', '2026-06-03', NULL, 'service-supports/9TmZLb10pPozkpFXGJj27EWilmQYxMLWGck1zS3o.pdf', 'DATL951224MY2F07650.pdf', '2026-07-06', 'service-payments/BATWjb3Et7k0xzwMXA0kEneAU9fCfZXOFnHL1BiH.pdf', 'PAGO TONER_LUIS CARLOS DAMIAN_10 PZAS 58A PRODIFEM_060726.pdf', '2026-07-06', 3, 'paid', '2026-07-07 01:39:11', '2026-07-07 01:41:35'),
(4, 3, '2026-07-03', '2026-06-03', NULL, 'service-supports/Q7MsPO5G7zoUOWaElRfouaDnsHJA3HHQtOmvQgNG.pdf', 'FA0000014879.pdf', '2026-07-06', 'service-payments/UO9q5Lv0YxfDb3dkP3zbDVotgsdZRqA7C9GM55eZ.pdf', 'pago art limpieza cm cotz 4820 020726.pdf', '2026-07-06', 3, 'paid', '2026-07-07 03:55:17', '2026-07-07 03:58:54'),
(5, 5, '2026-07-03', '2026-06-03', NULL, 'service-supports/egxkEXJB7K1d7XmZWbtRuadRbtyYXxdaEk5TTghx.pdf', 'A167564.PDF', '2026-07-06', 'service-payments/DpLOIFDIHQoVSUX1TgVfFSgyLTuGZcmcNI8xK0zf.pdf', 'pago de papeleria centro de mezclas cotz 592214 020726.pdf', '2026-07-06', 3, 'paid', '2026-07-07 03:56:43', '2026-07-07 03:59:35'),
(6, 6, '2026-07-03', '2026-06-03', NULL, 'service-supports/Ff3fmQQufkUeuDQCtTXlrzTxjJNVgTPq3dNtS6la.pdf', 'GADB850316733_Factura__1909_6F8BDE18-E9DB-480E-887F-5A074755CF66 (1).pdf', '2026-07-06', 'service-payments/YvGHPOi8Bz3w8ySv2JVjUHuc8ObqGRNag4EF5C2p.pdf', 'pago fact 1909 bertha guadalupe garcia rpbi  020726.pdf', '2026-07-06', 3, 'paid', '2026-07-07 03:57:43', '2026-07-07 04:00:09'),
(7, 7, '2026-07-18', '2026-06-18', NULL, 'service-supports/jtlZC6jiQRRo0xQhKOpgSuWoJPANNxAMCqcr0IsM.pdf', '977160806625 (3).pdf', '2026-07-06', 'service-payments/N6HOIAnmv0bPEhZA8254ElSvuXZbgzdMDgHEriF5.pdf', 'pago cfe mes 19 mayo26-18jun26 010726.pdf', '2026-07-06', 3, 'paid', '2026-07-07 03:58:28', '2026-07-07 04:00:55'),
(8, 8, '2026-07-29', '2026-06-29', NULL, 'service-supports/KMLDKhQF0vQo4awwsFV94o3HjOnNafVIZdN8Txzq.pdf', 'Recibo-Jun (15).pdf', '2026-07-07', 'service-payments/bRMsOIUhLaHhZSxikVYdjasvTkObK4DN30fH7uNF.pdf', 'telmex pedro moreno 101 julio26.pdf', '2026-07-24', 3, 'paid', '2026-07-07 07:18:59', '2026-07-24 06:53:58'),
(9, 9, '2026-07-15', '2026-06-15', NULL, 'service-supports/pkheoex0kyME5ObJXvb3KQcm0PbMH3wRSoWf4mdF.pdf', '628963680.PDF', '2026-07-09', 'service-payments/8VbOkZTLjnmKvNAU04JlnFjgeAQao0uSdb87ujr1.pdf', 'PAGO AT&T  628963680 JUNIO26 7 lineas 090726.pdf', '2026-07-09', 3, 'paid', '2026-07-09 08:17:45', '2026-07-10 00:42:04'),
(10, 10, '2026-07-10', '2026-06-10', NULL, 'service-supports/U6KZbXUuY0zlUYR0ZamvKMotFhF6w5OXpkG5487y.pdf', 'Biozig jul26.pdf', '2026-07-13', 'service-payments/p6xZQosPNmptN9YPU9rhtUbm83VGp042Z6JeEWlM.pdf', 'pago totalplay biozig 11jul-10ago26 160726.pdf', '2026-07-20', 3, 'paid', '2026-07-14 02:05:24', '2026-07-20 23:31:08'),
(11, 14, '2026-07-07', '2026-05-08', NULL, 'service-supports/he8fzcpA1OSmEfyymzMyepcDIs04gY1kNzNTurCA.pdf', '512110100761.pdf', '2026-07-16', 'service-payments/5r2U8Fgm2LGzeyQYaISeUDP4QwzbzVdD1xolCYTr.pdf', 'CFE PEDREGAL DEL LAGO LOCAL 06mayo26_07julio26 160726.pdf', '2026-07-20', 3, 'paid', '2026-07-17 01:22:53', '2026-07-20 23:28:08'),
(12, 15, '2026-07-10', '2026-06-10', NULL, 'service-supports/BGYLVlDT3sBDXye92J32RqIISbS1lpPblGkU7ljS.pdf', 'Farmasoma Hidalgo.pdf', '2026-07-16', 'service-payments/AzckQ5dez1m7G8UbkB6sEm9N4hQSvb6P1DJlcHgW.pdf', 'PAGO_TELMEX_HIDALGO_JUNIO26_FARMASOMA_160726.pdf', '2026-07-20', 3, 'paid', '2026-07-17 01:27:42', '2026-07-20 23:31:43'),
(13, 11, '2026-07-30', '2026-06-30', NULL, 'service-supports/1JffSZKlcskjCkAkENbtLO02MSaSPpDeP9MIryz7.pdf', 'Recibo-Jun (18).pdf', '2026-07-16', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-17 01:38:28', '2026-07-17 01:38:28'),
(14, 12, '2026-07-30', '2026-06-30', NULL, 'service-supports/YDezjSRoxM1T5XXowHELcO5IQLR9JMIqx770ob28.pdf', 'Recibo-Jun (18).pdf', '2026-07-16', 'service-payments/ggM5QaoPZbSmiIobjtaeYqLjdL5Vr8RAXMkMI08u.pdf', 'PAGO TELMEX SEMEX junio 26  160726.pdf', '2026-07-24', 3, 'paid', '2026-07-17 01:38:49', '2026-07-25 01:58:02'),
(15, 13, '2026-07-30', '2026-06-30', NULL, 'service-supports/uX2E8xvUIcww2A3NAI2G7oqTXqO3Qkz0JHtL8hm4.pdf', 'Recibo-Jun (19).pdf', '2026-07-16', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-17 01:39:55', '2026-07-17 01:39:55'),
(16, 16, '2026-07-02', '2026-06-02', NULL, 'service-supports/RaYsexN19Ht2sOkhBGdJBKbPyBexL5DjoVOkVPTs.pdf', 'FA0623227036.pdf', '2026-07-17', 'service-payments/R7XSMxU3UkwNdpNopPBDyO8hGtmucr9HkCiyMR8R.pdf', 'PAGO TELCEL PUE CTA 1320111000 160726.pdf', '2026-07-20', 3, 'paid', '2026-07-17 23:31:39', '2026-07-20 23:26:17'),
(17, 17, '2026-07-02', '2026-06-02', NULL, 'service-supports/ZIsAgBN3ItNBRGSMYJeTICaxaBRl0e0qDmE6yV43.pdf', 'TELCEL CPC.pdf', '2026-07-17', 'service-payments/mqhH3oVaGy6U06jGMRfOx2tRtjiNSDdGRilNXfwW.pdf', 'PAGO TELECE_CPC_MAYO-JUN26_180626.pdf', '2026-07-20', 3, 'paid', '2026-07-18 00:41:42', '2026-07-20 23:27:03'),
(18, 18, '2026-07-19', '2026-06-19', NULL, 'service-supports/1fsb0qvLDWzg8ngZ2oaHKksrWcki0akSbvgHmlrK.pdf', 'TOTAL PLAY CPC JULIO26.pdf', '2026-07-17', 'service-payments/FAABtA38qaje4sDavdmZUOrApgg0eftY4NrAgOzP.pdf', 'PAGO TOTALPLAY PUE 209junio26_19ago26 160726.pdf', '2026-07-22', 3, 'paid', '2026-07-18 02:09:21', '2026-07-22 07:29:25'),
(19, 19, '2026-07-30', '2026-06-30', NULL, 'service-supports/WQOQRIPUkUjZ8LkwjLUMyPnRsF0Qqlv41MA2qEbY.pdf', 'Recibo-Jul.pdf', '2026-07-17', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-18 02:19:12', '2026-07-18 02:19:12'),
(20, 20, '2026-07-16', '2026-06-16', NULL, 'service-supports/9Il0lJ0NXNDhRtQNtUfd5xW8zoRRgzFMwMKfwTGQ.pdf', 'TOTAL PLAY TOLUCA JULIO.pdf', '2026-07-17', 'service-payments/sXciNR9HSGm2RO7Np0skJC8zyADADZoTdkwopkcw.pdf', 'totalplay toluca 17mayo26-16junio26 280526.pdf', '2026-07-20', 3, 'paid', '2026-07-18 03:23:44', '2026-07-21 00:34:19'),
(21, 26, '2026-07-04', '2026-05-05', NULL, 'service-supports/06xCxyJ9ojrGlXVow2TWtYw6VJr37CX7u3ac36Il.pdf', '969000001083.pdf', '2026-07-21', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-22 03:09:51', '2026-07-22 03:09:51'),
(22, 27, '2026-07-18', '2026-06-18', NULL, 'service-supports/pgcX77kSrE9isAZU6BuiOQX1IXXCfYc77hgiFJO7.pdf', '977160806625 (4).pdf', '2026-07-22', 'service-payments/hXz8zMzgSVHFdXypQhNYufys2bDqxQe6OJ66jGph.pdf', 'CFE SAN FCO 524- CBTA 118jun-16jul26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:07:28', '2026-07-24 06:38:29'),
(23, 25, '2026-07-16', '2026-06-16', NULL, 'service-supports/5yabhWz32xUIybhtv0gn3GNaTZixD60f3PK3VjBm.pdf', '147500200015 (1).pdf', '2026-07-22', 'service-payments/djZ067k81RMImw9l9qkTjoCmpPhgVHy6Am1FOLmv.pdf', 'CFE CINE VENUS 16jun-14jul26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:08:10', '2026-07-24 06:48:32'),
(24, 28, '2026-07-02', '2026-06-02', NULL, 'service-supports/ERg1JajgGrKhfjlEcXulqQt2fQthGwdA1O885w9A.pdf', 'PEDREGAL JUL26.pdf', '2026-07-22', 'service-payments/iT4b74MXXxqS3O2yNysgcdrzKVQk3L3bU7qY22eD.pdf', 'PAGO TELMEX FUENTES DEL PEDREGAL 7665 JUNIO26 250626.pdf', '2026-07-22', 3, 'paid', '2026-07-23 01:11:36', '2026-07-23 02:18:19'),
(25, 29, '2026-07-02', '2026-06-02', NULL, 'service-supports/LDC8ZeN1y0QaglWyhwQPhTnJiIbjJuExp2XRd5cc.pdf', 'FINDELZ JUL26.pdf', '2026-07-22', 'service-payments/UHtxzgClo5lsvAl4on3jmtu2CAPAe3BnezYc9Gah.pdf', 'TELMEX CARR MEX-TOL 3054 FINDELZ SA DE CV julio 26.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:13:24', '2026-07-24 06:46:05'),
(26, 30, '2026-07-02', '2026-06-02', NULL, 'service-supports/oqmGVQVfANmaWjEByjhzzyoKnyp0cOlk6oLrS32v.pdf', 'DUREXA JUL26.pdf', '2026-07-22', 'service-payments/39vbGXiPkUfrFEreJF6unjfEsq883hSVTblpHjm7.pdf', 'TELMEX DUREXA MES JULIO26 SRIA DE LA MARINA 536-A.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:32:37', '2026-07-24 06:46:57'),
(27, 31, '2026-07-08', '2026-06-08', NULL, 'service-supports/mKKwl3e1u9KR8nzU9IfGQMM6scxq8iDpCdKK1lcZ.pdf', 'VENTAS SEQ JUL26.pdf', '2026-07-22', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-23 01:34:57', '2026-07-23 01:34:57'),
(28, 32, '2026-07-29', '2026-06-29', NULL, 'service-supports/fRRjdew6GW4LMOc0AAv4fYE5D5XN4qKEJRScnUVL.pdf', 'PEDRO MORENO JUL 26.pdf', '2026-07-22', 'service-payments/QiGa1jrq82CPUjRaWx3U0zB0UNgtCLIVaYocGseB.pdf', 'telmex pedro moreno 101 julio26.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:36:40', '2026-07-25 01:55:06'),
(29, 33, '2026-07-22', '2026-06-22', NULL, 'service-supports/v3mDnJxKSVIsjjajHf2IpdZK9VdSkJ9VYXrIPnzF.pdf', 'RIO NILO JUL26.pdf', '2026-07-22', 'service-payments/L9c31mxRts1O8goM64iMOpPC6u32BOXGjs5xHc0Q.pdf', 'telmex nilo alejandro flores j julio26  230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:38:29', '2026-07-24 06:49:23'),
(30, 34, '2026-07-28', '2026-06-28', NULL, 'service-supports/3nuWoEQVyj9uq6psd6CrYmeJBZPpppGDA1tZwZGQ.pdf', 'FARM MTY JUL26.pdf', '2026-07-22', 'service-payments/vKfHiLPhAift1L0nFHdsVDTiQQ1oPtdYmZSj2UzK.pdf', 'PAGO TELMEX MTY 8110985210 julio26.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:40:07', '2026-07-24 06:45:07'),
(31, 24, '2026-07-27', '2025-07-27', NULL, 'service-supports/xOFRDwc7cIeFNE6eH33bsy6uxk1IreS0dIl564aw.pdf', 'POLIZA_KAMGOOO_FARMASOMA_27072026-2027.pdf', '2026-07-22', 'service-payments/jkfMuwZoHE62wxK6aKNI6hhAuRsxivCn3EstWmCk.pdf', 'POLIZA NO. 51103880282  AMPLIA 2026-2027 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:40:40', '2026-07-25 01:13:03'),
(32, 35, '2026-07-11', '2026-06-11', NULL, 'service-supports/GsXOds9rN5xgN1t4FnCqQlNTDuTDMIgv9DC8AYTr.pdf', 'VID JULIO26.pdf', '2026-07-22', 'service-payments/UcHb4NyVPlgp8x7mojtQpdE2fjDewV5vccpEMh6Y.pdf', 'pago telmex vidicron secretaria de la marina 5558132375 junio 26.pdf', '2026-07-22', 3, 'paid', '2026-07-23 01:42:03', '2026-07-23 02:14:20'),
(33, 36, '2026-07-30', '2026-06-30', NULL, 'service-supports/Zy0iKbXtqU529rlkKDLiXaXqOytc8h1e79QH939H.pdf', 'DTTUR JUL26.pdf', '2026-07-22', 'service-payments/xEXnAfhPkLSH3xRlsqnxt72qJkca5HERmr1YrXgL.pdf', 'pago telmex julio26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:44:38', '2026-07-24 06:56:54'),
(34, 37, '2026-07-03', '2026-06-03', NULL, 'service-supports/79n5VoQk7eTroGVTBLpbU4EhcGKN1U6Mkx7bB9wV.pdf', 'DTVD JUL26.pdf', '2026-07-22', 'service-payments/K433FKz6N5BhpgFjZkIQkjPX7i9fO1uqCMPg650l.pdf', 'PAGO_TELMEX_julio26_DISTRIVIDEO_230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 01:46:21', '2026-07-24 06:47:32'),
(35, 38, '2026-07-28', '2026-05-29', NULL, 'service-supports/u33KlIWA5W5qXPaGbA1Y9wa31CdgEqt43SFQ6i0W.pdf', '1939581602010004-22072026-140500-Y.pdf', '2026-07-22', 'service-payments/i4JbKHo0Ha9Et8tdwJ5JeYXkKFFBRcMk0MHZKnJF.pdf', 'PAGO_AGUA 3BIM26_CONSURENT_230726 b.pdf', '2026-07-24', 3, 'paid', '2026-07-23 02:10:00', '2026-07-24 06:52:01'),
(36, 39, '2026-07-26', '2026-05-27', NULL, 'service-supports/IfWEWdplD4Yfdbey6RXMBfvgUzj4tYkxPf5UwjjA.pdf', '1939532486010008-22072026-141228-Y.pdf', '2026-07-22', 'service-payments/j57NqsGB5mBhqB4Abo4DTrzmBSrNXapKdAmhHyBv.pdf', 'PAGO AGUA CONCEPCION B 113  3ER BIM 26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-23 02:12:47', '2026-07-24 06:50:48'),
(37, 41, '2026-07-27', '2026-05-28', NULL, 'service-supports/Fqq2ccK8JRiWkgjnETUxF2WyTNxZQMAKEtKCcRxw.pdf', '1844409410010004-22072026-170611-Y.pdf', '2026-07-22', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-23 04:57:08', '2026-07-23 05:08:33'),
(38, 42, '2026-07-27', '2026-05-28', NULL, 'service-supports/giKs9YW0CKYXlFd2xV8S5FgI9YziXalJjbwLDpCo.pdf', '2146188351010008-22072026-164914-Y.pdf', '2026-07-22', 'service-payments/WgLSKFLNlIG2j486jAnWtQoBRCAFz1rL06JRvxZy.pdf', '3ER BIM-2026 AGUA DR. ATL 254 220726.pdf', '2026-07-23', 3, 'paid', '2026-07-23 05:09:05', '2026-07-23 23:20:01'),
(39, 40, '2026-07-28', '2026-05-29', NULL, 'service-supports/wSYD25MhEp72fsf62SD2wEJL1QKA2K8wio8pMz7N.pdf', '1939581602010004-22072026-140500-Y.pdf', '2026-07-22', 'service-payments/zTcC0gPwp1DP1jq78Rc1lwb17UfeYVLEHwq9wwjM.pdf', 'AGUA SAN FCO 516 3BIM 2026 B.pdf', '2026-07-24', 3, 'paid', '2026-07-23 05:10:48', '2026-07-24 06:52:59'),
(40, 43, '2026-07-23', '2026-06-23', NULL, 'service-supports/PQMVxiXG5XOXqvnO4xwZJECT8GBr9VvRBuA1WrzG.pdf', 'total play circunvalacion jul-ago26.pdf', '2026-07-23', 'service-payments/JxkKcBd0TFuql9Jsf2rAx0BWkCaRe92nOdmKsPaG.pdf', 'PAGO_TOTAL PLAY 23julio26 -22ago26 230723.pdf', '2026-07-24', 3, 'paid', '2026-07-23 07:33:08', '2026-07-24 06:49:58'),
(41, 46, '2026-07-16', '2026-05-17', NULL, 'service-supports/QZmGr8amW8LnC54CufgFk8t2VuzHlWX94KJ4FOg6.pdf', '138060602281 (2).pdf', '2026-07-23', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-24 00:29:01', '2026-07-24 00:29:01'),
(42, 48, '2026-07-14', '2026-06-14', NULL, 'service-supports/rPiRmD9kmX6efkQgW2ZIh9CF8cUffLWUNFR0Ku67.pdf', '972930200486.pdf', '2026-07-23', 'service-payments/n1hU28igkDQ88KGeebmXOEeXHKM9GJdr9Rr56qsx.pdf', 'CFE BC 245 114mayo26-15jul26  230726.pdf', '2026-07-24', 3, 'paid', '2026-07-24 01:38:25', '2026-07-24 06:40:32'),
(43, 49, '2026-07-28', '2025-07-28', NULL, 'service-supports/QyYv39EU5oXmQdiTupL78Lyit93FH3SYG2AFG7As.pdf', 'PF51103880274.pdf', '2026-07-23', 'service-payments/Oc0eyO9uRPReMmHaCeG2nhHcgHmmQ94eyEizstdP.pdf', 'POLIZA NO. 51100880274 2026-2027 AMPLIA 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-24 03:45:15', '2026-07-24 08:07:48'),
(44, 44, '2026-07-23', '2026-06-23', NULL, 'service-supports/2msQs06Tfy1SmPg3HXKPnzIaFepYvS3hR2tPnDUJ.pdf', 'reciboTelmex-3.pdf', '2026-07-23', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-24 03:46:16', '2026-07-24 03:46:16'),
(45, 21, '2026-07-30', '2026-06-30', NULL, 'service-supports/YGHweSyJadkzz0JmZVMadHIXluwSNf5ob8qJSZQR.pdf', 'img20260723_19485933.pdf', '2026-07-24', 'service-payments/81XkHcpyzAdXgw6GQQvEFdt8fEUosSBhsgMw3Ej4.pdf', 'telmex san fco 516  4079 julio26.pdf', '2026-07-24', 3, 'paid', '2026-07-24 07:49:22', '2026-07-24 08:11:14'),
(46, 22, '2026-07-30', '2026-06-30', NULL, 'service-supports/Yu8hueZjq7voxyCkxjlYIyJnFkHv7RCqrsIKdCyf.pdf', 'img20260723_19503173.pdf', '2026-07-24', 'service-payments/5kPsCmfcnXE7Vv5L7urWTtnrfEm1kNYeUd3lyOeZ.pdf', 'telmex san fco 516  1099 julio26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-24 07:50:55', '2026-07-24 08:11:36'),
(47, 23, '2026-07-30', '2026-06-30', NULL, 'service-supports/2xbzi2v9Mbbdmyp6yBqUWnCLmRVRJYfODW5eUMqK.pdf', 'img20260723_19520856.pdf', '2026-07-24', 'service-payments/rDHW2B2yWcpr8tiAzoEdmAiQlzg9wGnJbL5D4Aev.pdf', 'telmex san fco 516  6906 julio26 230726.pdf', '2026-07-24', 3, 'paid', '2026-07-24 07:52:25', '2026-07-24 08:12:20'),
(48, 42, '2026-07-05', '2026-06-05', NULL, 'service-supports/C5hzc2FqlktMkLMdSFJ4FVOH8AyVXINCn9j5jnMI.pdf', 'agua distrilux.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-24 23:13:39', '2026-07-24 23:13:39'),
(49, 44, '2026-07-01', '2026-06-01', NULL, 'service-supports/p31WX0NagzQ9UbOG6PGZN65IEuJPLvOAsDIfwFoi.pdf', 'reciboTelmex-3 (1).pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:24:24', '2026-07-25 03:24:24'),
(50, 46, '2026-07-05', '2026-06-05', NULL, 'service-supports/skLeoYJzUElzfj05g8FjZfPfc8tvPVv0XjRoZNqB.pdf', '138060602281 (3).pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:25:33', '2026-07-25 03:25:33'),
(51, 27, '2026-07-05', '2026-07-18', NULL, 'service-supports/Tiqh8600Rt1qM46ZcMuogZTwrDMV04WpSi44RxIZ.pdf', '977160806625 (5).pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:26:52', '2026-07-25 03:26:52'),
(52, 37, '2026-07-05', '2026-06-05', NULL, 'service-supports/E9Rn7kRJjWfX7cz4omiiphAY9uFheYfCwlXQaacY.pdf', 'Recibo-Jul (3).pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:28:06', '2026-07-25 03:28:06'),
(53, 38, '2026-07-05', '2026-06-05', NULL, 'service-supports/xkudohbbggr5S7JqFr43FknjRO6jLjDiTkRHjUSn.pdf', '1939574572010005-22072026-141437-Y.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:29:23', '2026-07-25 03:29:23'),
(54, 39, '2026-07-05', '2026-06-05', NULL, 'service-supports/A91pnQXR4CX9CfSIJ8IR1C7dYccAhgoJnecqkkEF.pdf', 'AGUA CONCEPCION 113 3ER BIM26.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:33:20', '2026-07-25 03:33:20'),
(55, 43, '2026-07-05', '2026-07-23', NULL, 'service-supports/CFC1ZXsvCrC2xLf8J3AQ2WIYYMLNQD8WLvVtyTRg.pdf', 'TOTAL PLAY CIRCUNVALCION.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:33:45', '2026-07-25 03:33:45'),
(56, 41, '2026-07-05', '2026-06-05', NULL, 'service-supports/gioxEsmm0Ikr5fxrpo4ugeHvof78cNT7SGEZtJwu.pdf', 'AGUA EUCLIDES 3ER BIM26.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:35:29', '2026-07-25 03:35:29'),
(57, 45, '2026-07-05', '2026-06-26', NULL, 'service-supports/WxicN1Dla5jyQeJ9SMsUh2rEy1IrOClW824KBItv.pdf', 'TOTAL PLAY PATRIOTIC.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:37:05', '2026-07-25 03:37:05'),
(58, 47, '2026-07-05', '2026-06-26', NULL, 'service-supports/x4lf0viF21Vdf1CmNJ8MlQ3TluW8x9FXREf9InrS.pdf', 'img20260724_15193331.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:37:32', '2026-07-25 03:37:32'),
(59, 48, '2026-07-05', '2026-06-05', NULL, 'service-supports/EawEU2kupvTWO5FoATCKKtbnZG6r0gNs8IqzNv1J.pdf', 'CFE B. CALIFORNIA.pdf', '2026-07-24', NULL, NULL, NULL, NULL, 'support-loaded', '2026-07-25 03:38:48', '2026-07-25 03:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('AFJbGItcQKGlAFF956cELjpvJOiWcGn2wW9WT4CG', NULL, '40.77.167.47', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV2FiSVVGN1VBMXVJUlFaMzNLbG9aT2p3VXpuRm5Jak5ZS0FsZDZhMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MjoiaHR0cHM6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2ludmVudGFyaW9zL2hpc3RvcmlhbCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwczovL3Nhc29yZGVuZXNkZWNvbXByYS5jb20vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1784947135),
('cs0cGP5a7L8zatjL3H2mqBGLbLUjtDJOmKEGtT5R', 1, '177.245.67.237', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTDFrVGdUQlVaSDltQmx2Z0FWVWxjeUxPT0h0NTl4UXR4NW5PTFZ6eSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ2OiJodHRwczovL3Nhc29yZGVuZXNkZWNvbXByYS5jb20vc2VydmljaW9zL21lc2VzIjtzOjU6InJvdXRlIjtzOjE1OiJzZXJ2aWNlcy5tb250aHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1784932519),
('gdEuxV3qcRvRZH51OTwrcSrHF2ZFewZNACSO6g4d', NULL, '40.77.167.30', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRXdHQXZpNHQ4NkxZT2IyWUN6VHlGWnplRnJOT25HZ0tuQ3BYaEtHbSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2NvbXByYWRvci9vcmRlbmVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vc2Fzb3JkZW5lc2RlY29tcHJhLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1784935427),
('KNmNQ1aAjdJJ2pyaeLXJNAoMljAHSvJaWSvCGHuf', NULL, '187.188.14.2', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiek9UZ0J0UnM5dFRGVzNSOFhIWnJ6d09jTUVaUHZDdHFFdEVDRDhqSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vc2Fzb3JkZW5lc2RlY29tcHJhLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1784943699),
('M45AdPTlyCEqSKCoueFoPMRq1R54iti5NJJFpa4b', 1, '187.188.14.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNVQ2dlV5MGpCVnNLM3gzM0pYdXM5NTNWNGtIQzhMRVdFS1pGTWZSQSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU2OiJodHRwczovL3Nhc29yZGVuZXNkZWNvbXByYS5jb20vZmluYW56YXMvb3JkZW5lcy12aWdlbnRlcyI7czo1OiJyb3V0ZSI7czoyMToiZmluYW5jZS5vcmRlcnMuYWN0aXZlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1784933240),
('mdKqpZGoBZbrO1ZRLcf49W5Z4j6lL5rERqdlAotv', 4, '189.147.160.169', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaEhjSDlkNVZkTTJkTlVCT1RrRlI5ZlR1YXp2cWh6SE9VSUFPTDc4SyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHBzOi8vc2Fzb3JkZW5lc2RlY29tcHJhLmNvbS9jb21wcmFkb3IvcHJvdmVlZG9yZXMiO3M6NToicm91dGUiO3M6MjE6ImJ1eWVyLnByb3ZpZGVycy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1784931074),
('NnefCJnvgVuz6CJOAmq2TNgRI2B5vC6YXCmLXANX', NULL, '192.71.224.103', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXhVZnF0d2FFVHhyNWt6RXM4WEMyYVhTVzRnTzB1N3dha1lTSHNaRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1784943149),
('OPfjbbQB6cWGtTMwlfEDdXUkVGcnLZmDOceCWt3S', 1, '177.245.67.237', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQWpWS3BKbnhoR1dBUVZWejY4TEJWR0JlMEtSbTdVVmxRZ3VlM0dFTSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU2OiJodHRwczovL3Nhc29yZGVuZXNkZWNvbXByYS5jb20vZmluYW56YXMvb3JkZW5lcy12aWdlbnRlcyI7czo1OiJyb3V0ZSI7czoyMToiZmluYW5jZS5vcmRlcnMuYWN0aXZlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1784949850),
('QThPDDkrVUITVUzYDl5203XJ3VNaQVey18Q5z5Dj', NULL, '64.69.216.78', 'Insites-scanner/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDRCZHlRVU9YSXBtckVhMHBXYjRCbHNIeTdLZ0NtelBMQm5JQ3R4ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1784928542),
('R7bnTYE7PcVW3I4yMFB4tRgDARcKBWxEJcczsw9K', NULL, '187.168.64.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGtKRkJVblBSem1zSVpUOTdkRm0xYXhOcFpkOEVnT2l4S3I2NzJJVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vc2Fzb3JkZW5lc2RlY29tcHJhLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1784933527),
('UnTsr87HZwFR5lZ8h1G7FqiXQhpCUunP0IcyPKgH', NULL, '64.69.216.78', 'Insites-scanner/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0gySWJoelAxUktwV3V3ekloNkRESjNzVHB4VmlYYlVQaDNwN3ZWSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784928542),
('ZaN7vsPvwBh1Uc4ClxPVNpuY7SDS2M0tsbonL1Bm', NULL, '192.71.142.134', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ29JZVZXQ1pHOXVKeko4b1dOV3piMkVRSGdMcFRzb2FhVlRaVTdLOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784943150),
('ZCO0dlQXRFoHIxiWQCzj8viqNLTO9kcx8EqtDo0Z', 2, '187.168.64.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibnZUWGFRUjExTnl2U25LZ1FWSVl4d0lLOEJ0bUZrdVBHSzZtNWtmZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHBzOi8vc2Fzb3JkZW5lc2RlY29tcHJhLmNvbS9zZXJ2aWNpb3MvbWVzZXMiO3M6NToicm91dGUiO3M6MTU6InNlcnZpY2VzLm1vbnRocyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1784929128),
('ztpyU4LBWjnNiERcPIbM2bjlqUtDMp81o4CESKoR', NULL, '40.77.167.70', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNFhrZzBZaTNPZEVrdXJoNWl3OHdQeVlSdzY0NG5yWDJITkR0MWtNSyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1OToiaHR0cHM6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2NvbXByYWRvci9vcmRlbmVzP3BhbmVsPXBhaWQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNjoiaHR0cHM6Ly9zYXNvcmRlbmVzZGVjb21wcmEuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784940357);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plain_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buyer',
  `companies` json DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `plain_password`, `role`, `companies`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrador', 'gherzig@sasordenesdecompra.com', NULL, '$2y$12$/KbgTKLbzFayI90gSl38BuXGRhm7wmgo8yzyyQFrueZsRAN9nQe3q', 'ghfarma2026', 'superadmin', '[]', 1, NULL, '2026-07-02 21:53:12', '2026-07-02 21:53:12'),
(2, 'Katya Nolacea Guarneros', 'admon.empresas2107@gmail.com', NULL, '$2y$12$DnS2XfH/0GP8EJHiw.D1X.p5x.usgdmG.MzyOs4nl1NvoDmiBXBnS', NULL, 'administrative_assistant', '[]', 1, NULL, '2026-07-02 23:55:24', '2026-07-02 23:55:24'),
(3, 'Araceli Aguirre', 'aaguirre_sorem@yahoo.com.mx', NULL, '$2y$12$wdllfdw/OwLJJ3N1PlO69e.R/n/jUdUgCklHqRzPv4W8RDyxYcyXi', NULL, 'finance', '[]', 1, NULL, '2026-07-03 03:21:52', '2026-07-03 03:21:52'),
(4, 'Maria Magdalena Anaya Revilla', 'mmanayar@farmasoma.com.mx', NULL, '$2y$12$KSbJQXk.6yq9XYAVjHClH.ENSuZYKrFXjr8qjYVG4.piFMkW8ffJ6', NULL, 'buyer', '[{\"name\": \"Vidicron S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Prodifem S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Farmasoma S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Sandra Paola Camacho Fonseca\", \"warehouses\": []}]', 1, NULL, '2026-07-03 04:54:35', '2026-07-25 04:46:59'),
(5, 'Gabriela Cortes', 'gcortesm@prodifem.com.mx', NULL, '$2y$12$WZZpPQrultTo/ddzIY3k9eaUp3Hc58DRduq3FDBeUJmfTWz4/uU5a', NULL, 'buyer', '[{\"name\": \"Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Prodifem S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Farmasoma S.A. de C.V.\", \"warehouses\": []}]', 1, NULL, '2026-07-03 05:24:29', '2026-07-25 04:43:49'),
(6, 'Kathya Laura Nolacea Guarneros', 'compras.admon2107@gmail.com', NULL, '$2y$12$cx7uHOiZkhn8GJ74mIRRAuI.ePJ1JCkiFYBTr8rqkigucKuhPz2vy', NULL, 'buyer', '[{\"name\": \"Brimak S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Findelz S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Gustavo Diaz Martinez\", \"warehouses\": []}, {\"name\": \"Vidicron S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Grilsa S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Prodifem S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Farmasoma S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Sandra Paola Camacho Fonseca\", \"warehouses\": []}]', 1, NULL, '2026-07-07 02:02:29', '2026-07-25 04:46:59'),
(7, 'Maria del Carmen Hernández', 'recepcion.0918@gmail.com', NULL, '$2y$12$atTpc7AcwGkUfwbQmWua4uEEGwcz5AHfBBrgaK4beWqAL2mXx7OKW', 'recepcion0918', 'inventory', '[{\"name\": \"Centro Biotecnologico de Terapias Avanzadas S.A. de C.V.\", \"warehouses\": []}, {\"name\": \"Farmasoma S.A. de C.V.\", \"warehouses\": [\"Concepcion\"]}, {\"name\": \"Prodifem S.A. de C.V.\", \"warehouses\": []}]', 1, NULL, '2026-07-22 07:25:13', '2026-07-25 01:50:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_rfc_unique` (`rfc`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `providers_buyer_id_rfc_unique` (`buyer_id`,`rfc`),
  ADD KEY `providers_provider_business_line_id_foreign` (`provider_business_line_id`);

--
-- Indexes for table `provider_business_lines`
--
ALTER TABLE `provider_business_lines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_business_lines_name_unique` (`name`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_folio_unique` (`folio`),
  ADD KEY `purchase_orders_buyer_id_foreign` (`buyer_id`),
  ADD KEY `purchase_orders_company_id_foreign` (`company_id`),
  ADD KEY `purchase_orders_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`);

--
-- Indexes for table `purchase_order_payments`
--
ALTER TABLE `purchase_order_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_order_payments_purchase_order_id_unique` (`purchase_order_id`),
  ADD KEY `purchase_order_payments_paid_by_foreign` (`paid_by`);

--
-- Indexes for table `purchase_order_receipts`
--
ALTER TABLE `purchase_order_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_receipts_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_receipts_received_by_foreign` (`received_by`);

--
-- Indexes for table `purchase_order_receipt_items`
--
ALTER TABLE `purchase_order_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_receipt_items_purchase_order_receipt_id_foreign` (`purchase_order_receipt_id`),
  ADD KEY `purchase_order_receipt_items_purchase_order_item_id_foreign` (`purchase_order_item_id`);

--
-- Indexes for table `recurring_services`
--
ALTER TABLE `recurring_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recurring_services_folio_unique` (`folio`),
  ADD KEY `recurring_services_created_by_foreign` (`created_by`);

--
-- Indexes for table `recurring_service_receipts`
--
ALTER TABLE `recurring_service_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recurring_service_receipts_recurring_service_id_due_date_unique` (`recurring_service_id`,`due_date`),
  ADD KEY `recurring_service_receipts_paid_by_foreign` (`paid_by`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=499;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `provider_business_lines`
--
ALTER TABLE `provider_business_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `purchase_order_payments`
--
ALTER TABLE `purchase_order_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `purchase_order_receipts`
--
ALTER TABLE `purchase_order_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_order_receipt_items`
--
ALTER TABLE `purchase_order_receipt_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recurring_services`
--
ALTER TABLE `recurring_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `recurring_service_receipts`
--
ALTER TABLE `recurring_service_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `providers_provider_business_line_id_foreign` FOREIGN KEY (`provider_business_line_id`) REFERENCES `provider_business_lines` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `purchase_orders_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_payments`
--
ALTER TABLE `purchase_order_payments`
  ADD CONSTRAINT `purchase_order_payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_order_payments_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_receipts`
--
ALTER TABLE `purchase_order_receipts`
  ADD CONSTRAINT `purchase_order_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_order_receipt_items`
--
ALTER TABLE `purchase_order_receipt_items`
  ADD CONSTRAINT `purchase_order_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_receipt_items_purchase_order_receipt_id_foreign` FOREIGN KEY (`purchase_order_receipt_id`) REFERENCES `purchase_order_receipts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recurring_services`
--
ALTER TABLE `recurring_services`
  ADD CONSTRAINT `recurring_services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `recurring_service_receipts`
--
ALTER TABLE `recurring_service_receipts`
  ADD CONSTRAINT `recurring_service_receipts_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recurring_service_receipts_recurring_service_id_foreign` FOREIGN KEY (`recurring_service_id`) REFERENCES `recurring_services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
