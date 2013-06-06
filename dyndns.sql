CREATE TABLE IF NOT EXISTS `domains` (
  `id` bigint(20) NOT NULL auto_increment,
  `hostname` text character set utf8 NOT NULL,
  `ip` varchar(50) character set utf8 NOT NULL,
  `updateSource` varchar(50) character set utf8 NOT NULL,
  `updateTime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;