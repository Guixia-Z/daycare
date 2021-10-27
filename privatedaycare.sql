-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3333
-- Generation Time: Oct 27, 2021 at 08:21 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `privatedaycare`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `startTS` timestamp NOT NULL DEFAULT current_timestamp(),
  `endTS` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('present','absent') NOT NULL,
  `note` varchar(100) DEFAULT NULL,
  `childId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `startTS`, `endTS`, `status`, `note`, `childId`) VALUES
(1, '2021-10-26 12:30:16', '2021-10-26 21:20:16', 'present', NULL, 1),
(2, '2021-10-26 12:04:26', '2021-10-26 21:50:27', 'present', NULL, 2),
(3, '2021-10-26 11:46:12', '2021-10-27 21:27:10', 'present', NULL, 3),
(4, '2021-10-26 04:00:00', '2021-10-26 04:00:00', 'absent', 'Edward had a fever.', 4),
(5, '2021-10-26 12:39:59', '2021-10-26 19:27:19', 'present', NULL, 5),
(6, '2021-10-26 12:23:03', '2021-10-26 19:48:06', 'present', NULL, 6),
(7, '2021-10-26 12:11:15', '2021-10-26 19:47:15', 'present', NULL, 7),
(8, '2021-10-26 12:13:32', '2021-10-26 19:57:20', 'present', NULL, 8),
(9, '2021-10-26 04:00:00', '2021-10-26 04:00:00', 'absent', 'Steve coughs a lot.', 9),
(10, '2021-10-26 11:58:18', '2021-10-26 19:59:37', 'present', NULL, 10),
(11, '2021-10-26 12:01:29', '2021-10-26 21:47:12', 'present', NULL, 11),
(12, '2021-10-26 12:18:01', '2021-10-26 21:48:00', 'present', NULL, 12);

-- --------------------------------------------------------

--
-- Table structure for table `childnotes`
--

CREATE TABLE `childnotes` (
  `id` int(11) NOT NULL,
  `noteCreatedTS` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `weight (kg)` decimal(5,2) NOT NULL,
  `height (cm)` decimal(5,2) NOT NULL,
  `skills` varchar(100) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `photoFilePath` varchar(200) DEFAULT NULL,
  `educatorId` int(11) NOT NULL,
  `childId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `childnotes`
--

