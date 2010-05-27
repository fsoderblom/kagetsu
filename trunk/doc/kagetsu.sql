-- MySQL dump 10.11
--
-- Host: localhost    Database: kagetsu
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny2+spu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `event` (
  `id` int(10) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `log` (
  `id` int(10) NOT NULL auto_increment,
  `ip` varchar(64) NOT NULL default '',
  `object` int(10) NOT NULL default '0',
  `network` varchar(64) NOT NULL default '',
  `datum` datetime NOT NULL default '0000-00-00 00:00:00',
  `event` int(10) NOT NULL default '0',
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `object` (`object`,`ip`,`network`,`datum`,`event`,`comment`)
) ENGINE=MyISAM AUTO_INCREMENT=27587 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `object`
--

DROP TABLE IF EXISTS `object`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `object` (
  `id` int(10) NOT NULL auto_increment,
  `parent` int(10) NOT NULL default '0',
  `status` smallint(10) default '0',
  `statuschanged` datetime default NULL,
  `ip` varchar(64) NOT NULL default '',
  `name` varchar(128) default NULL,
  `type` int(10) default NULL,
  `office` int(10) default NULL,
  `vrf` int(10) default NULL,
  `alert` int(10) default NULL,
  `accesstime` float(6,5) default NULL,
  `successful` int(11) default '0',
  `failed` int(11) default '0',
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`,`status`,`statuschanged`,`ip`,`name`,`type`,`office`,`vrf`,`alert`,`accesstime`,`successful`,`failed`)
) ENGINE=MyISAM AUTO_INCREMENT=854 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `office`
--

DROP TABLE IF EXISTS `office`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `office` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `subnet`
--

DROP TABLE IF EXISTS `subnet`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `subnet` (
  `id` int(10) NOT NULL auto_increment,
  `network` varchar(64) NOT NULL default '',
  `prefix` varchar(10) NOT NULL default '32',
  `office` int(10) default NULL,
  `start` time default NULL,
  `end` time default NULL,
  PRIMARY KEY  (`id`),
  KEY `network` (`network`,`prefix`,`office`,`start`,`end`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `type` (
  `id` int(10) NOT NULL auto_increment,
  `type` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vrf`
--

DROP TABLE IF EXISTS `vrf`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vrf` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-27 14:49:51
