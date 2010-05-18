-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.77

--
-- Create schema userstory
--

CREATE DATABASE IF NOT EXISTS userstory;
USE userstory;

--
-- Definition of table `stories`
--

DROP TABLE IF EXISTS `stories`;
CREATE TABLE `stories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `themeId` int(10) unsigned NOT NULL,
  `asA` varchar(255) NOT NULL,
  `iNeed` text NOT NULL,
  `soThat` text NOT NULL,
  `acceptanceCriteria` text NOT NULL,
  `config` text NOT NULL,
  `estimate` int(10) unsigned NOT NULL default '0',
  `nickname` varchar(255) NOT NULL,
  `done` int(10) unsigned NOT NULL default '0',
  `priorityOrder` int(10) unsigned NOT NULL default '0',
  `deleted` int(10) unsigned NOT NULL default '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `remaining` int(10) NOT NULL default '0',
  `historicalRemaining` text,
  `criticalPath` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=latin1;


--
-- Definition of table `themes`
--

DROP TABLE IF EXISTS `themes`;
CREATE TABLE `themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `themeName` varchar(255) NOT NULL,
  `priorityOrder` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `themes`
--