INSERT INTO `childnotes` (`id`, `noteCreatedTS`, `weight (kg)`, `height (cm)`, `skills`, `note`, `photoFilePath`, `educatorId`, `childId`) VALUES
(1, '2021-10-27 04:05:11', '8.30', '70.10', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 1, 1),
(2, '2021-10-27 06:16:50', '8.85', '73.80', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 1, 2),
(3, '2021-10-27 06:18:56', '11.50', '85.10', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 1, 3),
(4, '2021-10-27 06:40:27', '12.70', '87.70', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 2, 4),
(5, '2021-10-27 06:41:34', '13.60', '92.10', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 2, 5),
(6, '2021-10-27 06:42:46', '13.10', '89.20', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 2, 6),
(7, '2021-10-27 06:42:46', '13.90', '94.20', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 4, 7),
(8, '2021-10-27 06:42:46', '14.90', '97.60', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 4, 8),
(9, '2021-10-27 07:08:02', '15.30', '99.00', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 4, 9),
(10, '2021-10-27 07:08:02', '16.30', '102.50', 'swimming', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 8, 10),
(11, '2021-10-27 07:08:02', '17.40', '105.90', 'swimming', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 8, 11),
(12, '2021-10-27 07:08:02', '16.90', '104.50', 'singing', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis leo et dolor interdum, a sollicitudin nulla ultricies. Morbi a justo quam. Etiam tellus augue, pulvinar ut lobortis ut, volutpat id metus. Etiam ut tincidunt odio, et porta est. Morbi mollis, arcu non accumsan mollis, orci augue rutrum odio, quis posuere sem magna sit amet ipsum. Praesent a leo condimentum, fringilla nibh sit amet, laoreet metus. In maximus scelerisque vehicula.', NULL, 8, 12);

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `firstSchoolDay` date NOT NULL,
  `emergencyContact` varchar(100) DEFAULT NULL,
  `emergencyPhone` varchar(20) DEFAULT NULL,
  `parentId` int(11) NOT NULL,
  `educatorId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `firstName`, `lastName`, `gender`, `dateOfBirth`, `firstSchoolDay`, `emergencyContact`, `emergencyPhone`, `parentId`, `educatorId`, `groupId`) VALUES
(1, 'Amanda', 'Brown', 'F', '2020-10-15', '2021-09-07', 'Jerry Brown', '514-660-2788', 3, 1, 1),
(2, 'Judy', 'Martin', 'F', '2020-02-17', '2020-09-08', 'John Johnson', '514-903-1688', 9, 1, 1),
(3, 'Ryan', 'MacDonald', 'M', '2019-12-20', '2020-09-08', 'David MacDonald', '514-846-8886', 10, 1, 1),
(4, 'Edward', 'Taylor', 'M', '2019-05-09', '2020-09-08', 'Robert Taylor', '514-932-2888', 11, 2, 2),
(5, 'Charles', 'Thompson', 'M', '2019-02-01', '2020-09-08', 'Mary Campbell', '514-846-6688', 12, 2, 2),
(6, 'Cindy', 'Gauthier', 'F', '2018-12-10', '2020-09-08', 'Linda Anderson', '514-846-6688', 13, 2, 2),
(7, 'Amy', 'Black', 'F', '2018-10-23', '2019-09-09', 'Jennifer Jones', '514-903-7979', 14, 4, 3),
(8, 'Denise', 'Morin', 'F', '2018-07-06', '2019-09-09', 'Lisa Leblanc', '514-937-0156', 15, 4, 3),
(9, 'Steve', 'Young', 'M', '2018-01-09', '2019-09-09', 'Susan Cote', '514-933-9399', 16, 4, 3),
(10, 'Gerald', 'Bouchard', 'M', '2017-09-30', '2018-09-10', 'Jean Williams', '438-924-0056', 17, 8, 4),
(11, 'Alex', 'Miller', 'M', '2017-06-07', '2018-09-10', 'Paul Miller', '514-663-6286', 18, 8, 4),
(12, 'Shirley', 'Martin', 'F', '2017-01-05', '2018-09-10', 'Gary Martin', '438-669-6569', 7, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `groupName` varchar(25) NOT NULL,
  `groupDescription` varchar(50) NOT NULL,
  `roomNumber` varchar(10) NOT NULL,
  `educatorId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupName`, `groupDescription`, `roomNumber`, `educatorId`) VALUES
(1, 'Babies', 'for children 0.5 - 2 yr', 'Room 201', 1),
(2, 'Toddlers', 'for children 2 - 3 yr', 'Room 203', 2),
(3, 'Big kids', 'for children 3 - 4 yr', 'Room 101', 4),
(4, 'Preschoolers', 'for children 4 - 5 yr', 'Room 103', 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('parent','educator','admin') NOT NULL,
  `createdTS` timestamp NOT NULL DEFAULT current_timestamp(),
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `createdTS`, `firstName`, `lastName`, `gender`, `phoneNumber`, `address`) VALUES
(1, 'Cathy@123.com', 'Cathy123', 'educator', '2021-10-27 01:23:42', 'Cathy', 'Roy', 'F', '438-380-3840', '1978 Boulevard de Maisonneuve O'),
(2, 'Joe@123.com', 'Joe123', 'educator', '2021-10-27 01:29:29', 'Joe', 'Gagnon', 'F', '514-951-7653', '1800 Sherbrooke St W'),
(3, 'Jerry@123.com', 'Jerry123', 'parent', '2021-10-24 13:43:57', 'Jerry', 'Brown', 'M', '514-660-2788', '3360 Av. des Grandes Tourelles'),
(4, 'Emma@123.com', 'Emma123', 'educator', '2021-10-24 13:45:04', 'Emma', 'Tremblay', 'F', '514-596-7168', '6343 Sherbrooke St E'),
(5, 'Lily@123.com', 'Lily123', 'admin', '2021-10-24 13:47:08', 'Lily', 'Lee', 'F', '514-228-2288', '668 DuFort street'),
(6, 'Tom@123.com', 'Tom123', 'parent', '2021-10-24 14:40:20', 'Tom', 'Smith', 'M', '438-987-1433', '452 St Mathieu St B'),
(7, 'Gary@123.com', 'Gary123', 'parent', '2021-10-25 13:44:24', 'Gary', 'Martin', 'M', '438-669-6569', '1862 Boulevard de Maisonneuve O'),
(8, 'Karen@123.com', 'Karen123', 'educator', '2021-10-27 01:35:16', 'Karen', 'Wilson', 'F', '514-313-8000', '1853 Saint-Catherine St W'),
(9, 'John@123.com', 'John123', 'parent', '2021-10-27 01:55:00', 'John', 'Johnson', 'M', '514-903-1688', '1444 St Mathieu St'),
(10, 'David@123.com', 'David123', 'parent', '2021-10-27 01:58:36', 'David', 'MacDonald', 'M', '514-846-8886', '1909 Saint-Catherine St W'),
(11, 'Robert@123.com', 'Robert123', 'parent', '2021-10-27 02:01:58', 'Robert', 'Taylor', 'M', '514-932-2888', '1871 Saint-Catherine St W'),
(12, 'Mary@123.com', 'Mary123', 'parent', '2021-10-27 02:05:36', 'Mary', 'Campbell', 'F', '514-989-9048', '1409 Rue Saint-Marc'),
(13, 'Linda@123.com', 'Linda123', 'parent', '2021-10-27 02:08:30', 'Linda', 'Anderson', 'F', '514-846-6688', '1452A St Mathieu St'),
(14, 'Jennifer@123.com', 'Jennifer123', 'parent', '2021-10-27 02:13:15', 'Jennifer', 'Jones', 'F', '514-903-7979', '1626 Maisonneuve Blvd W'),
(15, 'Lisa@123.com', 'Lisa123', 'parent', '2021-10-27 02:15:48', 'Lisa', 'Leblanc', 'F', '514-937-0156', '2077 Saint-Catherine St'),
(16, 'Susan@123.com', 'Susan123', 'parent', '2021-10-27 02:19:16', 'Susan', 'Cote', 'F', '514-933-9399', '2079 Saint-Catherine St W'),
(17, 'Jean@123.com', 'Jean123', 'parent', '2021-10-27 02:22:16', 'Jean', 'Williams', 'F', '438-924-0056', '1620 Lincoln Ave'),
(18, 'Paul@123.com', 'Paul123', 'parent', '2021-10-27 02:48:16', 'Paul', 'Miller', 'M', '514-663-6286', '1570 Saint-Catherine St W');

-- --------------------------------------------------------

--
-- Table structure for table `waitinglist`
--

CREATE TABLE `waitinglist` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `hasSibling` char(1) NOT NULL,
  `parentId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `waitinglist`
--

INSERT INTO `waitinglist` (`id`, `firstName`, `lastName`, `gender`, `dateOfBirth`, `hasSibling`, `parentId`) VALUES
(1, 'Ken', 'Smith', 'M', '2020-09-22', 'N', 6),
(2, 'Yoyo', 'Martin', 'F', '2021-01-02', 'Y', 7),
(3, 'Leo', 'Tremblay', 'M', '2021-02-03', 'Y', 17);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `childId` (`childId`);

--
-- Indexes for table `childnotes`
--
ALTER TABLE `childnotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `educatorId` (`educatorId`),
  ADD KEY `childId` (`childId`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parentId` (`parentId`),
  ADD KEY `educatorId` (`educatorId`),
  ADD KEY `groupId` (`groupId`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `educatorId` (`educatorId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `waitinglist`
--
ALTER TABLE `waitinglist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parentId` (`parentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `childnotes`
--
ALTER TABLE `childnotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `waitinglist`
--
ALTER TABLE `waitinglist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`childId`) REFERENCES `children` (`id`);

--
-- Constraints for table `childnotes`
--
ALTER TABLE `childnotes`
  ADD CONSTRAINT `childnotes_ibfk_1` FOREIGN KEY (`educatorId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `childnotes_ibfk_2` FOREIGN KEY (`childId`) REFERENCES `children` (`id`);

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parentId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `children_ibfk_2` FOREIGN KEY (`educatorId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `children_ibfk_3` FOREIGN KEY (`groupId`) REFERENCES `groups` (`id`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`educatorId`) REFERENCES `users` (`id`);

--
-- Constraints for table `waitinglist`
--
ALTER TABLE `waitinglist`
  ADD CONSTRAINT `waitinglist_ibfk_1` FOREIGN KEY (`parentId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
