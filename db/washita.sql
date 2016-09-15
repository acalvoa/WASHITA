-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 15-09-2016 a las 15:07:14
-- Versión del servidor: 10.1.17-MariaDB-1~trusty
-- Versión de PHP: 5.6.25-2+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `washita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(124) NOT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `city`
--

INSERT INTO `city` (`ID`, `NAME`, `CREATE_DATE`) VALUES
(1, 'Viña', '2016-09-15 12:17:27'),
(2, 'Santiago', '2016-09-15 12:17:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `city_area`
--

CREATE TABLE IF NOT EXISTS `city_area` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CITY_ID` int(11) NOT NULL,
  `NAME` varchar(124) NOT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `city_area`
--

INSERT INTO `city_area` (`ID`, `CITY_ID`, `NAME`, `CREATE_DATE`) VALUES
(1, 1, 'Reñaca', '2016-09-15 12:17:34'),
(2, 1, 'Plan Viña del Mar', '2016-09-15 12:17:35'),
(3, 1, 'Concón', '2016-09-15 12:17:35'),
(4, 2, 'Providencia', '2016-09-15 12:17:35'),
(5, 2, 'Las Condes', '2016-09-15 12:17:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discount`
--

CREATE TABLE IF NOT EXISTS `discount` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `COUPON` varchar(30) NOT NULL,
  `VALUE` decimal(10,2) NOT NULL,
  `VALID_TILL` datetime NOT NULL,
  `IS_PERCENT` tinyint(1) NOT NULL DEFAULT '1',
  `IS_ONE_TIME` tinyint(1) NOT NULL DEFAULT '0',
  `IS_ONE_TIME_USED` tinyint(1) NOT NULL DEFAULT '0',
  `INFLUENCER_USER_ID` int(11) DEFAULT NULL,
  `MAX_USAGE` int(11) NOT NULL DEFAULT '10000',
  `USED` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `COUPON_INDX` (`COUPON`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `discount`
--

INSERT INTO `discount` (`ID`, `COUPON`, `VALUE`, `VALID_TILL`, `IS_PERCENT`, `IS_ONE_TIME`, `IS_ONE_TIME_USED`, `INFLUENCER_USER_ID`, `MAX_USAGE`, `USED`) VALUES
(1, 'TEST20', '20.00', '2020-02-17 00:00:00', 1, 0, 0, NULL, 10000, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flow_payment`
--

CREATE TABLE IF NOT EXISTS `flow_payment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `FLOW_NUMBER` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `STATUS` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `TRANSACTION_AMOUNT` decimal(10,0) DEFAULT NULL,
  `PAYER_EMAIL` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `DESCRIPTION` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `RESPONSE_TYPE` smallint(3) DEFAULT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `FLOW_PAYMENT_ORDER_NUMBER_INDX` (`ORDER_NUMBER`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `NAME` varchar(256) CHARACTER SET utf8 NOT NULL,
  `ADDRESS` varchar(1024) CHARACTER SET utf8 NOT NULL,
  `EMAIL` varchar(124) DEFAULT NULL,
  `PHONE` varchar(20) DEFAULT NULL,
  `WEIGHT` decimal(6,2) NOT NULL,
  `IS_IRONING` bit(1) DEFAULT NULL,
  `DISCOUNT_COUPON` varchar(30) NOT NULL,
  `PRICE_WITH_DISCOUNT` decimal(10,0) NOT NULL,
  `PRICE_WITHOUT_DISCOUNT` decimal(10,0) NOT NULL,
  `PAYMENT_STATUS` smallint(3) NOT NULL DEFAULT '0',
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PICKUP_FROM` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `PICKUP_TILL` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IS_ONLY_IRONING` bit(1) DEFAULT NULL,
  `DROPOFF_FROM` timestamp NULL DEFAULT NULL,
  `DROPOFF_TILL` timestamp NULL DEFAULT NULL,
  `ACTUAL_WEIGHT` decimal(6,2) DEFAULT NULL,
  `ADDITIONAL_PRICE_WITHOUT_DISCOUNT` decimal(10,0) DEFAULT NULL,
  `ADDITIONAL_PRICE_WITH_DISCOUNT` decimal(10,0) DEFAULT NULL,
  `IS_FEEDBACK_REQUESTED` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is email sent',
  `FEEDBACK_CODE` varchar(40) DEFAULT NULL,
  `CITY_AREA_ID` int(11) DEFAULT NULL,
  `WASH_TYPE` smallint(3) DEFAULT NULL,
  `COMMENT` varchar(3000) DEFAULT NULL,
  `ACTUAL_PRICE_WITH_DISCOUNT` decimal(9,0) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ORDER_NUMBER_INDX` (`ORDER_NUMBER`),
  KEY `ORDER_PICKUP_INDX` (`PICKUP_FROM`,`PICKUP_TILL`),
  KEY `ORDERS_FEEDBACK_CODE_INDX` (`FEEDBACK_CODE`),
  KEY `ORDERS_DISCOUNT_COUPON_AND_EMAIL_INDX` (`DISCOUNT_COUPON`,`EMAIL`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`ID`, `ORDER_NUMBER`, `NAME`, `ADDRESS`, `EMAIL`, `PHONE`, `WEIGHT`, `IS_IRONING`, `DISCOUNT_COUPON`, `PRICE_WITH_DISCOUNT`, `PRICE_WITHOUT_DISCOUNT`, `PAYMENT_STATUS`, `CREATE_DATE`, `PICKUP_FROM`, `PICKUP_TILL`, `IS_ONLY_IRONING`, `DROPOFF_FROM`, `DROPOFF_TILL`, `ACTUAL_WEIGHT`, `ADDITIONAL_PRICE_WITHOUT_DISCOUNT`, `ADDITIONAL_PRICE_WITH_DISCOUNT`, `IS_FEEDBACK_REQUESTED`, `FEEDBACK_CODE`, `CITY_AREA_ID`, `WASH_TYPE`, `COMMENT`, `ACTUAL_PRICE_WITH_DISCOUNT`) VALUES
(1, '21001', 'Angelo Calvo Alfaro', 'Colombia 2055, San Ramon', 'angelo.calvoa@gmail.com', '954081153', '1.00', NULL, '', '1400', '1400', 0, '2016-09-15 13:00:10', '2016-09-15 19:00:00', '2016-09-15 21:00:00', NULL, '2016-09-16 11:00:00', '2016-09-16 13:00:00', NULL, NULL, NULL, 0, NULL, 4, 0, '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_custom_wash_items`
--

CREATE TABLE IF NOT EXISTS `order_custom_wash_items` (
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `WASH_TYPE` tinyint(4) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `COUNT` int(11) NOT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IS_ACTUAL` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_feedback`
--

CREATE TABLE IF NOT EXISTS `order_feedback` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RATING` decimal(10,0) DEFAULT NULL,
  `TEXT` varchar(2000) DEFAULT NULL COMMENT 'User feedback',
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEEDBACK_CODE` varchar(40) DEFAULT NULL,
  `RATING_EASINESS` tinyint(4) DEFAULT NULL,
  `RATING_IRONING` tinyint(4) DEFAULT NULL,
  `RATING_WASHING` tinyint(4) DEFAULT NULL,
  `RATING_RECOMMEND` tinyint(4) DEFAULT NULL,
  `RATING_PICKUP` tinyint(4) DEFAULT NULL,
  `RATING_OVERALL` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ORDER_FEEDBACK_ORDER_NUMBER_INDX` (`ORDER_NUMBER`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_wash_items`
--

CREATE TABLE IF NOT EXISTS `order_wash_items` (
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `WASH_ITEM_ID` int(11) NOT NULL,
  `COUNT` int(11) NOT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IS_ACTUAL` tinyint(1) NOT NULL DEFAULT '0',
  KEY `ORDER_WASH_ITEMS_ORDER_NUMBER_INDX` (`ORDER_NUMBER`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registration_code`
--

CREATE TABLE IF NOT EXISTS `registration_code` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `USER_TYPE` smallint(3) NOT NULL DEFAULT '1',
  `IS_USED` tinyint(1) NOT NULL DEFAULT '0',
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INITIAL_PERSONAL_DISCOUNT` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `REGISTRATION_CODE_INDX` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EMAIL` varchar(124) NOT NULL,
  `NOTIFICATION_EMAIL` varchar(124) NOT NULL,
  `PASSWORD` varchar(512) NOT NULL,
  `NAME` varchar(256) CHARACTER SET utf8 NOT NULL,
  `LASTNAME` varchar(256) CHARACTER SET utf8 NOT NULL,
  `IS_COMPLETE` bit(1) NOT NULL DEFAULT b'0',
  `FIRST_FAILED_LOGIN_TIME` int(10) unsigned NOT NULL DEFAULT '0',
  `FAILED_LOGIN_COUNT` int(10) unsigned NOT NULL DEFAULT '0',
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AUTH_PROVIDER_NAME` varchar(255) DEFAULT NULL COMMENT 'Provider name',
  `AUTH_PROVIDER_UID` varchar(255) DEFAULT NULL COMMENT 'Provider user ID',
  `TEMP_CODE_PASSWORD` varchar(255) DEFAULT NULL COMMENT 'Temporary code to change the password',
  `TEMP_CODE_PASSWORD_VALID_TILL` datetime DEFAULT NULL COMMENT 'Temporary code validity',
  `USER_TYPE` smallint(3) NOT NULL DEFAULT '0',
  `REGISTRATION_CODE` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `PHONE` varchar(20) DEFAULT NULL,
  `ADDRESS` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `CITY_AREA_ID` int(11) DEFAULT NULL,
  `PERSONAL_DISCOUNT_AMOUNT` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `USERS_AUTH_PROVIDER_UNIQUE_INDX` (`AUTH_PROVIDER_NAME`,`AUTH_PROVIDER_UID`),
  KEY `USERS_EMAIL_INDX` (`EMAIL`),
  KEY `USERS_AUTH_PROVIDER_UID_INDX` (`AUTH_PROVIDER_UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`ID`, `EMAIL`, `NOTIFICATION_EMAIL`, `PASSWORD`, `NAME`, `LASTNAME`, `IS_COMPLETE`, `FIRST_FAILED_LOGIN_TIME`, `FAILED_LOGIN_COUNT`, `CREATE_DATE`, `AUTH_PROVIDER_NAME`, `AUTH_PROVIDER_UID`, `TEMP_CODE_PASSWORD`, `TEMP_CODE_PASSWORD_VALID_TILL`, `USER_TYPE`, `REGISTRATION_CODE`, `PHONE`, `ADDRESS`, `CITY_AREA_ID`, `PERSONAL_DISCOUNT_AMOUNT`) VALUES
(1, 'angelo.calvoa@gmail.com', 'angelo.calvoa@gmail.com', '$2a$08$lnrGnmt4qIAGUVUFtwjHIus4l4xED3Zjacxb91xfXXUmX0lv6Aajy', 'Angelo', 'Calvo Alfaro', b'1', 0, 0, '2016-09-15 12:58:53', NULL, NULL, NULL, NULL, 0, NULL, '954081153', 'Colombia 2055, San Ramon', 4, '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wash_item`
--

CREATE TABLE IF NOT EXISTS `wash_item` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(256) CHARACTER SET utf8 NOT NULL,
  `ITEM_WEIGHT` decimal(3,2) NOT NULL COMMENT 'In kilos',
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IMAGE_FILE_NAME` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `ITEM_DRY_CLEAN_PRICE` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ITEM_SPECIAL_CLEAN_PRICE` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
