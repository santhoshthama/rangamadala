-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 07:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rangamandala_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `artist_notifications`
--

CREATE TABLE `artist_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The artist receiving the notification',
  `drama_id` int(11) DEFAULT NULL COMMENT 'Related drama',
  `type` varchar(50) NOT NULL COMMENT 'Notification type',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artist_notifications`
--

INSERT INTO `artist_notifications` (`id`, `user_id`, `drama_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(4, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 05:56:43'),
(6, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 05:57:14'),
(12, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 06:23:53'),
(13, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 06:37:43'),
(15, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Lighting Design\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 07:21:48'),
(17, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Lighting Design\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 07:22:51'),
(18, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Lighting Design\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 07:23:21'),
(20, 23, 18, 'pm_provider_confirmed_manual_payment', 'Provider Confirmed Bank Transfer', 'Ruwan Perera confirmed your bank transfer payment for \"Lighting Design\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 07:24:11'),
(23, 23, 18, 'pm_provider_rejected_terms', 'Provider Rejected Confirmed Terms', 'Ruwan Perera rejected confirmed terms for \"Theater Production\". Reason: not avaiable', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-16 07:37:58'),
(25, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:02:36'),
(27, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:03:14'),
(28, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:03:25'),
(31, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:17:28'),
(34, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:18:16'),
(35, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:18:19'),
(37, 23, 18, 'pm_provider_rejected_request', 'Provider Rejected Service Request', 'Ruwan Perera rejected the service request for \"Theater Production\". Reason: booking conflict', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:24:15'),
(39, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:25:09'),
(42, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:25:52'),
(43, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 00:26:06'),
(44, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 03:10:05'),
(45, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 09:18:10'),
(46, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 09:36:33'),
(47, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 09:59:38'),
(48, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 10:00:27'),
(49, 44, 18, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 10:00:47'),
(50, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 10:01:34'),
(51, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 10:02:00'),
(52, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Lighting Design\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 10:11:38'),
(53, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Lighting Design\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 19:01:28'),
(54, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 19:10:03'),
(55, 44, 18, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 19:10:55'),
(56, 44, 18, 'payment_submitted_by_pm', 'Bank Transfer Submitted', 'Production manager uploaded bank transfer proof for your service request. Please verify the payment details.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 19:11:21'),
(57, 23, 18, 'pm_provider_confirmed_manual_payment', 'Provider Confirmed Bank Transfer', 'Ruwan Perera confirmed your bank transfer payment for \"Theater Production\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 19:11:43'),
(58, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 20:11:37'),
(59, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 20:11:54'),
(60, 44, 18, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 20:12:03'),
(61, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 20:12:13'),
(62, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 20:12:33'),
(63, 44, 18, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 20:12:39'),
(64, 44, 18, 'payment_submitted_by_pm', 'Bank Transfer Submitted', 'Production manager uploaded bank transfer proof for your service request. Please verify the payment details.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-19 20:12:57'),
(65, 23, 18, 'pm_provider_confirmed_manual_payment', 'Provider Confirmed Bank Transfer', 'Ruwan Perera confirmed your bank transfer payment for \"Theater Production\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-19 20:13:10'),
(66, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 01:50:26'),
(67, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 01:51:04'),
(68, 44, 18, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 01:52:26'),
(69, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 01:55:02'),
(70, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 01:55:50'),
(71, 44, 18, 'payment_submitted_by_pm', 'Bank Transfer Submitted', 'Production manager uploaded bank transfer proof for your service request. Please verify the payment details.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 01:56:32'),
(72, 23, 18, 'pm_provider_confirmed_manual_payment', 'Provider Confirmed Bank Transfer', 'Ruwan Perera confirmed your bank transfer payment for \"Theater Production\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 01:57:06'),
(73, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 02:07:26'),
(74, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 02:07:55'),
(75, 23, 18, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 02:12:40'),
(76, 23, 18, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 02:13:06'),
(77, 44, 17, 'service_request_created_pm', 'Service Request from Mahendra', 'Mahendra sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:05:44'),
(78, 44, 17, 'service_request_created_pm', 'Service Request from Mahendra', 'Mahendra sent a new service request for \"Lighting Design\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:10:07'),
(79, 44, 17, 'service_request_created_pm', 'Service Request from Mahendra', 'Mahendra sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:11:07'),
(80, 49, 17, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=17', 0, '2026-04-20 04:11:22'),
(81, 49, 17, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Lighting Design\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=17', 0, '2026-04-20 04:11:50'),
(82, 44, 18, 'service_request_created_pm', 'Service Request from abc', 'abc sent a new service request for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:13:19'),
(83, 23, 20, 'role_assigned', 'Role Invitation: api', 'You have been invited for the role \"api\" in \"Ape Kathawa\". Please check your dashboard to respond.', 'http://localhost/Rangamadala/public/artistdashboard', 0, '2026-04-20 04:33:56'),
(84, 24, 20, 'role_assigned', 'Role Invitation: api', 'You have been invited for the role \"api\" in \"Ape Kathawa\". Please check your dashboard to respond.', 'http://localhost/Rangamadala/public/artistdashboard', 0, '2026-04-20 04:33:58'),
(85, 50, 20, 'role_assigned', 'Role Invitation: api', 'You have been invited for the role \"api\" in \"Ape Kathawa\". Please check your dashboard to respond.', 'http://localhost/Rangamadala/public/artistdashboard', 0, '2026-04-20 04:34:01'),
(86, 49, 20, 'role_assigned', 'Role Invitation: api', 'You have been invited for the role \"api\" in \"Ape Kathawa\". Please check your dashboard to respond.', 'http://localhost/Rangamadala/public/artistdashboard', 0, '2026-04-20 04:34:02'),
(87, 49, 20, 'role_assigned', 'Role Request Updated: api', 'The director updated your request for \"api\" in \"Ape Kathawa\".', 'http://localhost/Rangamadala/public/artistdashboard', 0, '2026-04-20 04:34:27'),
(88, 50, 19, 'interview_scheduled', 'Interview Scheduled: Village Girl', 'An interview has been scheduled for the role \"Village Girl\" in \"Nari Baana\" on Apr 25, 2026 at 12:07 AM.', 'http://localhost/Rangamadala/public/artistdashboard/view_drama?drama_id=19', 0, '2026-04-20 04:35:42'),
(89, 23, 18, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=18', 0, '2026-04-20 04:42:29'),
(90, 44, 17, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Lighting Design\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:42:53'),
(91, 44, 17, 'provider_quote_confirmed_by_pm', 'Quotation Confirmed by PM', 'Production manager confirmed your quotation for \"Theater Production\" in \"Maname\". Please review and accept to continue.', 'http://localhost/Rangamadala/public/ServiceRequests', 1, '2026-04-20 04:43:02'),
(92, 49, 17, 'pm_provider_accepted_terms', 'Provider Accepted Confirmed Terms', 'Ruwan Perera accepted your confirmed terms for \"Theater Production\". Service is now in progress.', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=17', 0, '2026-04-20 04:43:32'),
(93, 49, 17, 'pm_provider_marked_completed', 'Provider Marked Service Completed', 'Ruwan Perera marked \"Theater Production\" as completed for \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=17', 0, '2026-04-20 04:43:53'),
(94, 49, 17, 'pm_provider_responded_quote', 'Provider Responded with Quotation', 'Ruwan Perera sent a quotation response for \"Theater Production\" in \"Maname\".', 'http://localhost/Rangamadala/public/production_manager/manage_services?drama_id=17', 0, '2026-04-20 04:57:40');

-- --------------------------------------------------------

--
-- Table structure for table `artist_portfolios`
--

CREATE TABLE `artist_portfolios` (
  `id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `past_dramas` text NOT NULL,
  `position_worked` varchar(150) NOT NULL,
  `years_in_industry` int(11) NOT NULL DEFAULT 0,
  `specialized_fields` text NOT NULL,
  `education_qualifications` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audience_show_bookings`
--

CREATE TABLE `audience_show_bookings` (
  `id` int(11) NOT NULL,
  `audience_id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `booking_status` enum('pending','accepted','rejected','confirmed','completed','watched','attended') NOT NULL DEFAULT 'pending',
  `request_details_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_details_json`)),
  `rejection_reason` text DEFAULT NULL,
  `payhere_order_id` varchar(120) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audience_show_bookings`
--

INSERT INTO `audience_show_bookings` (`id`, `audience_id`, `drama_id`, `ticket_price`, `booking_status`, `request_details_json`, `rejection_reason`, `payhere_order_id`, `paid_at`, `created_at`) VALUES
(5, 2, 7, 1200.00, 'accepted', '{\"request_venue\":\"Colombo University New theatre\",\"show_date\":\"2026-04-25\",\"show_time\":\"9.00 a.m to 11.00 p.m\",\"show_datetime\":\"2026-04-25 9.00 a.m to 11.00 p.m\",\"present_count\":300,\"request_notes\":\"\"}', NULL, 'SHOW-5-1775915207', NULL, '2026-04-11 19:00:16'),
(6, 2, 9, 10000.00, 'confirmed', '{\"request_venue\":\"Colombo University New theatre\",\"show_date\":\"2026-04-30\",\"show_time\":\"9.00 a.m to 11.00 p.m\",\"show_datetime\":\"2026-04-30 9.00 a.m to 11.00 p.m\",\"present_count\":30,\"request_notes\":\"\"}', NULL, 'SHOW-6-1775957601', '2026-04-12 07:06:23', '2026-04-12 07:01:54'),
(7, 2, 10, 2000.00, 'confirmed', '{\"request_venue\":\"Colombo University New theatre\",\"show_date\":\"2026-04-25\",\"show_time\":\"9.00 a.m to 11.00 p.m\",\"show_datetime\":\"2026-04-25 9.00 a.m to 11.00 p.m\",\"present_count\":30,\"request_notes\":\"\"}', NULL, 'SHOW-7-1776056185', '2026-04-13 10:27:30', '2026-04-12 14:24:42'),
(8, 2, 11, 10000.00, 'confirmed', '{\"request_venue\":\"SCS\",\"show_date\":\"2026-04-25\",\"show_time\":\"9.00 a.m to 11.00 p.m\",\"show_datetime\":\"2026-04-25 9.00 a.m to 11.00 p.m\",\"present_count\":30,\"request_notes\":\"\"}', NULL, 'SHOW-8-1776010700', '2026-04-12 21:53:21', '2026-04-12 14:50:06'),
(9, 2, 13, 15000.00, 'confirmed', '{\"request_sender_name\":\"Santhosh Wickramarathna\",\"request_contact_phone\":\"0713425300\",\"request_contact_email\":\"santhosh@gmail.com\",\"request_venue\":\"Colombo University New theatre\",\"show_date\":\"2026-04-26\",\"show_time\":\"9:30 AM to 1:30 PM\",\"show_time_start\":\"09:30\",\"show_time_end\":\"13:30\",\"show_datetime\":\"2026-04-26 9:30 AM to 1:30 PM\",\"present_count\":300,\"request_notes\":\"\"}', NULL, 'SHOW-9-1776056466', '2026-04-13 10:31:30', '2026-04-13 00:38:54'),
(10, 17, 14, 20000.00, 'accepted', '{\"request_sender_name\":\"Dodangodage Don Santhosh Thamal Wickramarathna\",\"request_contact_phone\":\"0783434025\",\"request_contact_email\":\"bro@gmail.com\",\"request_venue\":\"Rest cross\",\"show_date\":\"2026-04-25\",\"show_time\":\"12:30 PM to 1:30 PM\",\"show_time_start\":\"12:30\",\"show_time_end\":\"13:30\",\"show_datetime\":\"2026-04-25 12:30 PM to 1:30 PM\",\"present_count\":30,\"request_notes\":\"\"}', NULL, NULL, NULL, '2026-04-13 22:45:50'),
(11, 2, 13, 15000.00, 'accepted', '{\"request_sender_name\":\"Santhosh Wickramarathna\",\"request_contact_phone\":\"0713425300\",\"request_contact_email\":\"thamalsana@gmail.com\",\"request_venue\":\"New City theatre\",\"show_date\":\"2026-04-26\",\"show_time\":\"5:00 AM to 8:00 AM\",\"show_time_start\":\"05:00\",\"show_time_end\":\"08:00\",\"show_datetime\":\"2026-04-26 5:00 AM to 8:00 AM\",\"present_count\":199,\"request_notes\":\"\"}', NULL, 'SHOW-11-1776660524', NULL, '2026-04-14 23:01:05');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Classical Drama', '2026-04-09 16:51:14'),
(2, 'Musical Drama', '2026-04-09 16:51:14'),
(3, 'Comedy Drama', '2026-04-09 16:51:14'),
(4, 'Traditional Dance', '2026-04-09 16:51:14'),
(5, 'Modern Theatre', '2026-04-09 16:51:14'),
(6, 'Street Drama', '2026-04-09 16:51:14'),
(7, 'Folk Theatre', '2026-04-09 16:51:14'),
(8, 'Experimental Theatre', '2026-04-09 16:51:14');

-- --------------------------------------------------------

--
-- Table structure for table `class_enrollments`
--

CREATE TABLE `class_enrollments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('enrolled','cancelled','completed') NOT NULL DEFAULT 'enrolled',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_enrollment_payments`
--

CREATE TABLE `class_enrollment_payments` (
  `id` int(11) NOT NULL,
  `order_id` varchar(120) NOT NULL,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('initiated','completed','failed') NOT NULL DEFAULT 'initiated',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dramas`
