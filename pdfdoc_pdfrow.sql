CREATE TABLE IF NOT EXISTS `pdfdoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `disabled` (`disabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `pdfrow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pdfdoc_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `value` text,
  `url` varchar(500) DEFAULT NULL,
  `align` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `bold` tinyint(1) DEFAULT NULL,
  `italic` tinyint(1) DEFAULT NULL,
  `underline` tinyint(1) DEFAULT NULL,
  `height` varchar(50) DEFAULT NULL,
  `width` varchar(50) DEFAULT NULL,
  `seq` int(11) DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pdfdoc_id` (`pdfdoc_id`),
  KEY `type` (`type`),
  KEY `disabled` (`disabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;