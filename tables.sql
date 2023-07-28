-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2023 at 12:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dispense`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `administratorId` int(11) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `passwordHash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`administratorId`, `emailAddress`, `phoneNumber`, `gender`, `passwordHash`) VALUES
(14, 'ad@yahoo.com', '12103923', 'Male', '$2y$10$ys12v03G/ddaLUr4I.34I.ggFsrA4vxibB0ByjBeFhW4kxBVzoJnO');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `contractId` int(11) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `pharmacyId` int(11) DEFAULT NULL,
  `pharmaceuticalId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `doctorId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `SSN` varchar(15) DEFAULT NULL,
  `passwordHash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctorId`, `name`, `gender`, `emailAddress`, `phoneNumber`, `SSN`, `passwordHash`) VALUES
(1, 'Ali Nadim', 'Male', 'ad@yahoo.com', '12103923', '123456', '$2y$10$WRh/VzUqOkWkbpybN9RJ8.jD4kP5xsCQalqNf8wJTmruE0to/IAVa');

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

CREATE TABLE `drug` (
  `drugId` int(11) NOT NULL,
  `scientificName` varchar(100) DEFAULT NULL,
  `tradeName` varchar(100) DEFAULT NULL,
  `contractId` int(11) DEFAULT NULL,
  `form` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patientId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `SSN` varchar(15) DEFAULT NULL,
  `dateOfBirth` date DEFAULT NULL,
  `passwordHash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patientId`, `name`, `gender`, `emailAddress`, `phoneNumber`, `SSN`, `dateOfBirth`, `passwordHash`) VALUES
(1, 'Ali Nadim', 'Male', 'ad@yahoo.com', '021312312', '98812983123', '2000-12-12', '$2y$10$gJobAgCTyYbmGQ6EVO5nn.ELH2iAk8CyxXH/y7oAM6Io13ttso6/a'),
(2, 'Ali Nadim', 'Male', 'ad@yahoo.com', '12103923', '12345', '2000-12-12', '$2y$10$ZSMQPwcBkKzEpteRahLI/eu8aioAsHlw7iyrHQfwEDg9vsPQk8/jS');

-- --------------------------------------------------------

--
-- Table structure for table `patient_doctor`
--

CREATE TABLE `patient_doctor` (
  `patientDoctorId` int(11) NOT NULL,
  `patientId` int(11) DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  `isPrimary` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmaceutical`
--

CREATE TABLE `pharmaceutical` (
  `pharmaceuticalId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmaceutical`
--

INSERT INTO `pharmaceutical` (`pharmaceuticalId`, `name`, `emailAddress`, `phoneNumber`) VALUES
(1, 'Good Stuff', 'ad@yahoo.com', '12103923');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacist`
--

CREATE TABLE `pharmacist` (
  `pharmacistId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `SSN` varchar(15) DEFAULT NULL,
  `pharmacyId` int(11) DEFAULT NULL,
  `passwordHash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacist`
--

INSERT INTO `pharmacist` (`pharmacistId`, `name`, `emailAddress`, `phoneNumber`, `SSN`, `pharmacyId`, `passwordHash`) VALUES
(1, 'Ali Nadim', 'ad@yahoo.com', '12103923', '123459', 1, '$2y$10$KiBSmkZJ3nrsf48nk2p/0.69BtA9/g7hR6E2rpCZQGKkjo0E.DohS');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

CREATE TABLE `pharmacy` (
  `pharmacyId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy`
--

INSERT INTO `pharmacy` (`pharmacyId`, `name`, `emailAddress`, `phoneNumber`) VALUES
(1, 'Good Stuff', 'ad@yahoo.com', '12103923');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescriptionId` int(11) NOT NULL,
  `drugId` int(11) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `patientDoctorId` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `isDispensed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor`
--

CREATE TABLE `supervisor` (
  `supervisorId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pharmaceuticalId` int(11) DEFAULT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `SSN` varchar(15) DEFAULT NULL,
  `passwordHash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor`
--

INSERT INTO `supervisor` (`supervisorId`, `name`, `pharmaceuticalId`, `emailAddress`, `SSN`, `passwordHash`) VALUES
(1, 'Ali Nadim', 1, 'ad@yahoo.com', '1234589', '$2y$10$DKaPDQUcu1R0rrX/pvqv4OlAgqxdYERo9.QXSoILwUJFtqAWCi98m');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`administratorId`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`contractId`),
  ADD KEY `pharmacyId` (`pharmacyId`),
  ADD KEY `pharmaceuticalId` (`pharmaceuticalId`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doctorId`),
  ADD UNIQUE KEY `SSN` (`SSN`);

--
-- Indexes for table `drug`
--
ALTER TABLE `drug`
  ADD PRIMARY KEY (`drugId`),
  ADD KEY `contractId` (`contractId`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patientId`),
  ADD UNIQUE KEY `SSN` (`SSN`);

--
-- Indexes for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  ADD PRIMARY KEY (`patientDoctorId`),
  ADD KEY `patientId` (`patientId`),
  ADD KEY `doctorId` (`doctorId`);

--
-- Indexes for table `pharmaceutical`
--
ALTER TABLE `pharmaceutical`
  ADD PRIMARY KEY (`pharmaceuticalId`);

--
-- Indexes for table `pharmacist`
--
ALTER TABLE `pharmacist`
  ADD PRIMARY KEY (`pharmacistId`),
  ADD UNIQUE KEY `SSN` (`SSN`),
  ADD KEY `pharmacyId` (`pharmacyId`);

--
-- Indexes for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD PRIMARY KEY (`pharmacyId`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescriptionId`),
  ADD KEY `drugId` (`drugId`),
  ADD KEY `patientDoctorId` (`patientDoctorId`);

--
-- Indexes for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`supervisorId`),
  ADD UNIQUE KEY `SSN` (`SSN`),
  ADD KEY `pharmaceuticalId` (`pharmaceuticalId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `administratorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `contractId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `doctorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drugId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patientId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  MODIFY `patientDoctorId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmaceutical`
--
ALTER TABLE `pharmaceutical`
  MODIFY `pharmaceuticalId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pharmacist`
--
ALTER TABLE `pharmacist`
  MODIFY `pharmacistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `pharmacyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescriptionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor`
--
ALTER TABLE `supervisor`
  MODIFY `supervisorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`pharmacyId`) REFERENCES `pharmacy` (`pharmacyId`),
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`pharmaceuticalId`) REFERENCES `pharmaceutical` (`pharmaceuticalId`);

--
-- Constraints for table `drug`
--
ALTER TABLE `drug`
  ADD CONSTRAINT `drug_ibfk_1` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`);

--
-- Constraints for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  ADD CONSTRAINT `patient_doctor_ibfk_1` FOREIGN KEY (`patientId`) REFERENCES `patient` (`patientId`),
  ADD CONSTRAINT `patient_doctor_ibfk_2` FOREIGN KEY (`doctorId`) REFERENCES `doctor` (`doctorId`);

--
-- Constraints for table `pharmacist`
--
ALTER TABLE `pharmacist`
  ADD CONSTRAINT `pharmacist_ibfk_1` FOREIGN KEY (`pharmacyId`) REFERENCES `pharmacy` (`pharmacyId`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`drugId`) REFERENCES `drug` (`drugId`),
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`patientDoctorId`) REFERENCES `patient_doctor` (`patientDoctorId`);

--
-- Constraints for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD CONSTRAINT `supervisor_ibfk_1` FOREIGN KEY (`pharmaceuticalId`) REFERENCES `pharmaceutical` (`pharmaceuticalId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
