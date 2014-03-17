-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 17, 2014 at 07:51 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cyoaapp`
--
CREATE DATABASE IF NOT EXISTS `cyoaapp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `cyoaapp`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `authorID` int(11) NOT NULL,
  `title` varchar(2555) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`ID`, `authorID`, `title`) VALUES
(1, 42, 'title');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `choiceDialog` varchar(255) NOT NULL,
  `parentID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `booksID` int(11) NOT NULL COMMENT 'FK of books',
  `jsonID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `book-jsonID` (`booksID`,`jsonID`) COMMENT 'One jsonID per Book'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`ID`, `choiceDialog`, `parentID`, `title`, `content`, `booksID`, `jsonID`) VALUES
(1, 'place holder', 0, 'Sleepy', 'Seth is sleepy. Should he sleep?', 1, 1),
(2, 'place holder', 1, 'Yes', 'Seth falls asleep wonderfully. Excellent choice!', 1, 2),
(3, 'place holder', 1, 'No', 'Then, what should he do?', 1, 3),
(4, 'place holder', 3, 'Practice Karate', 'Seth masters the art of karate chops. He is ready to fight lord duku!', 1, 4),
(5, 'place holder', 3, 'Watch TV', 'The TV is broken ...', 1, 5),
(6, 'place holder', 3, 'Code A website', 'He already is ... ;)', 1, 6),
(7, 'place holder', 5, 'Fix the TV', 'He blows up, Seth dies. Or ... is he sleeping? He hopes he''s sleeping.', 1, 7),
(8, 'place holder', 5, 'Hit the TV', 'NO! ITs exepnsive! OMGS!\n\nNo, seriously though, go back, pick a different option. This TV is too nice to hit.', 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `actualName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `password`, `username`, `actualName`, `email`) VALUES
(1, 'test', 'test', 'test', ''),
(2, 'test5', 'test2', 'test3', ''),
(3, '3da541559918a808c2402bba5012f6c60b27661c', 'seth', 'Seth Gossler', ''),
(4, 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'pooz', 'Seth G 2 ', 'sethgossler@gmail.com'),
(5, 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'poozegeg', 'asdg', 'sethgossler@gmail.com'),
(6, 'f99fd8ca7c2d428e54f4c7f0711f135b422e8207', 'asd@asd.asd', 'seth asdf', 'asdg'),
(7, '86bd56853e1c534e66fc28eaf22d8a9df41b79d3', 'asdf@hehe.asd', 'asdh', 'ehehweh'),
(8, '99f7c2f7fc7199dc26a203ebea3b892e8a505f0e', 'SethG', 'Seth Gossler', 'sethgossler@gmail.com'),
(9, 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
