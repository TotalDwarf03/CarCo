-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2023 at 10:52 PM
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
-- Database: `carco`
--
CREATE DATABASE IF NOT EXISTS `carco` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `carco`;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomer`
--

DROP TABLE IF EXISTS `tblcustomer`;
CREATE TABLE `tblcustomer` (
  `CustomerID` int(11) NOT NULL,
  `CustomerName` varchar(50) NOT NULL,
  `Image` varchar(50) DEFAULT NULL,
  `AddressLine1` varchar(30) NOT NULL,
  `AddressLine2` varchar(30) NOT NULL,
  `AddressLine3` varchar(30) NOT NULL,
  `Postcode` varchar(8) NOT NULL,
  `Telephone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblcustomer`
--

TRUNCATE TABLE `tblcustomer`;
--
-- Dumping data for table `tblcustomer`
--

INSERT INTO `tblcustomer` (`CustomerID`, `CustomerName`, `Image`, `AddressLine1`, `AddressLine2`, `AddressLine3`, `Postcode`, `Telephone`) VALUES
(1, 'Bettws Service Station', 'Images/Customers/1.jpg', 'Heol Dewi Sant', 'Bettws', 'Bridgend', 'CF32 8TA', '01656 722440'),
(2, 'BCP Bridgend', 'Images/Customers/2.jpg', '5 Kestrel Cl', 'Bridgend Industrial Estate', 'Bridgend', 'CF31 3RW', '01656 674011');

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomerlogin`
--

