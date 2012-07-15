-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_nlsh_guestbook`
-- 

CREATE TABLE `tl_nlsh_guestbook` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `webadress` varchar(255) NOT NULL default '',
  `date` varchar(255) NOT NULL default '',
  `headline` varchar(255) NOT NULL default '',
  `entry` text NULL,
  `published` char(1) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
