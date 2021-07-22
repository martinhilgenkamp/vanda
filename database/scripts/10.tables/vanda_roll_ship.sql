CREATE TABLE IF NOT EXISTS `vanda_roll_ship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `klant` varchar(250) DEFAULT NULL,
  `datum` datetime NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=latin1;