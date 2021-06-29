CREATE TABLE IF NOT EXISTS `vanda_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_no` varchar(50) NOT NULL,
  `article_desc` varchar(250) NOT NULL,
  `default_amount` int(11) NOT NULL,
  `export` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;