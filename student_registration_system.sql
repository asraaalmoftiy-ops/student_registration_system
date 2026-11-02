-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02 نوفمبر 2026 الساعة 18:07
-- إصدار الخادم: 10.1.29-MariaDB
-- PHP Version: 7.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_registration_system`
--

-- --------------------------------------------------------

--
-- بنية الجدول `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `profile_picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `address`, `profile_picture`, `created_at`) VALUES
(2, 'سلمى', 'salma77@gmail.com', '0995693499', 'جوبر', NULL, '2026-10-12 22:46:51'),
(3, 'محمد', 'mhmd2@gmail.com', '0995695698', 'درعا', NULL, '2026-10-12 22:56:24'),
(4, 'علي', 'aliali@gmail.com', '0982595698', 'دمشق', NULL, '2026-10-12 22:57:11'),
(5, 'مايا', 'maya12@gmail.com', '0995632148', 'هامة ريف دمشق', NULL, '2026-10-12 22:58:49'),
(6, 'ملاك', 'malak457@gmail.com', '0997695458', 'ريف دمشق التل', NULL, '2026-10-12 22:59:58'),
(7, 'دينيز', 'deniz@gmail.com', '0269853471', 'برزة', NULL, '2026-10-12 23:01:07'),
(8, 'مريم', 'mariamww@gmail.com', '0635289745', 'جديدة الفضل', NULL, '2026-10-12 23:02:00'),
(9, 'أحمد العلي', 'ahmed4477@gmail.com', '0236985475', 'مزة جبل', NULL, '2026-10-12 23:03:03'),
(10, 'إسلام', 'islam@gmail.com', '00963582145', 'قدسيا دمشق', NULL, '2026-10-12 23:04:10'),
(11, 'رتاج', 'ritaj@gmail.com', '0996583256', 'دمر البلد', NULL, '2026-10-12 23:05:22'),
(12, 'مرام', 'maram@gmail.com', '0987569835', 'جرمانا', NULL, '2026-10-12 23:06:30'),
(13, 'عمر', 'omar@gmail.com', '0965892235', 'حماة', NULL, '2026-10-12 23:07:23'),
(23, 'سامر', 'samer@gmail.com', '0236984455', 'حمص', 'uploads/f385a0f5ba90b8f180d4a36a55e90c10.jpg', '2026-10-14 10:58:58'),
(25, 'إسراء', 'asraa@gmail.com', '0992653477', 'دمشق', NULL, '2026-10-14 19:01:47'),
(26, 'ماهر', 'maher@gmail.com', '0986536985', 'دمشق', 'uploads/de637e2bd8d688a94aaf9714559ac9a7.jpg', '2026-10-15 10:49:44'),
(27, 'مالك', 'malik@gmail.com', '0985623615', 'دمشق صناعة', NULL, '2026-10-15 10:51:53'),
(28, 'عادل محمد', 'aadeel@gmail.com', '0978854223', 'دمر', 'uploads/bfd552d6076b167ef22f5794de6941ad.jpg', '2026-10-19 15:04:03'),
(29, 'دانية', 'dania@gmail.com', '0995193447', 'دشق الميدان', NULL, '2026-10-19 16:18:15');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'esraa', 'esraa@gmail.com', '$2y$10$kADpc3ido1VKirdhO9hnPOtREQgmf45SlgCNslWCZpuSt/RWS4.AW', '2026-10-12 21:30:48'),
(2, 'ahmed', 'ahmed@gmail.com', '$2y$10$OZh4zIXMKUnydAjoMS.EfOwjovIBMUOWAZ3cUuV2HspiaX.PfmxyK', '2026-10-14 12:00:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
