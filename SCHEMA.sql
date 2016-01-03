/* MySQL schema for colloq */

SET NAMES utf8;

CREATE TABLE `activity` (
  `email` varchar(30) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` text,
  PRIMARY KEY (`email`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ballots` (
  `voter` varchar(30) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nom1` int(11) NOT NULL,
  `nom2` int(11) NOT NULL,
  `nom3` int(11) NOT NULL,
  `nom4` int(11) NOT NULL,
  `nom5` int(11) NOT NULL,
  PRIMARY KEY (`voter`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `nom1` (`nom1`),
  KEY `nom2` (`nom2`),
  KEY `nom3` (`nom3`),
  KEY `nom4` (`nom4`),
  KEY `nom5` (`nom5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `nomid` int(11) NOT NULL,
  `issuer` varchar(30) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content` text NOT NULL,
  `test` int(11) NOT NULL,
  PRIMARY KEY (`when`),
  KEY `nomid` (`nomid`),
  KEY `issuer` (`issuer`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nominees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(30) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `affiliation` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  `nominator` varchar(30) NOT NULL,
  `syntax` tinyint(1) NOT NULL,
  `semantics` tinyint(1) NOT NULL,
  `phonology` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fullname_uniqueness` (`lastname`,`firstname`),
  KEY `lastname` (`lastname`),
  KEY `firstname` (`firstname`),
  KEY `affiliation` (`affiliation`),
  KEY `nominator` (`nominator`),
  KEY `syntax` (`syntax`),
  KEY `semantics` (`semantics`),
  KEY `phonology` (`phonology`),
  KEY `website` (`website`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `recent` (
  `lastname` varchar(30) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `year` int(11) NOT NULL,
  `term` enum('spring','fall') NOT NULL DEFAULT 'fall',
  PRIMARY KEY (`lastname`,`firstname`,`year`),
  KEY `year` (`year`),
  KEY `term` (`term`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  PRIMARY KEY (`id`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
