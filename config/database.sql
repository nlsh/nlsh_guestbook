-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

--
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
  `com_nlsh_gb_template` varchar(32) NOT NULL default ''
  `com_nlsh_gb_bolMail` char(1) NOT NULL default ''
  `com_nlsh_gb_email` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;