--

CREATE TABLE `dramas` (
  `id` int(11) NOT NULL,
  `drama_name` varchar(255) NOT NULL COMMENT 'Drama name as in public performance board certificate',
  `certificate_number` varchar(100) NOT NULL COMMENT 'Public performance certificate number',
  `owner_name` varchar(255) NOT NULL COMMENT 'Owner name',
  `description` text DEFAULT NULL COMMENT 'Artist provided synopsis for the drama',
  `category_id` int(11) DEFAULT NULL,
  `certificate_image` varchar(255) DEFAULT NULL COMMENT 'Image of public performance board certificate',
  `public_description` text DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `ticket_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `showing_prices` varchar(500) DEFAULT NULL,
  `poster_image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dramas`
--

INSERT INTO `dramas` (`id`, `drama_name`, `certificate_number`, `owner_name`, `description`, `category_id`, `certificate_image`, `public_description`, `genre`, `language`, `duration_minutes`, `venue`, `event_date`, `event_time`, `ticket_price`, `showing_prices`, `poster_image`, `is_published`, `published_at`, `published_by`, `created_by`, `creator_artist_id`, `created_at`, `updated_at`) VALUES
(9, 'Hunuwataye kathawa', 'PPB/120/234', 'santhosh wickramarathna', 'Critical situation in Sri lanka', 3, 'certificate_1775957274_69daf51ae6dbe.jpg', 'Critical situation in Sri lanka', '', 'sinhala', 120, '', NULL, NULL, 0.00, 'Rs. 10000.00', 'poster_69e5a48690446_hunuwataye_kathawa.jpg', 1, '2026-04-12 07:00:45', 6, NULL, NULL, '2026-04-12 01:28:28', '2026-04-20 03:59:02'),
(10, 'Kelani Palama', 'PPB/120/238', 'Lakiya', 'Ruhini', 7, 'certificate_1775983856_69db5cf003be9.jpg', 'Ruhini', '', 'sinhala', 120, '', NULL, NULL, 0.00, 'Rs . 2000.00', 'poster_69e5a5027ae0f_Kelani-Palama.jpg', 1, '2026-04-12 14:23:33', 6, NULL, NULL, '2026-04-12 08:51:31', '2026-04-20 04:01:06'),
(11, 'ko kukko', 'PPB/120/237', 'Santhosh Parakrama', 'Boy', 3, 'certificate_1775984971_69db614b93e7f.jpg', 'Boy', '', 'sinhala', 120, '', NULL, NULL, 0.00, '10000.00', 'poster_69e5a4b7edb30_Ko_Kukko_1.jpg', 1, '2026-04-12 14:47:50', 12, NULL, NULL, '2026-04-12 09:10:57', '2026-04-20 03:59:51'),
(12, 'Maname', 'PPB/120/239', 'santhosh wickramarathna', 'Night time problem', 2, 'certificate_1776010090_69dbc36aedacd.png', 'Night time problem', '', 'sinhala', 120, '', NULL, NULL, 0.00, 'Rs . 150000.00', 'poster_69e5a4386ac3d_maname.jpg', 1, '2026-04-12 22:00:50', 6, NULL, NULL, '2026-04-12 16:09:01', '2026-04-20 03:57:44'),
(13, 'Mathalan', 'PPB/120/240', 'Samaravera Ediriverkrama', 'About talent', 7, 'certificate_1776011661_69dbc98d3e416.jpg', 'About talent', '', 'Sinhala', 120, '', NULL, NULL, 0.00, 'Rs . 15000.00', 'poster_69e5a4123341e_hq720.jpg', 1, '2026-04-12 22:06:02', 6, NULL, NULL, '2026-04-12 16:34:51', '2026-04-20 03:57:06'),
(17, 'Maname', 'PPB/2026/0987', 'Ruhuni', 'test', NULL, 'certificate_1776231849_69df25a990949.jpg', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 0, NULL, NULL, 23, 23, '2026-04-15 05:44:58', '2026-04-15 05:44:58'),
(18, 'Maname', 'PPB/2026/0123', 'Ruhuni', 'd1', NULL, 'certificate_1776266215_69dfabe76dc02.png', 'd1', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 0, NULL, NULL, 24, 24, '2026-04-15 15:17:11', '2026-04-15 15:17:11'),
(19, 'Nari Baana', 'PPB/2026/0111', 'Saman', 'Love  story', NULL, 'certificate_1776656981_69e5a25539936.jpeg', 'Love  story', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 0, NULL, NULL, 50, 50, '2026-04-20 03:50:27', '2026-04-20 03:50:27'),
(20, 'Ape Kathawa', 'PPB/2026/165', 'Ruhuni', 'local drama', NULL, 'certificate_1776658683_69e5a8fb830d5.jpeg', 'local drama', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 0, NULL, NULL, 50, 50, '2026-04-20 04:18:16', '2026-04-20 04:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `drama_budgets`
--

CREATE TABLE `drama_budgets` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `allocated_amount` decimal(10,2) NOT NULL,
  `spent_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','approved','completed','cancelled') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_budgets`
--

INSERT INTO `drama_budgets` (`id`, `drama_id`, `item_name`, `category`, `allocated_amount`, `spent_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 18, 'Theater Production #11', 'Theater Production', 1000.00, 1000.00, 'approved', 23, '2026-04-19 19:11:43', '2026-04-19 19:11:43'),
(2, 18, 'Theater Production #16', 'Theater Production', 100.00, 100.00, 'approved', 23, '2026-04-19 20:13:10', '2026-04-19 20:13:10'),
(3, 18, 'Theater Production #16', 'Theater Production', 5614.00, 5614.00, 'completed', 23, '2026-04-20 01:57:06', '2026-04-20 01:57:06'),
(4, 17, 'ligtning', 'Lighting Design', 1000.00, 0.00, 'pending', 49, '2026-04-20 04:39:35', '2026-04-20 04:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `drama_classes`
--

CREATE TABLE `drama_classes` (
  `id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `class_level` enum('beginner','intermediate','advanced','all_levels') NOT NULL DEFAULT 'all_levels',
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `class_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 120,
  `venue` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drama_classes`
--

INSERT INTO `drama_classes` (`id`, `created_by`, `title`, `description`, `class_level`, `fee`, `capacity`, `class_date`, `start_time`, `duration_minutes`, `venue`, `is_published`, `created_at`, `updated_at`) VALUES
(4, 50, 'Voice Training', 'Singing', 'beginner', 1000.00, 30, '2026-04-23', '10:00:00', 120, 'New arts theater, UOC', 1, '2026-04-20 04:21:44', '2026-04-20 04:21:44');

-- --------------------------------------------------------

--
-- Table structure for table `drama_creation_requests`
--

CREATE TABLE `drama_creation_requests` (
  `id` int(11) NOT NULL,
  `drama_name` varchar(255) NOT NULL,
  `certificate_number` varchar(100) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `certificate_image` varchar(255) DEFAULT NULL,
  `requested_by` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_drama_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_creation_requests`
--

INSERT INTO `drama_creation_requests` (`id`, `drama_name`, `certificate_number`, `owner_name`, `description`, `certificate_image`, `requested_by`, `status`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_drama_id`, `created_at`, `updated_at`) VALUES
(11, 'Maname', 'PPB/2026/0987', 'Ruhuni', 'test', 'certificate_1776231849_69df25a990949.jpg', 23, 'approved', 1, '2026-04-15 11:14:58', NULL, 17, '2026-04-15 05:44:09', '2026-04-15 05:44:58'),
(12, 'T1', 'PPB/2026/01234', 'RN', 't2', 'certificate_1776231962_69df261ab2420.png', 23, 'pending', NULL, NULL, NULL, NULL, '2026-04-15 05:46:02', '2026-04-15 05:46:02'),
(13, 'Maname', 'PPB/2026/0123', 'Ruhuni', 'd1', 'certificate_1776266215_69dfabe76dc02.png', 24, 'approved', 1, '2026-04-15 20:47:11', NULL, 18, '2026-04-15 15:16:55', '2026-04-15 15:17:11'),
(14, 'Nari Baana', 'PPB/2026/0111', 'Saman', 'Love  story', 'certificate_1776656981_69e5a25539936.jpeg', 50, 'approved', 1, '2026-04-20 09:20:27', NULL, 19, '2026-04-20 03:49:41', '2026-04-20 03:50:27'),
(15, 'Ape Kathawa', 'PPB/2026/165', 'Ruhuni', 'local drama', 'certificate_1776658683_69e5a8fb830d5.jpeg', 50, 'approved', 1, '2026-04-20 09:48:16', NULL, 20, '2026-04-20 04:18:03', '2026-04-20 04:18:16'),
(16, 'Maname', 'PPB/2026/0178', 'Saman', 'Good', 'certificate_1776659537_69e5ac517b555.jpg', 50, 'pending', NULL, NULL, NULL, NULL, '2026-04-20 04:32:17', '2026-04-20 04:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `drama_manager_assignments`
--

CREATE TABLE `drama_manager_assignments` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `manager_artist_id` int(11) NOT NULL COMMENT 'Artist assigned as Production Manager',
  `assigned_by` int(11) NOT NULL COMMENT 'Director who assigned the PM',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'When the PM was assigned',
  `status` enum('active','removed') NOT NULL DEFAULT 'active' COMMENT 'Assignment status',
  `removed_at` datetime DEFAULT NULL COMMENT 'When the PM was removed (if applicable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_manager_assignments`
--

INSERT INTO `drama_manager_assignments` (`id`, `drama_id`, `manager_artist_id`, `assigned_by`, `assigned_at`, `status`, `removed_at`) VALUES
(1, 18, 23, 24, '2026-04-15 20:57:24', 'active', NULL),
(2, 17, 49, 23, '2026-04-20 09:34:56', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drama_manager_requests`
--

CREATE TABLE `drama_manager_requests` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `artist_id` int(11) NOT NULL COMMENT 'Artist invited to be PM',
  `director_id` int(11) NOT NULL COMMENT 'Director who sent the request',
  `status` enum('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Request status',
  `message` text DEFAULT NULL COMMENT 'Optional message from director',
  `requested_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'When request was sent',
  `responded_at` datetime DEFAULT NULL COMMENT 'When artist responded',
  `response_note` text DEFAULT NULL COMMENT 'Optional note from artist when responding'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_manager_requests`
--

INSERT INTO `drama_manager_requests` (`id`, `drama_id`, `artist_id`, `director_id`, `status`, `message`, `requested_at`, `responded_at`, `response_note`) VALUES
(2, 17, 24, 23, 'cancelled', NULL, '2026-04-15 11:45:08', NULL, NULL),
(3, 18, 23, 24, 'accepted', NULL, '2026-04-15 20:49:55', '2026-04-15 20:57:24', NULL),
(4, 17, 49, 23, 'accepted', NULL, '2026-04-20 09:31:47', '2026-04-20 09:34:56', NULL),
(5, 19, 23, 50, 'pending', NULL, '2026-04-20 10:06:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drama_ratings`
--

CREATE TABLE `drama_ratings` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `is_helpful` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drama_roles`
--

CREATE TABLE `drama_roles` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `role_name` varchar(100) NOT NULL COMMENT 'Role/Character name',
  `role_description` text DEFAULT NULL COMMENT 'Role description and requirements',
  `role_type` enum('lead','supporting','ensemble','dancer','musician','other') DEFAULT 'supporting' COMMENT 'Type of role',
  `salary` decimal(10,2) DEFAULT NULL COMMENT 'Salary offered for this role',
  `positions_available` int(11) NOT NULL DEFAULT 1,
  `positions_filled` int(11) NOT NULL DEFAULT 0,
  `status` enum('open','closed','filled') NOT NULL DEFAULT 'open',
  `requirements` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `published_message` text DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_roles`
--

INSERT INTO `drama_roles` (`id`, `drama_id`, `role_name`, `role_description`, `role_type`, `salary`, `positions_available`, `positions_filled`, `status`, `requirements`, `is_published`, `published_at`, `published_message`, `published_by`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 13, 'Mathalan main', 'Male chareter', 'lead', 20000.00, 1, 0, 'open', NULL, 1, '2026-04-15 09:49:46', 'Male chracter need', NULL, NULL, '2026-04-13 09:18:07', '2026-04-15 04:19:46'),
(2, 19, 'Nariya', 'Fox', 'lead', 5000.00, 1, 0, 'open', NULL, 1, '2026-04-20 09:22:44', NULL, 50, 50, '2026-04-20 03:51:47', '2026-04-20 03:52:44'),
(3, 19, 'Village Girl', 'Innocent Girl', 'lead', 3500.00, 1, 0, 'open', NULL, 1, '2026-04-20 09:22:38', NULL, 50, 50, '2026-04-20 03:52:26', '2026-04-20 03:52:38'),
(4, 20, 'api', 'strong chracter', 'supporting', 2000.00, 1, 0, 'open', NULL, 0, NULL, NULL, NULL, 50, '2026-04-20 04:33:24', '2026-04-20 04:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `drama_schedules`
--

CREATE TABLE `drama_schedules` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL,
  `event_type` enum('rehearsal','interview','meeting','performance') NOT NULL DEFAULT 'rehearsal',
  `event_title` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `participants` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_schedules`
--

INSERT INTO `drama_schedules` (`id`, `drama_id`, `event_type`, `event_title`, `event_description`, `scheduled_date`, `start_time`, `end_time`, `venue`, `role_id`, `status`, `participants`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 19, 'rehearsal', 'reharsal12', NULL, '2026-04-23', '11:07:00', '12:07:00', 'colombo', NULL, 'confirmed', NULL, NULL, 50, '2026-04-20 04:37:30', '2026-04-20 04:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `drama_services`
--

CREATE TABLE `drama_services` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `service_type` varchar(100) NOT NULL COMMENT 'Type of service (Theater Production, Lighting Design, etc.)',
  `budget` decimal(12,2) DEFAULT NULL COMMENT 'Expected budget for this service type',
  `description` text DEFAULT NULL COMMENT 'Description or requirements for this service',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drama_services`
--

INSERT INTO `drama_services` (`id`, `drama_id`, `service_type`, `budget`, `description`, `created_at`, `updated_at`) VALUES
(1, 18, 'Theater Production', 0.00, '', '2026-04-15 15:27:49', '2026-04-15 15:27:49'),
(2, 18, 'Lighting Design', 0.00, '', '2026-04-16 05:55:53', '2026-04-16 05:55:53'),
(3, 18, 'Sound Systems', 0.00, '', '2026-04-19 12:32:26', '2026-04-19 12:32:26'),
(4, 18, 'Video Production', 0.00, '', '2026-04-19 19:33:13', '2026-04-19 19:33:13'),
(5, 17, 'Theater Production', 100000.00, '', '2026-04-20 04:05:21', '2026-04-20 04:05:21'),
(6, 17, 'Lighting Design', 50000.00, '', '2026-04-20 04:05:29', '2026-04-20 04:05:29');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `alt_text` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `image_path`, `title`, `alt_text`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'assets/images/stagePer.png', 'Stage Performance', 'Stage Performance', 1, 1, '2026-04-09 16:51:15', '2026-04-09 16:51:15'),
(8, 'assets/images/Rehersal.png', 'Rehearsal', 'Rehearsal', 2, 1, '2026-04-09 16:53:51', '2026-04-09 16:53:51'),
(9, 'assets/images/AudienceView.png', 'Audience View', 'Audience View', 3, 1, '2026-04-09 16:53:51', '2026-04-09 16:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `service_request_id` int(11) NOT NULL,
  `payment_type` enum('advance','remaining','full') NOT NULL COMMENT 'Type of payment',
  `amount` decimal(12,2) NOT NULL,
  `payment_gateway` varchar(50) DEFAULT 'payhere' COMMENT 'Gateway used',
  `payment_status` enum('pending','completed','success','failed','refunded','canceled','cancelled','chargedback','expired') DEFAULT 'pending',
  `paid_by` int(11) DEFAULT NULL COMMENT 'User who made payment',
  `paid_to` int(11) DEFAULT NULL COMMENT 'User receiving payment',
  `paid_at` timestamp NULL DEFAULT NULL,
  `gateway_payment_id` varchar(100) DEFAULT NULL COMMENT 'Gateway payment ID',
  `gateway_order_id` varchar(100) DEFAULT NULL COMMENT 'Gateway order ID',
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'Internal payment reference',
  `transaction_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Gateway response payload' CHECK (json_valid(`transaction_response`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `service_request_id`, `payment_type`, `amount`, `payment_gateway`, `payment_status`, `paid_by`, `paid_to`, `paid_at`, `gateway_payment_id`, `gateway_order_id`, `reference_number`, `transaction_response`, `created_at`, `updated_at`) VALUES
(9, 16, 'advance', 100.00, 'bank_transfer', 'completed', 23, 44, '2026-04-19 20:13:10', NULL, NULL, 'REQ-16-advance-1776629577', '{\"source\":\"bank_slip_upload\",\"uploaded_at\":\"2026-04-19 22:12:57\",\"bank_slip_path\":\"slip_16_1776629577_9c37dd4b.png\",\"bank_submitted_at\":\"2026-04-19 22:12:57\",\"provider_confirmed_at\":\"2026-04-19 22:13:10\",\"provider_confirmed_by\":44}', '2026-04-19 20:12:57', '2026-04-19 20:13:10'),
(10, 16, 'remaining', 5514.00, 'bank_transfer', 'completed', 23, 44, '2026-04-20 01:57:05', NULL, NULL, 'REQ-16-remaining-1776650192', '{\"source\":\"bank_slip_upload\",\"uploaded_at\":\"2026-04-20 03:56:32\",\"bank_slip_path\":\"slip_16_1776650192_70ef8315.png\",\"bank_submitted_at\":\"2026-04-20 03:56:32\",\"provider_confirmed_at\":\"2026-04-20 03:57:05\",\"provider_confirmed_by\":44}', '2026-04-20 01:56:32', '2026-04-20 01:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `services_provided` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `provider_id`, `year`, `project_name`, `services_provided`, `description`) VALUES
(1, 45, 2024, 'Maname', 'Theater Production, Stage Rental, Technical Support', 'Provided the theatre venue, stage setup, rehearsal coordination, backstage support, and technical assistance for a large inter-school drama production.'),
(2, 46, 2025, 'Sihinayaki Sandawa – Musical Stage Show', 'Lighting Design, Stage Lighting Setup, Live Cue Operation', 'Handled full lighting design and show-time light operation for a musical event held in Colombo. Designed scene-based lighting moods and managed spotlight cues during live performances.'),
(3, 46, 2024, 'UCSC Drama Festival', 'Lighting Design, Technical Lighting Support', 'Provided stage lighting arrangement and operation for university drama performances. Managed color washes, actor highlights, and scene transitions.'),
(4, 47, 2025, 'Ranga Abhinaya Live Drama Night', 'Sound Systems, Live Audio Mixing, Stage Monitor Setup', 'Provided full PA setup, microphone arrangement, backstage monitor system, and live sound mixing for a drama performance held in Colombo.'),
(5, 48, 2025, 'Colombo Theatre Festival Highlights', 'Video Production, Event Coverage, Editing', 'Captured and edited highlight videos of multiple theatre performances for a Colombo-based drama festival.');

-- --------------------------------------------------------

--
-- Table structure for table `provider_availability`
--

CREATE TABLE `provider_availability` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `available_date` date NOT NULL,
  `status` enum('available','booked') NOT NULL DEFAULT 'available',
  `allow_more_bookings` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `booked_for` varchar(255) DEFAULT NULL,
  `booking_details` text DEFAULT NULL,
  `service_request_id` int(11) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT current_timestamp(),
  `booked_on` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_availability`
--

INSERT INTO `provider_availability` (`id`, `provider_id`, `available_date`, `status`, `allow_more_bookings`, `description`, `booked_for`, `booking_details`, `service_request_id`, `added_on`, `booked_on`, `updated_at`) VALUES
(7, 44, '2026-04-21', 'booked', 0, 'Service request booking', 'abc', 'Booked from accepted request #17', 17, '2026-04-20 01:55:02', NULL, '2026-04-20 02:07:26'),
(8, 44, '2026-04-23', 'booked', 1, 'Service request booking', 'Mahendra', 'Booked from accepted request #20', 20, '2026-04-20 01:59:23', NULL, '2026-04-20 04:43:32'),
(10, 44, '2026-04-20', 'booked', 0, 'Service request booking', 'abc', 'Booked from accepted request #15', 15, '2026-04-20 02:12:40', NULL, '2026-04-20 02:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `role_applications`
--

CREATE TABLE `role_applications` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `application_message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `media_links` text DEFAULT NULL COMMENT 'Artist portfolio or media links',
  `profile_viewed_at` datetime DEFAULT NULL,
  `profile_viewed_by` int(11) DEFAULT NULL,
  `interview_at` datetime DEFAULT NULL,
  `interview_scheduled_at` datetime DEFAULT NULL,
  `interview_scheduled_by` int(11) DEFAULT NULL,
  `interview_status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `interview_notes` text DEFAULT NULL,
  `interview_confirmation_status` enum('pending','confirmed','declined') NOT NULL DEFAULT 'pending',
  `interview_confirmed_at` datetime DEFAULT NULL,
  `interview_confirmation_note` text DEFAULT NULL,
  `interview_confirmation_seen_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_applications`
--

INSERT INTO `role_applications` (`id`, `role_id`, `artist_id`, `application_message`, `status`, `applied_at`, `reviewed_at`, `reviewed_by`, `media_links`, `profile_viewed_at`, `profile_viewed_by`, `interview_at`, `interview_scheduled_at`, `interview_scheduled_by`, `interview_status`, `interview_notes`, `interview_confirmation_status`, `interview_confirmed_at`, `interview_confirmation_note`, `interview_confirmation_seen_at`) VALUES
(1, 2, 50, 'I am a professional artist', 'pending', '2026-04-20 04:20:25', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 'pending', NULL, NULL, NULL),
(2, 3, 50, 'Im a artist', 'pending', '2026-04-20 04:30:57', NULL, NULL, 'www.youtube.com', '2026-04-20 10:05:42', 50, '2026-04-25 00:07:00', '2026-04-20 10:05:42', 50, 'pending', NULL, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_assignments`
--

CREATE TABLE `role_assignments` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','completed','terminated') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_requests`
--

CREATE TABLE `role_requests` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `director_id` int(11) NOT NULL,
  `status` enum('pending','interview','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `interview_at` datetime DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_requests`
--

INSERT INTO `role_requests` (`id`, `role_id`, `artist_id`, `director_id`, `status`, `note`, `interview_at`, `requested_at`, `responded_at`) VALUES
(1, 4, 23, 50, 'pending', NULL, NULL, '2026-04-20 04:33:56', NULL),
(2, 4, 24, 50, 'pending', NULL, NULL, '2026-04-20 04:33:58', NULL),
(3, 4, 50, 50, 'pending', NULL, NULL, '2026-04-20 04:34:01', NULL),
(4, 4, 49, 50, 'interview', NULL, '2026-04-22 11:04:00', '2026-04-20 04:34:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `serviceprovider`
--

CREATE TABLE `serviceprovider` (
  `user_id` int(11) NOT NULL,
  `professional_title` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `social_media_link` varchar(255) DEFAULT NULL,
  `professional_summary` text DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `availability_notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `serviceprovider`
--

INSERT INTO `serviceprovider` (`user_id`, `professional_title`, `location`, `social_media_link`, `professional_summary`, `availability`, `availability_notes`, `created_at`, `updated_at`, `birthday`) VALUES
(44, 'Stage provider', 'Matara', 'https://web.facebook.com/champika.wanniarachchi.79', 'Stage providers since 2000', 1, '', '2026-04-19 03:06:09', '2026-04-19 09:53:20', NULL),
(45, 'Theatre Venue Manager & Production Coordinator', 'Colombo', 'https://www.facebook.com/nadeesha.theatre', NULL, 1, 'Available on weekdays and weekends for theatre bookings, rehearsals, and live productions.', '2026-04-19 06:55:11', '2026-04-19 06:55:11', NULL),
(46, 'Stage Lighting Designer & Event Lighting Technician', 'Maharagama', 'https://www.facebook.com/kasun.lighting.lk', NULL, 1, 'Available for theatre dramas, musical shows, school concerts, and indoor stage events across Colombo and nearby areas.', '2026-04-19 07:08:56', '2026-04-19 07:08:56', NULL),
(47, 'Live Sound Engineer & Event Audio Technician', 'Galle', 'https://www.facebook.com/dilan.sound.lk', NULL, 1, 'Available for dramas, concerts, church events, school functions, and indoor or outdoor stage shows in Colombo and surrounding areas.', '2026-04-19 07:13:48', '2026-04-19 07:13:48', NULL),
(48, 'Videographer & Creative Media Producer', 'Kandy', 'https://www.facebook.com/sachintha.visuals', NULL, 1, 'Available for weddings, stage dramas, music videos, university events, and commercial shoots across Colombo and nearby areas', '2026-04-19 07:18:39', '2026-04-19 07:18:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `provider_id`, `service_type_id`, `created_at`, `updated_at`) VALUES
(19, 44, 1, '2026-04-19 03:06:09', '2026-04-19 03:06:09'),
(21, 44, 2, '2026-04-19 03:08:13', '2026-04-19 03:08:13'),
(22, 45, 1, '2026-04-19 06:55:11', '2026-04-19 06:55:11'),
(23, 46, 2, '2026-04-19 07:08:56', '2026-04-19 07:08:56'),
(24, 47, 3, '2026-04-19 07:13:48', '2026-04-19 07:13:48'),
(25, 48, 4, '2026-04-19 07:18:39', '2026-04-19 07:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `service_costume_details`
--

CREATE TABLE `service_costume_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `types_of_costumes_provided` text DEFAULT NULL,
  `custom_costume_design_available` varchar(10) DEFAULT NULL,
  `available_sizes` varchar(100) DEFAULT NULL,
  `alterations_provided` varchar(10) DEFAULT NULL,
  `number_of_costumes_available` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_lighting_details`
--

CREATE TABLE `service_lighting_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `lighting_equipment_provided` text DEFAULT NULL,
  `max_stage_size` varchar(255) DEFAULT NULL,
  `lighting_design_service` varchar(10) DEFAULT NULL,
  `lighting_crew_available` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_lighting_details`
--

INSERT INTO `service_lighting_details` (`service_id`, `rate_per_hour`, `rate_type`, `description`, `lighting_equipment_provided`, `max_stage_size`, `lighting_design_service`, `lighting_crew_available`) VALUES
(21, 20000.00, 'hourly', '', '', '40ft*20ft', 'Yes', 'No'),
(23, 28000.00, 'daily', 'Professional lighting design and stage lighting setup services for theatre productions, concerts, award ceremonies, and school events. I provide lighting planning, fixture setup, live operation, and technical support to create the correct mood and visual impact for each performance.', 'LED PAR lights, moving head lights, spotlights, flood lights, DMX controller, dimmer packs, lighting console, trussing, clamps, power cables, extension lines, and follow spot equipment.', '40ft × 30ft', 'Yes', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `service_makeup_details`
--

CREATE TABLE `service_makeup_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `type_of_makeup_services` text DEFAULT NULL,
  `experience_stage_makeup_years` int(11) DEFAULT NULL,
  `character_based_makeup_available` varchar(10) DEFAULT NULL,
  `can_handle_full_cast` varchar(10) DEFAULT NULL,
  `maximum_actors_per_show` int(11) DEFAULT NULL,
  `bring_own_makeup_kit` varchar(10) DEFAULT NULL,
  `onsite_service_available` varchar(10) DEFAULT NULL,
  `touchup_service_during_show` varchar(10) DEFAULT NULL,
  `traditional_cultural_makeup_expertise` text DEFAULT NULL,
  `sample_makeup_photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_other_details`
--

CREATE TABLE `service_other_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `requester_name` varchar(100) NOT NULL,
  `requester_email` varchar(100) NOT NULL,
  `requester_phone` varchar(20) NOT NULL,
  `drama_name` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `service_required` varchar(255) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `service_details_json` longtext DEFAULT NULL COMMENT 'JSON object containing service-specific details',
  `notes` text DEFAULT NULL,
  `provider_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `status` enum('pending','provider_responded','confirmed','accepted','rejected','completed','completed_paid','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `drama_id`, `provider_id`, `requested_by`, `requester_name`, `requester_email`, `requester_phone`, `drama_name`, `service_type`, `service_required`, `service_date`, `start_date`, `end_date`, `budget`, `description`, `service_details_json`, `notes`, `provider_notes`, `rejection_reason`, `status`, `payment_status`, `accepted_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(15, 18, 44, 23, 'abc', 'abc@gmail.com', '0711015851', 'Maname', 'Theater Production', '', NULL, '2026-04-20', '2026-04-20', NULL, '', '{\"provider_response\":{\"quote_amount\":\"1000\",\"needs_advance\":false,\"advance_amount\":\"\",\"advance_due_date\":\"\",\"final_payment_due_date\":\"2026-04-21\",\"note\":\"\",\"responded_at\":\"2026-04-19 22:11:54\"},\"pm_confirmation\":{\"final_quote\":null,\"final_start_date\":null,\"final_end_date\":null,\"note\":\"\",\"confirmed_at\":\"2026-04-19 22:12:03\",\"confirmed_by\":23}}', '', '', NULL, 'completed', 'unpaid', '2026-04-20 02:12:40', '2026-04-20 02:13:06', '2026-04-19 16:41:37', '2026-04-20 02:13:06'),
(16, 18, 44, 23, 'abc', 'abc@gmail.com', '0711015851', 'Maname', 'Theater Production', '', NULL, '2026-04-21', '2026-04-21', NULL, '', '{\"provider_response\":{\"quote_amount\":\"5614\",\"needs_advance\":true,\"advance_amount\":\"100\",\"advance_due_date\":\"2026-04-21\",\"final_payment_due_date\":\"2026-04-21\",\"note\":\"\",\"responded_at\":\"2026-04-19 22:12:33\"},\"pm_confirmation\":{\"final_quote\":null,\"final_start_date\":null,\"final_end_date\":null,\"note\":\"\",\"confirmed_at\":\"2026-04-19 22:12:39\",\"confirmed_by\":23}}', '', '', NULL, 'completed_paid', 'paid', '2026-04-20 01:55:02', '2026-04-20 01:55:50', '2026-04-19 16:42:13', '2026-04-20 01:57:05'),
(17, 18, 44, 23, 'abc', 'abc@gmail.com', '0711015851', 'Maname', 'Theater Production', '', NULL, '2026-04-21', '2026-04-21', NULL, '', '{\"provider_response\":{\"quote_amount\":\"10000\",\"needs_advance\":false,\"advance_amount\":\"\",\"advance_due_date\":\"\",\"final_payment_due_date\":\"2026-04-24\",\"note\":\"\",\"responded_at\":\"2026-04-20 03:51:04\"},\"pm_confirmation\":{\"final_quote\":null,\"final_start_date\":null,\"final_end_date\":null,\"note\":\"\",\"confirmed_at\":\"2026-04-20 03:52:26\",\"confirmed_by\":23}}', '', '', NULL, 'completed', 'unpaid', '2026-04-20 02:07:26', '2026-04-20 02:07:55', '2026-04-19 22:20:26', '2026-04-20 02:07:55'),
(18, 17, 44, 49, 'Mahendra', 'mahendra@gmail.com', '0766967867', 'Maname', 'Theater Production', '', NULL, '2026-04-23', '2026-04-23', NULL, '', '{\"provider_response\":{\"quote_amount\":\"10000\",\"needs_advance\":true,\"advance_amount\":\"1000\",\"advance_due_date\":\"2026-04-20\",\"final_payment_due_date\":\"2026-04-21\",\"note\":\"\",\"responded_at\":\"2026-04-20 06:57:40\"}}', '', '', NULL, 'provider_responded', 'unpaid', NULL, NULL, '2026-04-20 00:35:44', '2026-04-20 04:57:40'),
(19, 17, 44, 49, 'Mahendra', 'mahendra@gmail.com', '0766967867', 'Maname', 'Lighting Design', '', NULL, '2026-04-23', '2026-04-23', NULL, '', '{\"provider_response\":{\"quote_amount\":\"20000\",\"needs_advance\":true,\"advance_amount\":\"1000\",\"advance_due_date\":\"2026-04-20\",\"final_payment_due_date\":\"2026-04-21\",\"note\":\"\",\"responded_at\":\"2026-04-20 06:11:50\"},\"pm_confirmation\":{\"final_quote\":null,\"final_start_date\":null,\"final_end_date\":null,\"note\":\"\",\"confirmed_at\":\"2026-04-20 06:42:53\",\"confirmed_by\":49}}', '', '', NULL, 'confirmed', 'unpaid', NULL, NULL, '2026-04-20 00:40:07', '2026-04-20 04:42:53'),
(20, 17, 44, 49, 'Mahendra', 'mahendra@gmail.com', '0766967867', 'Maname', 'Theater Production', '', NULL, '2026-04-23', '2026-04-23', NULL, '', '{\"provider_response\":{\"quote_amount\":\"100000\",\"needs_advance\":false,\"advance_amount\":\"\",\"advance_due_date\":\"\",\"final_payment_due_date\":\"2026-04-22\",\"note\":\"\",\"responded_at\":\"2026-04-20 06:11:22\"},\"pm_confirmation\":{\"final_quote\":null,\"final_start_date\":null,\"final_end_date\":null,\"note\":\"\",\"confirmed_at\":\"2026-04-20 06:43:02\",\"confirmed_by\":49}}', '', '', NULL, 'completed', 'unpaid', '2026-04-20 04:43:32', '2026-04-20 04:43:53', '2026-04-20 00:41:07', '2026-04-20 04:43:53'),
(21, 18, 44, 23, 'abc', 'abc@gmail.com', '0711015851', 'Maname', 'Theater Production', '', NULL, '2026-04-22', '2026-04-22', NULL, '', '{\"provider_response\":{\"quote_amount\":\"10000\",\"needs_advance\":false,\"advance_amount\":\"\",\"advance_due_date\":\"\",\"final_payment_due_date\":\"2026-04-25\",\"note\":\"\",\"responded_at\":\"2026-04-20 06:42:29\"}}', '', '', NULL, 'provider_responded', 'unpaid', NULL, NULL, '2026-04-20 00:43:18', '2026-04-20 04:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `service_set_details`
--

CREATE TABLE `service_set_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `types_of_sets_designed` text DEFAULT NULL,
  `set_construction_provided` varchar(10) DEFAULT NULL,
  `stage_installation_support` varchar(10) DEFAULT NULL,
  `max_stage_size_supported` varchar(255) DEFAULT NULL,
  `materials_used` text DEFAULT NULL,
  `sample_set_designs` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_sound_details`
--

CREATE TABLE `service_sound_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `sound_equipment_provided` text DEFAULT NULL,
  `max_audience_size` int(11) DEFAULT NULL,
  `sound_effects_handling` varchar(10) DEFAULT NULL,
  `sound_engineer_included` varchar(10) DEFAULT NULL,
  `equipment_brands` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_sound_details`
--

INSERT INTO `service_sound_details` (`service_id`, `rate_per_hour`, `rate_type`, `description`, `sound_equipment_provided`, `max_audience_size`, `sound_effects_handling`, `sound_engineer_included`, `equipment_brands`) VALUES
(24, 32000.00, 'daily', 'Professional sound system rental and live audio support for theatre productions, musical programs, seminars, and stage performances. I provide complete audio setup, testing, live mixing, and technical support to ensure clear sound coverage throughout the event.', 'PA speakers, subwoofers, stage monitors, digital mixer, analog mixer, wireless microphones, wired microphones, DI boxes, amplifiers, equalizers, XLR cables, speaker stands, and playback system.', 800, 'Yes', 'Yes', 'Yamaha, Shure, JBL, Behringer, Soundcraft');

-- --------------------------------------------------------

--
-- Table structure for table `service_theater_details`
--

CREATE TABLE `service_theater_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `theatre_name` varchar(255) DEFAULT NULL,
  `seating_capacity` int(11) DEFAULT NULL,
  `stage_dimensions` varchar(255) DEFAULT NULL,
  `stage_type` varchar(100) DEFAULT NULL,
  `available_facilities` text DEFAULT NULL,
  `technical_facilities` text DEFAULT NULL,
  `equipment_rent` text DEFAULT NULL,
  `stage_crew_available` varchar(10) DEFAULT NULL,
  `location_address` text DEFAULT NULL,
  `theatre_photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_theater_details`
--

INSERT INTO `service_theater_details` (`service_id`, `rate_per_hour`, `rate_type`, `description`, `theatre_name`, `seating_capacity`, `stage_dimensions`, `stage_type`, `available_facilities`, `technical_facilities`, `equipment_rent`, `stage_crew_available`, `location_address`, `theatre_photos`) VALUES
(19, 15000.00, 'hourly', '', 'Lotus Theter', 450, '30ft * 40ft', 'Open Stage', 'Dressing rooms, Parking', 'Lighting system', 'Generator', 'Yes', 'Asoka,5th cross Rd,Matara', NULL),
(22, 35000.00, 'daily', 'Fully equipped theatre venue and production support service for stage dramas, musical shows, school events, and cultural performances. We provide a well-maintained performance space with essential facilities for rehearsals and live shows.', 'Lotus Stage Theatre', 450, '32ft × 24ft', 'Indoor', 'Dressing rooms, AC, Parking, Washrooms', 'Lighting system, Sound system, Projector', 'Wireless microphones, extra stage monitors, podium, fog machine, portable LED lights, recording microphones', 'Yes', 'No. 25, Auditorium Road, Colombo 07, Sri Lanka', 'uploads/theatre_photos/theatre_69e47c4f48db1_images.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE `service_types` (
  `service_type_id` int(11) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_types`
--

INSERT INTO `service_types` (`service_type_id`, `service_type`, `created_at`, `updated_at`) VALUES
(1, 'Theater Production', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(2, 'Lighting Design', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(3, 'Sound Systems', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(4, 'Video Production', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(5, 'Set Design', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(6, 'Costume Design', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(7, 'Other', '2026-04-09 16:51:14', '2026-04-09 16:51:14'),
(22, 'Makeup & Hair', '2026-04-09 16:53:51', '2026-04-09 16:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `service_video_details`
--

CREATE TABLE `service_video_details` (
  `service_id` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text DEFAULT NULL,
  `services_offered` text DEFAULT NULL,
  `equipment_used` text DEFAULT NULL,
  `num_crew_members` int(11) DEFAULT NULL,
  `editing_software` varchar(255) DEFAULT NULL,
  `drone_service_available` varchar(10) DEFAULT NULL,
  `max_video_resolution` varchar(50) DEFAULT NULL,
  `photo_editing_included` varchar(10) DEFAULT NULL,
  `delivery_time` varchar(255) DEFAULT NULL,
  `raw_footage_provided` varchar(10) DEFAULT NULL,
  `portfolio_links` text DEFAULT NULL,
  `sample_videos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_video_details`
--

INSERT INTO `service_video_details` (`service_id`, `rate_per_hour`, `rate_type`, `description`, `services_offered`, `equipment_used`, `num_crew_members`, `editing_software`, `drone_service_available`, `max_video_resolution`, `photo_editing_included`, `delivery_time`, `raw_footage_provided`, `portfolio_links`, `sample_videos`) VALUES
(25, 30000.00, 'daily', 'Professional video production services for stage dramas, concerts, weddings, and promotional content. I handle filming, editing, and final delivery with high-quality visuals and cinematic storytelling.', 'Event videography, theatre recording, wedding videography, music videos, promotional videos, social media content, photography coverage', 'Sony A7III camera, Canon EOS R, DJI Ronin gimbal, tripod, LED panel lights, softbox lighting, shotgun microphones, lapel microphones', 3, 'Adobe Premiere Pro, After Effects, DaVinci Resolve', 'Yes', '4K', 'Yes', '5–7 business days', 'No', 'https://www.youtube.com/@sachinthaVisuals , https://www.instagram.com/sachintha.media', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `swiper_slides`
--

CREATE TABLE `swiper_slides` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `drama_id` int(11) DEFAULT NULL COMMENT 'Link to dramas table for View More button',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `swiper_slides`
--

INSERT INTO `swiper_slides` (`id`, `image_path`, `title`, `description`, `drama_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(16, 'uploads/dramas/poster_69e5a4123341e_hq720.jpg', 'Mathalan', 'About talent', 13, 6, 1, '2026-04-13 10:43:22', '2026-04-20 03:57:06'),
(17, 'uploads/dramas/poster_69e5a4386ac3d_maname.jpg', 'Maname', 'Submitted by director for home page approval', 12, 7, 1, '2026-04-13 11:24:33', '2026-04-20 03:57:44'),
(18, 'uploads/dramas/poster_69e5a48690446_hunuwataye_kathawa.jpg', 'Hunuwataye kathawa', 'Submitted by director for home page approval', 9, 8, 1, '2026-04-13 11:25:19', '2026-04-20 03:59:02'),
(19, 'uploads/dramas/poster_69e5a4b7edb30_Ko_Kukko_1.jpg', 'ko kukko', 'Submitted by director for home page approval', 11, 9, 1, '2026-04-13 11:25:56', '2026-04-20 03:59:52'),
(20, 'uploads/dramas/poster_69e5a5027ae0f_Kelani-Palama.jpg', 'Kelani Palama', 'Ruhini', 10, 10, 1, '2026-04-13 11:29:13', '2026-04-20 04:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL COMMENT 'Artist, Director, Audience, Service Provider',
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5 COMMENT '1-5 star rating',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `role`, `message`, `image_path`, `rating`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'Nirahsha', 'Director', 'Managing my stage team became so much easier. Great platform for directors and managers!', 'https://i.postimg.cc/XYkqj8Rp/profile3.jpg', 5, 2, 1, '2026-04-09 16:52:44', '2026-04-09 16:52:44'),
(9, 'Tharindu', 'Audience', 'As an audience member, I can easily book shows and discover new performances every week.', 'https://i.postimg.cc/g0M0R0kp/profile1.jpg', 5, 3, 1, '2026-04-09 16:53:51', '2026-04-09 16:53:51'),
(11, 'Santhosh', 'Director', 'Nice experince', 'uploads/content/testimonials/testimonial_69ddf1c5d172c_Screenshot 2025-12-09 201927.png', 4, 4, 1, '2026-04-14 07:50:29', '2026-04-14 07:50:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `wphone` varchar(15) DEFAULT NULL,
  `nic_number` varchar(20) DEFAULT NULL,
  `nic_photo_back` varchar(255) DEFAULT NULL,
  `role` enum('admin','artist','audience','service_provider') NOT NULL DEFAULT 'audience',
  `profile_image` varchar(255) DEFAULT NULL,
  `years_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether user is verified (1=yes, 0=no)',
  `verification_status` enum('pending','approved','rejected') DEFAULT 'approved' COMMENT 'Current verification status',
  `rejection_reason` text DEFAULT NULL COMMENT 'Reason for rejection if status is rejected',
  `verified_by_admin_id` int(11) DEFAULT NULL COMMENT 'Admin user ID who verified/rejected',
  `verified_by` int(11) DEFAULT NULL COMMENT 'Admin user ID who verified',
  `verified_at` datetime DEFAULT NULL COMMENT 'Timestamp of verification action',
  `nic_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `wphone`, `nic_number`, `nic_photo_back`, `role`, `profile_image`, `years_experience`, `created_at`, `updated_at`, `bio`, `location`, `website`, `is_verified`, `verification_status`, `rejection_reason`, `verified_by_admin_id`, `verified_by`, `verified_at`, `nic_photo`) VALUES
(1, 'System Administrator', 'rangamadala@admin.com', '$2y$10$70F5ytuaGcMIfW6VUoidGeN6mWePXajJqUpeUjF8Uylzfra5hHoXu', '+94701234567', NULL, 'TEMP_VALUE', NULL, 'admin', NULL, NULL, '2026-04-09 16:53:51', '2026-04-14 07:07:30', NULL, NULL, NULL, 1, 'approved', NULL, NULL, NULL, '2026-04-09 22:23:51', ''),
(2, 'Santhosh Wickramarathna', 'santhosh@gmail.com', '$2y$10$SUOqDbANQGxYovWn.gZIsOhDE/ba134aacIZHbQa2Ww9/2PTT2dyG', '0713425300', NULL, 'TEMP_VALUE', NULL, 'audience', NULL, NULL, '2026-04-09 16:57:51', '2026-04-14 07:07:30', NULL, NULL, NULL, 1, 'approved', NULL, NULL, NULL, NULL, ''),
(23, 'abc', 'abc@gmail.com', '$2y$10$lDm0C9Qpb4y0OfUf7cqMTuptx7Vm8A4JaeIaOXg8YChkILQ6pQJTC', '0711015851', NULL, '123456789V', 'uploads/nic/69df389347180_back_Screenshot (2).png', 'artist', NULL, NULL, '2026-04-15 05:37:16', '2026-04-15 07:04:51', NULL, NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-15 11:11:20', 'uploads/nic/69df240c2b4e5_front_architecture diagram.jpg'),
(24, 'ABC', 'abcd@gmail.com', '$2y$10$ADCl8lgyxm9dLdqGJe3z4O/Iw7Iz6Tjdv.BIoDuUt4707oiVTMbPe', '0711015851', NULL, '123456789V', 'uploads/nic/69df2670de9ff_back_Screenshot (2).png', 'artist', NULL, NULL, '2026-04-15 05:47:28', '2026-04-15 05:48:15', NULL, NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-15 11:18:15', 'uploads/nic/69df2670de9fa_front_Screenshot (1).png'),
(25, 'Ruhuni', 'qwe@gmail.com', '$2y$10$gDppWDZ5GJY2gE1dEkDGrOSRWmxHV8FMT17lu8FDf0UggcDybfC9C', '07179894445', NULL, '123456789V', 'uploads/nic/69df2936b02ad_back_Screenshot (4).png', 'artist', 'default_user.png', NULL, '2026-04-15 05:59:18', '2026-04-15 05:59:32', NULL, NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-15 11:29:32', 'uploads/nic/69df2936b01ad_front_Screenshot (3).png'),
(27, 'abc', 'abc@gmail.com', '$2y$10$pIPxADOPRUobulolBJZnCuVmD76VyQP5lFPMOdDbhwvY5RUx.TrcC', '0711015851', NULL, '123456789V', 'uploads/nic/69df389347180_back_Screenshot (2).png', 'artist', NULL, NULL, '2026-04-15 07:04:51', '2026-04-15 07:04:51', NULL, NULL, NULL, 0, 'pending', NULL, NULL, NULL, NULL, 'uploads/nic/69df38934717b_front_Screenshot (1).png'),
(44, 'Ruwan Perera', 'ruwanperera@gmail.com', '$2y$10$N27zgS84cr28Xsrn2isIQucmtxweQHwQ7SJZ8ClaM.MQ.LYj4VUyq', '0711015851', '0711015851', '200281102160', 'uploads/nic_photos/69e446a1a7e23_back_Screenshot (4).png', 'service_provider', 'user_profile.png', 3, '2026-04-19 03:06:09', '2026-04-19 09:53:20', 'Stage providers since 2000', NULL, NULL, 1, 'approved', NULL, NULL, NULL, NULL, 'uploads/nic_photos/69e446a1a73a4_front_Screenshot (1).png'),
(45, 'Nadeesha Perera', 'nadeesha.perera@gmail.com', '$2y$10$pR2tBdkLpNguBeSD6FyHSOKnHndXQsfnSCEc.gHLHOd14xyZS58Pq', '0771234567', NULL, '199512345678', 'uploads/nic_photos/69e47c4f4877a_back_Screenshot (3).png', 'service_provider', 'user_profile.png', 8, '2026-04-19 06:55:11', '2026-04-20 03:45:58', 'Experienced theatre service provider specializing in venue coordination, stage rental, rehearsal support, and live event production. Skilled in handling stage operations, artist support, audience management, and basic technical coordination for theatre performances, cultural shows, and drama festivals.', NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:15:58', 'uploads/nic_photos/69e47c4f47ef0_front_Screenshot (4).png'),
(46, 'Kasun Madushanka', 'kasunmadushanka@gmail.com', '$2y$10$jxBIu0Fr0qmnAOkKttVl..be4UzGULM5IilCXL1MGTsxY.buIAtW2', '0774589632', NULL, '199823456789', 'uploads/nic_photos/69e47f887b256_back_Screenshot (4).png', 'service_provider', 'user_profile.png', 7, '2026-04-19 07:08:56', '2026-04-20 03:45:53', 'Experienced lighting designer and technician specializing in stage dramas, live musical events, school concerts, and cultural performances. Skilled in lighting setup, cue programming, mood lighting, spotlight handling, and live show operation for medium and large-scale stages.', NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:15:53', 'uploads/nic_photos/69e47f887adf6_front_Screenshot (3).png'),
(47, 'Dilan Fernando', 'dilanfernando.sound@gmail.com', '$2y$10$BF/GPQ2DSEuio1eF19Gp.uXRWqMIXF//K8q7eqYIhchP8dOMA7rWG', '0718456239', NULL, '199623456789', 'uploads/nic_photos/69e480ac81d5b_back_Screenshot (3).png', 'service_provider', 'user_profile.png', 9, '2026-04-19 07:13:48', '2026-04-20 03:46:01', 'Experienced sound system provider specializing in theatre productions, musical events, live shows, and public functions. Skilled in PA system setup, microphone balancing, mixer operation, monitor control, and live sound management for small to large audiences.\r\n\r\nAvailability', NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:16:01', 'uploads/nic_photos/69e480ac8195a_front_Screenshot (4).png'),
(48, 'Sachintha Wijesinghe', 'sachintha.media@gmail.com', '$2y$10$c6L/XTDcL7GXfyY3oa8Qk.ooU/f0hearXhC1S1im8.mUuzC1XFGKq', '0769123456', NULL, '199523456789', 'uploads/nic_photos/69e481cf3c6fc_back_Screenshot (4).png', 'service_provider', 'user_profile.png', 6, '2026-04-19 07:18:39', '2026-04-20 03:46:04', 'Creative video producer with experience in event videography, theatre recordings, and promotional content. Skilled in camera operation, lighting setup, editing, and storytelling. Focused on delivering high-quality visuals for stage productions and live performances.', NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:16:04', 'uploads/nic_photos/69e481cf3c512_front_Screenshot (3).png'),
(49, 'Mahendra', 'mahendra@gmail.com', '$2y$10$Ekh46sauyX.3RSwM3Jh2w.iE2Fyz4.9AUFROcpVHSFB5Ggf45VV8S', '0766967867', NULL, '123456789V', 'uploads/nic/69e5a11f848f7_back_20250103.jpg', 'artist', 'user_profile.png', NULL, '2026-04-20 03:44:31', '2026-04-20 03:45:49', NULL, NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:15:49', 'uploads/nic/69e5a11f84791_front_20241220.jpg'),
(50, 'Hemal Ranasinghe', 'hemal@gmail.com', '$2y$10$ub4gv/5FyLOTsdu4e6R.tOpAiL7pnYqOzlbPo0rdiy2RkHNMnNUjO', '0718115276', NULL, '123456780V', 'uploads/nic/69e5a1def11bc_back_20250103.jpg', 'artist', 'user_profile.png', 3, '2026-04-20 03:47:43', '2026-04-20 04:29:55', 'hi', 'Matara', NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 09:18:06', 'uploads/nic/69e5a1def11b4_front_20241220.jpg'),
(51, 'Nimal Perera', 'nimal@gmail.com', '$2y$10$DIpq/gDqvaRnniFPzC02lezsN6qiijRaZ1Ibz/mq/GVvGUudmChhO', '0711015851', NULL, '123456788V', 'uploads/nic/69e5ab8e17d76_back_default_user.jpg', 'artist', 'user_profile.png', NULL, '2026-04-20 04:29:02', '2026-04-20 04:45:58', NULL, NULL, NULL, 1, 'approved', NULL, 1, NULL, '2026-04-20 10:15:58', 'uploads/nic/69e5ab8e17d6b_front_default_user.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_bios`
--

CREATE TABLE `user_bios` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_bios`
--

INSERT INTO `user_bios` (`id`, `user_id`, `bio`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 2, 'I am top fan of this site', 'profile_2_1776083739.png', '2026-04-12 09:30:00', '2026-04-13 12:35:39');

-- --------------------------------------------------------

--
-- Stand-in structure for view `verification_summary`
-- (See below for the actual view)
--
CREATE TABLE `verification_summary` (
`role` enum('admin','artist','audience','service_provider')
,`verification_status` enum('pending','approved','rejected')
,`count` bigint(21)
,`latest_registration` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `verification_summary`
--
DROP TABLE IF EXISTS `verification_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `verification_summary`  AS SELECT `users`.`role` AS `role`, `users`.`verification_status` AS `verification_status`, count(0) AS `count`, max(`users`.`created_at`) AS `latest_registration` FROM `users` WHERE `users`.`role` in ('artist','service_provider') GROUP BY `users`.`role`, `users`.`verification_status` ORDER BY `users`.`role` ASC, `users`.`verification_status` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artist_notifications`
--
ALTER TABLE `artist_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_notifications_drama` (`drama_id`),
  ADD KEY `idx_notifications_created` (`created_at`);

--
-- Indexes for table `artist_portfolios`
--
ALTER TABLE `artist_portfolios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_artist_portfolios_artist_id` (`artist_id`);

--
-- Indexes for table `audience_show_bookings`
--
ALTER TABLE `audience_show_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_show_booking_status` (`booking_status`),
  ADD KEY `idx_show_booking_order` (`payhere_order_id`),
  ADD KEY `idx_show_booking_audience_drama` (`audience_id`,`drama_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_class_enrollment` (`class_id`,`user_id`),
  ADD KEY `idx_class_enrollments_class_id` (`class_id`),
  ADD KEY `idx_class_enrollments_user_id` (`user_id`);

--
-- Indexes for table `class_enrollment_payments`
--
ALTER TABLE `class_enrollment_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_class_payment_order_id` (`order_id`),
  ADD KEY `idx_class_payment_class_user` (`class_id`,`user_id`),
  ADD KEY `idx_class_payment_status` (`status`),
  ADD KEY `fk_class_payment_user_id` (`user_id`);

--
-- Indexes for table `dramas`
--
ALTER TABLE `dramas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `creator_artist_id` (`creator_artist_id`),
  ADD KEY `idx_dramas_is_published` (`is_published`),
  ADD KEY `idx_dramas_event_date` (`event_date`),
  ADD KEY `idx_dramas_category_id` (`category_id`),
  ADD KEY `idx_dramas_published_by` (`published_by`);

--
-- Indexes for table `drama_budgets`
--
ALTER TABLE `drama_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drama_id` (`drama_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `drama_classes`
--
ALTER TABLE `drama_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drama_classes_created_by` (`created_by`),
  ADD KEY `idx_drama_classes_class_date` (`class_date`);

--
-- Indexes for table `drama_creation_requests`
--
ALTER TABLE `drama_creation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dcr_certificate_status` (`certificate_number`,`status`),
  ADD KEY `idx_dcr_requested_by` (`requested_by`),
  ADD KEY `idx_dcr_status` (`status`),
  ADD KEY `idx_dcr_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_dcr_created_drama_id` (`created_drama_id`);

--
-- Indexes for table `drama_manager_assignments`
--
ALTER TABLE `drama_manager_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_drama_active_manager` (`drama_id`,`status`),
  ADD KEY `idx_manager_artist` (`manager_artist_id`),
  ADD KEY `idx_assigned_by` (`assigned_by`);

--
-- Indexes for table `drama_manager_requests`
--
ALTER TABLE `drama_manager_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drama_request` (`drama_id`),
  ADD KEY `idx_artist_request` (`artist_id`),
  ADD KEY `idx_director_request` (`director_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_pending_requests` (`artist_id`,`status`,`requested_at`);

--
-- Indexes for table `drama_ratings`
--
ALTER TABLE `drama_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_drama_user_rating` (`drama_id`,`user_id`),
  ADD KEY `idx_drama_ratings_drama` (`drama_id`),
  ADD KEY `idx_drama_ratings_user` (`user_id`),
  ADD KEY `idx_drama_ratings_rating` (`rating`);

--
-- Indexes for table `drama_roles`
--
ALTER TABLE `drama_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drama_roles_drama_id` (`drama_id`),
  ADD KEY `idx_drama_roles_created_by` (`created_by`),
  ADD KEY `idx_drama_roles_status` (`status`),
  ADD KEY `idx_drama_roles_is_published` (`is_published`),
  ADD KEY `idx_drama_roles_published_by` (`published_by`);

--
-- Indexes for table `drama_schedules`
--
ALTER TABLE `drama_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drama_date` (`drama_id`,`scheduled_date`),
  ADD KEY `idx_drama_status` (`drama_id`,`status`),
  ADD KEY `idx_scheduled_date` (`scheduled_date`),
  ADD KEY `drama_schedules_ibfk_2` (`role_id`),
  ADD KEY `drama_schedules_ibfk_3` (`created_by`);

--
-- Indexes for table `drama_services`
--
ALTER TABLE `drama_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_drama_service` (`drama_id`,`service_type`),
  ADD KEY `idx_drama_services_drama_id` (`drama_id`),
  ADD KEY `idx_drama_services_service_type` (`service_type`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_request` (`service_request_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_type` (`payment_type`),
  ADD KEY `idx_paid_by` (`paid_by`),
  ADD KEY `idx_paid_to` (`paid_to`),
  ADD KEY `idx_gateway_order_id` (`gateway_order_id`),
  ADD KEY `idx_gateway_payment_id` (`gateway_payment_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `provider_availability`
--
ALTER TABLE `provider_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_date` (`provider_id`,`available_date`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `available_date` (`available_date`),
  ADD KEY `availability_ibfk_request` (`service_request_id`);

--
-- Indexes for table `role_applications`
--
ALTER TABLE `role_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`role_id`,`artist_id`),
  ADD KEY `idx_role_applications_role_id` (`role_id`),
  ADD KEY `idx_role_applications_artist_id` (`artist_id`),
  ADD KEY `idx_role_applications_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_role_applications_profile_viewed_by` (`profile_viewed_by`),
  ADD KEY `idx_role_applications_interview_scheduled_by` (`interview_scheduled_by`);

--
-- Indexes for table `role_assignments`
--
ALTER TABLE `role_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`role_id`,`artist_id`),
  ADD KEY `idx_role_assignments_role_id` (`role_id`),
  ADD KEY `idx_role_assignments_artist_id` (`artist_id`),
  ADD KEY `idx_role_assignments_assigned_by` (`assigned_by`);

--
-- Indexes for table `role_requests`
--
ALTER TABLE `role_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_artist_request` (`role_id`,`artist_id`),
  ADD KEY `idx_role_requests_role_id` (`role_id`),
  ADD KEY `idx_role_requests_artist_id` (`artist_id`),
  ADD KEY `idx_role_requests_director_id` (`director_id`),
  ADD KEY `idx_role_requests_status` (`status`);

--
-- Indexes for table `serviceprovider`
--
ALTER TABLE `serviceprovider`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `service_type_id` (`service_type_id`),
  ADD KEY `idx_services_service_type_id` (`service_type_id`);

--
-- Indexes for table `service_costume_details`
--
ALTER TABLE `service_costume_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_lighting_details`
--
ALTER TABLE `service_lighting_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_makeup_details`
--
ALTER TABLE `service_makeup_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_other_details`
--
ALTER TABLE `service_other_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drama_id` (`drama_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `service_set_details`
--
ALTER TABLE `service_set_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_sound_details`
--
ALTER TABLE `service_sound_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_theater_details`
--
ALTER TABLE `service_theater_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`service_type_id`),
  ADD UNIQUE KEY `service_type` (`service_type`);

--
-- Indexes for table `service_video_details`
--
ALTER TABLE `service_video_details`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `swiper_slides`
--
ALTER TABLE `swiper_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_swiper_drama_id` (`drama_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_users_location` (`location`),
  ADD KEY `idx_users_verification_status` (`verification_status`),
  ADD KEY `idx_users_is_verified` (`is_verified`),
  ADD KEY `idx_users_verified_by` (`verified_by`),
  ADD KEY `idx_users_verified_by_admin_id` (`verified_by_admin_id`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `user_bios`
--
ALTER TABLE `user_bios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artist_notifications`
--
ALTER TABLE `artist_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `artist_portfolios`
--
ALTER TABLE `artist_portfolios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audience_show_bookings`
--
ALTER TABLE `audience_show_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `class_enrollment_payments`
--
ALTER TABLE `class_enrollment_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dramas`
--
ALTER TABLE `dramas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `drama_budgets`
--
ALTER TABLE `drama_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drama_classes`
--
ALTER TABLE `drama_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drama_creation_requests`
--
ALTER TABLE `drama_creation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `drama_manager_assignments`
--
ALTER TABLE `drama_manager_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drama_manager_requests`
--
ALTER TABLE `drama_manager_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `drama_ratings`
--
ALTER TABLE `drama_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drama_roles`
--
ALTER TABLE `drama_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drama_schedules`
--
ALTER TABLE `drama_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drama_services`
--
ALTER TABLE `drama_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provider_availability`
--
ALTER TABLE `provider_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `role_applications`
--
ALTER TABLE `role_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_assignments`
--
ALTER TABLE `role_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_requests`
--
ALTER TABLE `role_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `service_types`
--
ALTER TABLE `service_types`
  MODIFY `service_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `swiper_slides`
--
ALTER TABLE `swiper_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `user_bios`
--
ALTER TABLE `user_bios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artist_notifications`
--
ALTER TABLE `artist_notifications`
  ADD CONSTRAINT `artist_notifications_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `artist_notifications_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `artist_portfolios`
--
ALTER TABLE `artist_portfolios`
  ADD CONSTRAINT `fk_artist_portfolios_artist` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  ADD CONSTRAINT `fk_class_enrollments_class_id` FOREIGN KEY (`class_id`) REFERENCES `drama_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_enrollments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_enrollment_payments`
--
ALTER TABLE `class_enrollment_payments`
  ADD CONSTRAINT `fk_class_payment_class_id` FOREIGN KEY (`class_id`) REFERENCES `drama_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_payment_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dramas`
--
ALTER TABLE `dramas`
  ADD CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drama_budgets`
--
ALTER TABLE `drama_budgets`
  ADD CONSTRAINT `drama_budgets_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_budgets_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drama_classes`
--
ALTER TABLE `drama_classes`
  ADD CONSTRAINT `fk_drama_classes_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drama_creation_requests`
--
ALTER TABLE `drama_creation_requests`
  ADD CONSTRAINT `fk_dcr_created_drama_id` FOREIGN KEY (`created_drama_id`) REFERENCES `dramas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_dcr_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dcr_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drama_manager_assignments`
--
ALTER TABLE `drama_manager_assignments`
  ADD CONSTRAINT `drama_manager_assignments_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_manager_assignments_ibfk_2` FOREIGN KEY (`manager_artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_manager_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drama_manager_requests`
--
ALTER TABLE `drama_manager_requests`
  ADD CONSTRAINT `drama_manager_requests_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_manager_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_manager_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drama_ratings`
--
ALTER TABLE `drama_ratings`
  ADD CONSTRAINT `fk_drama_ratings_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_drama_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drama_roles`
--
ALTER TABLE `drama_roles`
  ADD CONSTRAINT `drama_roles_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_roles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `drama_roles_ibfk_3` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drama_schedules`
--
ALTER TABLE `drama_schedules`
  ADD CONSTRAINT `drama_schedules_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drama_schedules_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `drama_schedules_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drama_services`
--
ALTER TABLE `drama_services`
  ADD CONSTRAINT `drama_services_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_paid_by` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_paid_to` FOREIGN KEY (`paid_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_availability`
--
ALTER TABLE `provider_availability`
  ADD CONSTRAINT `availability_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `availability_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_applications`
--
ALTER TABLE `role_applications`
  ADD CONSTRAINT `role_applications_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_applications_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `role_applications_interview_scheduled_fk` FOREIGN KEY (`interview_scheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `role_applications_profile_viewed_fk` FOREIGN KEY (`profile_viewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_assignments`
--
ALTER TABLE `role_assignments`
  ADD CONSTRAINT `role_assignments_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_assignments_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_requests`
--
ALTER TABLE `role_requests`
  ADD CONSTRAINT `role_requests_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `serviceprovider`
--
ALTER TABLE `serviceprovider`
  ADD CONSTRAINT `serviceprovider_ibfk_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `services_ibfk_service_type` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `service_costume_details`
--
ALTER TABLE `service_costume_details`
  ADD CONSTRAINT `costume_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_lighting_details`
--
ALTER TABLE `service_lighting_details`
  ADD CONSTRAINT `lighting_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_makeup_details`
--
ALTER TABLE `service_makeup_details`
  ADD CONSTRAINT `makeup_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_other_details`
--
ALTER TABLE `service_other_details`
  ADD CONSTRAINT `other_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_set_details`
--
ALTER TABLE `service_set_details`
  ADD CONSTRAINT `set_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_sound_details`
--
ALTER TABLE `service_sound_details`
  ADD CONSTRAINT `sound_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_theater_details`
--
ALTER TABLE `service_theater_details`
  ADD CONSTRAINT `theater_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_video_details`
--
ALTER TABLE `service_video_details`
  ADD CONSTRAINT `video_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `swiper_slides`
--
ALTER TABLE `swiper_slides`
  ADD CONSTRAINT `fk_swiper_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_bios`
--
ALTER TABLE `user_bios`
  ADD CONSTRAINT `user_bios_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