DROP TABLE IF EXISTS `tblcustomerlogin`;
CREATE TABLE `tblcustomerlogin` (
  `CustomerLoginID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `Forename` varchar(20) NOT NULL,
  `Surname` varchar(20) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblcustomerlogin`
--

TRUNCATE TABLE `tblcustomerlogin`;
--
-- Dumping data for table `tblcustomerlogin`
--

INSERT INTO `tblcustomerlogin` (`CustomerLoginID`, `CustomerID`, `Forename`, `Surname`, `Username`, `Password`) VALUES
(1, 1, 'Steve', 'Daniel', 'BSSSteve', '$2y$10$yfGAUuS8TnkSuMys9PpRqO1bOOJMdczmdB3J6fcSzHBs.mBk4MRBi'),
(3, 1, 'Ceri', 'Ryall', 'BSSCeri', '$2y$10$sDqjQDAg7KGoHUFshcEF5egRvebvesod2yznqvzw7kjla8DVHxG0C'),
(4, 2, 'Chris', 'Jury', 'BCPChris', '$2y$10$xaHMhlzoamYipfjfG4x3felIKxjiayNVwOlz5jerpAFmnRYFDjbY6');

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

DROP TABLE IF EXISTS `tblorder`;
CREATE TABLE `tblorder` (
  `OrderID` int(11) NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp(),
  `CustomerID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `TotalCost` float(7,2) NOT NULL DEFAULT 0.00,
  `DeliveryDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblorder`
--

TRUNCATE TABLE `tblorder`;
--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`OrderID`, `CreationDate`, `CustomerID`, `StaffID`, `TotalCost`, `DeliveryDate`) VALUES
(1, '2023-11-27 15:46:41', 1, 1, 69.98, '2023-12-10'),
(30, '2023-11-30 21:03:53', 2, 1, 60.81, '2023-12-16');

-- --------------------------------------------------------

--
-- Table structure for table `tblorderproducts`
--

DROP TABLE IF EXISTS `tblorderproducts`;
CREATE TABLE `tblorderproducts` (
  `OrderProductID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `SystemProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Subtotal` float(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblorderproducts`
--

TRUNCATE TABLE `tblorderproducts`;
--
-- Dumping data for table `tblorderproducts`
--

INSERT INTO `tblorderproducts` (`OrderProductID`, `OrderID`, `SystemProductID`, `Quantity`, `Subtotal`) VALUES
(1, 1, 4, 2, 69.98),
(47, 30, 2, 1, 30.00),
(48, 30, 1, 1, 13.00),
(49, 30, 3, 1, 17.81);

-- --------------------------------------------------------

--
-- Table structure for table `tblpermissions`
--

DROP TABLE IF EXISTS `tblpermissions`;
CREATE TABLE `tblpermissions` (
  `PermissionID` int(11) NOT NULL,
  `PermissionName` varchar(20) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblpermissions`
--

TRUNCATE TABLE `tblpermissions`;
--
-- Dumping data for table `tblpermissions`
--

INSERT INTO `tblpermissions` (`PermissionID`, `PermissionName`, `Description`) VALUES
(2, 'User Manager', 'Can Insert, Edit or Delete Users'),
(3, 'Product Manager', 'Can Insert, Edit or Delete Products'),
(4, 'Customer Manager', 'Can Insert, Edit or Delete Customers'),
(5, 'Order Manager', 'Can Insert, Edit or Delete Orders');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

DROP TABLE IF EXISTS `tblstaff`;
CREATE TABLE `tblstaff` (
  `StaffID` int(11) NOT NULL,
  `Forename` varchar(20) NOT NULL,
  `Surname` varchar(20) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `Image` varchar(50) DEFAULT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblstaff`
--

TRUNCATE TABLE `tblstaff`;
--
-- Dumping data for table `tblstaff`
--

INSERT INTO `tblstaff` (`StaffID`, `Forename`, `Surname`, `Email`, `Image`, `Username`, `Password`) VALUES
(1, 'Kieran', 'Pritchard', '23036958@students.southwales.ac.uk', 'Images/Staff/1.webp', 'Admin', '$2y$10$hHYyt/td6WQ/Ube1mNRjHugPCKJrjq1QZWQ1bIsntJU3EIiq5Oue.'),
(2, 'Jane', 'Doe', 'Jane@CarCo.com', 'Images/Staff/2.avif', 'OrderManager', '$2y$10$d8KgsRKkQB9al/RGLHoQ2eXeSgY7P1kt587MrS.fA0aWm722un.BG'),
(3, 'Joe', 'Bloggs', 'Joe@CarCo.com', 'Images/Staff/3.avif', 'CustomerManager', '$2y$10$in6Wx.WxayeeatzbAxs4lutH4KehZGI7gb5iGh3EZ.IS.o8MBsPiK'),
(4, 'Jim', 'Toast', 'Jim@CarCo.com', 'Images/Staff/4.jpg', 'ProductManager', '$2y$10$DiozufZ3TbG1T1Gc.0EMgOxQTwAzu./8uM2VyJsbRfSiWSaYofwHW'),
(5, 'Bob', 'Smiles', 'Bob@CarCo.com', 'Images/Staff/5.png', 'UserManager', '$2y$10$eIC9BlU4fn45qzUgL8uUXOlM76HW86omGSjqa6ihwcH1m4i7DGMJK');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaffpermissions`
--

DROP TABLE IF EXISTS `tblstaffpermissions`;
CREATE TABLE `tblstaffpermissions` (
  `StaffPermissionID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `PermissionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblstaffpermissions`
--

TRUNCATE TABLE `tblstaffpermissions`;
--
-- Dumping data for table `tblstaffpermissions`
--

INSERT INTO `tblstaffpermissions` (`StaffPermissionID`, `StaffID`, `PermissionID`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 1, 4),
(4, 1, 5),
(5, 2, 5),
(7, 4, 3),
(8, 5, 2),
(21, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tblsystemproduct`
--

DROP TABLE IF EXISTS `tblsystemproduct`;
CREATE TABLE `tblsystemproduct` (
  `SystemProductID` int(11) NOT NULL,
  `SystemProductStatusID` int(11) NOT NULL,
  `ProductName` varchar(40) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Image` varchar(50) DEFAULT NULL,
  `CostPerUnit` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblsystemproduct`
--

TRUNCATE TABLE `tblsystemproduct`;
--
-- Dumping data for table `tblsystemproduct`
--

INSERT INTO `tblsystemproduct` (`SystemProductID`, `SystemProductStatusID`, `ProductName`, `Description`, `Image`, `CostPerUnit`) VALUES
(1, 1, 'Oil Filter', 'An Oil Filter. Produced by K&N.', 'Images/Products/1.jpg', 13.00),
(2, 1, 'Air Filter', 'An Air Filter. Produced by RamAir.', 'Images/Products/2.jpg', 30.00),
(3, 1, 'Fuel Filter', 'A Fuel Filter. Produced by Sytec.', 'Images/Products/3.jpg', 17.81),
(4, 1, '5W-30 Oil', '4 litres of Castrol 5W-30 Oil.', 'Images/Products/4.jpg', 34.99),
(5, 1, '5W-40 Oil', '4 Litres of Castrol 5W-40 Oil.', 'Images/Products/5.jpg', 49.99),
(6, 1, 'CarPlan AntiFreeze', '5 litres of Coolant/Anti-Freeze. Produced by Car Plan.', 'Images/Products/6.jpg', 22.99),
(7, 1, 'Halfords AntiFreeze', '5 litres of Anti-Freeze. Produced by Halfords.', 'Images/Products/7.webp', 14.99),
(8, 1, 'AE01-D', 'AE01-D Tyre by AeroTyre.', 'Images/Products/8.png', 49.99),
(9, 1, 'AE01-S', 'AE01-S Tyre by AeroTyre', 'Images/Products/9.png', 65.00),
(10, 1, 'AE01-T', 'AE01-T Tyre by AeroTyre', 'Images/Products/10.png', 79.99),
(11, 1, 'Valeo Wipers', 'Front wiper blades produced by Valeo.', 'Images/Products/11.jpg', 23.73),
(12, 1, 'Bosch Wipers', 'Front wiper blades produced by Bosch', 'Images/Products/12.jpg', 30.86),
(27, 2, 'Toyo Proxes', 'Tyres made by Toyo. Top of the range Tyres.', 'Images/Products/27.webp', 99.99);

-- --------------------------------------------------------

--
-- Table structure for table `tblsystemproductstatus`
--

DROP TABLE IF EXISTS `tblsystemproductstatus`;
CREATE TABLE `tblsystemproductstatus` (
  `ProductStatusID` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblsystemproductstatus`
--

TRUNCATE TABLE `tblsystemproductstatus`;
--
-- Dumping data for table `tblsystemproductstatus`
--

INSERT INTO `tblsystemproductstatus` (`ProductStatusID`, `Status`) VALUES
(1, 'In Stock'),
(2, 'Out of Stock');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblcustomer`
--
ALTER TABLE `tblcustomer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `tblcustomerlogin`
--
ALTER TABLE `tblcustomerlogin`
  ADD PRIMARY KEY (`CustomerLoginID`),
  ADD KEY `CustomerID` (`CustomerID`);

--
-- Indexes for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `tblorderproducts`
--
ALTER TABLE `tblorderproducts`
  ADD PRIMARY KEY (`OrderProductID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `SystemProductID` (`SystemProductID`);

--
-- Indexes for table `tblpermissions`
--
ALTER TABLE `tblpermissions`
  ADD PRIMARY KEY (`PermissionID`);

--
-- Indexes for table `tblstaff`
--
ALTER TABLE `tblstaff`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `tblstaffpermissions`
--
ALTER TABLE `tblstaffpermissions`
  ADD PRIMARY KEY (`StaffPermissionID`),
  ADD KEY `StaffID` (`StaffID`),
  ADD KEY `PermissionID` (`PermissionID`);

--
-- Indexes for table `tblsystemproduct`
--
ALTER TABLE `tblsystemproduct`
  ADD PRIMARY KEY (`SystemProductID`),
  ADD KEY `SystemProductStatusID` (`SystemProductStatusID`);

--
-- Indexes for table `tblsystemproductstatus`
--
ALTER TABLE `tblsystemproductstatus`
  ADD PRIMARY KEY (`ProductStatusID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblcustomer`
--
ALTER TABLE `tblcustomer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblcustomerlogin`
--
ALTER TABLE `tblcustomerlogin`
  MODIFY `CustomerLoginID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tblorderproducts`
--
ALTER TABLE `tblorderproducts`
  MODIFY `OrderProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tblpermissions`
--
ALTER TABLE `tblpermissions`
  MODIFY `PermissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblstaff`
--
ALTER TABLE `tblstaff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tblstaffpermissions`
--
ALTER TABLE `tblstaffpermissions`
  MODIFY `StaffPermissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblsystemproduct`
--
ALTER TABLE `tblsystemproduct`
  MODIFY `SystemProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tblsystemproductstatus`
--
ALTER TABLE `tblsystemproductstatus`
  MODIFY `ProductStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcustomerlogin`
--
ALTER TABLE `tblcustomerlogin`
  ADD CONSTRAINT `tblcustomerlogin_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `tblcustomer` (`CustomerID`);

--
-- Constraints for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD CONSTRAINT `tblorder_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `tblcustomer` (`CustomerID`),
  ADD CONSTRAINT `tblorder_ibfk_2` FOREIGN KEY (`StaffID`) REFERENCES `tblstaff` (`StaffID`);

--
-- Constraints for table `tblorderproducts`
--
ALTER TABLE `tblorderproducts`
  ADD CONSTRAINT `tblorderproducts_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `tblorder` (`OrderID`),
  ADD CONSTRAINT `tblorderproducts_ibfk_2` FOREIGN KEY (`SystemProductID`) REFERENCES `tblsystemproduct` (`SystemProductID`);

--
-- Constraints for table `tblstaffpermissions`
--
ALTER TABLE `tblstaffpermissions`
  ADD CONSTRAINT `tblstaffpermissions_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `tblstaff` (`StaffID`),
  ADD CONSTRAINT `tblstaffpermissions_ibfk_2` FOREIGN KEY (`PermissionID`) REFERENCES `tblpermissions` (`PermissionID`);

--
-- Constraints for table `tblsystemproduct`
--
ALTER TABLE `tblsystemproduct`
  ADD CONSTRAINT `tblsystemproduct_ibfk_2` FOREIGN KEY (`SystemProductStatusID`) REFERENCES `tblsystemproductstatus` (`ProductStatusID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
