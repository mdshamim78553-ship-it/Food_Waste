-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 22, 2026 at 04:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_waste`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'biplob hossain', 'biplobhossain@gmail.com', 'Partnership', 'our resturent near kawla bazar H2O wants to be in a partnership with your platform to help people', '2026-11-22 13:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `food_item` varchar(150) NOT NULL,
  `food_title` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `expiry_time` datetime NOT NULL,
  `pickup_address` text NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `food_image` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `food_item`, `food_title`, `category`, `quantity`, `expiry_time`, `pickup_address`, `contact_phone`, `food_image`, `status`, `created_at`) VALUES
(1, 1, 'Rice', '', '', '20', '0000-00-00 00:00:00', 'Dhaka,airport kawlar bazar', '', NULL, 'Accepted', '2026-11-21 15:08:18'),
(2, 1, 'rice', 'rice', 'Cooked Food', 'For 2 people', '2026-11-30 21:43:00', 'Dhaka,airport kawlar bazar', '01777245653', 'food_1795276133_9930.jpg', 'Available', '2026-11-21 15:48:53'),
(3, 1, 'chicken', '', '', '1', '0000-00-00 00:00:00', 'rajshahi', '', NULL, 'Requested', '2026-11-21 15:55:40'),
(4, 1, 'biriany', 'biriany', 'Cooked Food', 'for 2 people', '2026-11-22 22:01:00', 'nub campus', '01314433090', 'food_1795277011_7250.jpg', 'Accepted', '2026-11-21 16:03:31');

-- --------------------------------------------------------

--
-- Table structure for table `food_listings`
--

CREATE TABLE `food_listings` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `food_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `pickup_address` text DEFAULT NULL,
  `status` enum('available','requested','donated','expired') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_requests`
--

CREATE TABLE `food_requests` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_requests`
--

INSERT INTO `food_requests` (`id`, `donation_id`, `receiver_id`, `status`, `requested_at`) VALUES
(1, 1, 1, 'Accepted', '2026-11-21 15:29:56'),
(2, 3, 1, 'Pending', '2026-11-21 15:57:58'),
(3, 4, 1, 'Rejected', '2026-11-21 16:04:17'),
(4, 2, 6, 'Rejected', '2026-11-22 13:36:33'),
(5, 4, 6, 'Accepted', '2026-11-22 13:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `request_id`, `sender_id`, `message`, `created_at`) VALUES
(1, 5, 6, 'hi can i pickup the food now?', '2026-11-22 13:40:43'),
(2, 5, 1, 'yea sure ,,campus gate e dekha koro at 8:00', '2026-11-22 13:41:32'),
(3, 5, 1, 'tumi kothai?', '2026-11-22 14:45:31'),
(4, 5, 6, 'gate er shamne', '2026-11-22 14:47:46');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `page_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `state_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `role` enum('admin','donor','receiver') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `state_id`, `city_id`, `role`, `created_at`) VALUES
(1, 'tisha', 'tanjintisha522003@gmail.com', 'Foodwaste123', '01314433090', 'dhaka', NULL, NULL, 'donor', '2026-11-09 21:01:24'),
(3, 'shamim', 'shamimhossain123@gmail.com', 'Foodwaste123', '01777245653', 'pabna', NULL, NULL, 'donor', '2026-11-10 03:31:03'),
(5, 'shamim', 'testuser@gmail.com', 'Foodwaste123', '01777245653', 'pabna', NULL, NULL, 'donor', '2026-11-10 03:33:25'),
(6, 'sweety', 'sweety123@gmail.com', '$2y$10$JEl2K9MKXxoiGa3DZAlbmeul4ZcylmX6gniiuoWfaSiBxXU1QDxBW', '01314433091', 'dhaka,airport,ashkona', NULL, NULL, 'receiver', '2026-11-22 13:00:23'),
(7, 'System Admin', 'admin@foodshare.com', 'admin123', '01777245653', 'Dhaka, Bangladesh', NULL, NULL, 'admin', '2026-11-22 13:12:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `food_requests`
--
ALTER TABLE `food_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `city_id` (`city_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `food_listings`
--
ALTER TABLE `food_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_requests`
--
ALTER TABLE `food_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD CONSTRAINT `food_listings_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
