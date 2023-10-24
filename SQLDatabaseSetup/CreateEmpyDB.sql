-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2023 at 02:54 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `carco`
--
CREATE DATABASE IF NOT EXISTS `carco` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `carco`;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomer`
--

CREATE TABLE `tblcustomer` (
  `CustomerID` int(11) NOT NULL,
  `Forename` varchar(20) DEFAULT NULL,
  `Surname` varchar(20) DEFAULT NULL,
  `BusinessName` varchar(50) DEFAULT NULL,
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
-- --------------------------------------------------------

--
-- Table structure for table `tblcustomerlogin`
--

CREATE TABLE `tblcustomerlogin` (
  `CustomerLoginID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblcustomerlogin`
--

TRUNCATE TABLE `tblcustomerlogin`;
-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

CREATE TABLE `tblorder` (
  `OrderID` int(11) NOT NULL,
  `CreationDate` datetime NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `TotalCost` float(7,2) NOT NULL,
  `DeliveryDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblorder`
--

TRUNCATE TABLE `tblorder`;
-- --------------------------------------------------------

--
-- Table structure for table `tblorderproducts`
--

CREATE TABLE `tblorderproducts` (
  `OrderProductID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `SystemProductID` int(11) NOT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Subtotal` float(7,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblorderproducts`
--

TRUNCATE TABLE `tblorderproducts`;
-- --------------------------------------------------------

--
-- Table structure for table `tblpermissions`
--

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
(1, 'Admin', 'Full System Access'),
(2, 'User Manager', 'Can Insert, Edit or Delete Users'),
(3, 'Product Manager', 'Can Insert, Edit or Delete Products'),
(4, 'Customer Manager', 'Can Insert, Edit or Delete Customers'),
(5, 'Order Manager', 'Can Insert, Edit or Delete Orders');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

CREATE TABLE `tblstaff` (
  `StaffID` int(11) NOT NULL,
  `Forename` varchar(20) NOT NULL,
  `Surname` varchar(20) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblstaff`
--

TRUNCATE TABLE `tblstaff`;
-- --------------------------------------------------------

--
-- Table structure for table `tblstaffpermissions`
--

CREATE TABLE `tblstaffpermissions` (
  `StaffPermissionID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `PermissionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblstaffpermissions`
--

TRUNCATE TABLE `tblstaffpermissions`;
-- --------------------------------------------------------

--
-- Table structure for table `tblsystemproduct`
--

CREATE TABLE `tblsystemproduct` (
  `SystemProductID` int(11) NOT NULL,
  `SystemProductTypeID` int(11) NOT NULL,
  `ProductName` varchar(40) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `CostPerUnit` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblsystemproduct`
--

TRUNCATE TABLE `tblsystemproduct`;
-- --------------------------------------------------------

--
-- Table structure for table `tblsystemproducttype`
--

CREATE TABLE `tblsystemproducttype` (
  `SystemProductTypeID` int(11) NOT NULL,
  `Type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `tblsystemproducttype`
--

TRUNCATE TABLE `tblsystemproducttype`;
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
  ADD KEY `SystemProductTypeID` (`SystemProductTypeID`);

--
-- Indexes for table `tblsystemproducttype`
--
ALTER TABLE `tblsystemproducttype`
  ADD PRIMARY KEY (`SystemProductTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblcustomer`
--
ALTER TABLE `tblcustomer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcustomerlogin`
--
ALTER TABLE `tblcustomerlogin`
  MODIFY `CustomerLoginID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblorderproducts`
--
ALTER TABLE `tblorderproducts`
  MODIFY `OrderProductID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblpermissions`
--
ALTER TABLE `tblpermissions`
  MODIFY `PermissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblstaff`
--
ALTER TABLE `tblstaff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblstaffpermissions`
--
ALTER TABLE `tblstaffpermissions`
  MODIFY `StaffPermissionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsystemproduct`
--
ALTER TABLE `tblsystemproduct`
  MODIFY `SystemProductID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsystemproducttype`
--
ALTER TABLE `tblsystemproducttype`
  MODIFY `SystemProductTypeID` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `tblsystemproduct_ibfk_1` FOREIGN KEY (`SystemProductTypeID`) REFERENCES `tblsystemproducttype` (`SystemProductTypeID`);
COMMIT;
