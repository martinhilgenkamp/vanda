CREATE TABLE IF NOT EXISTS `vanda_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bedrijfskenmerk` varchar(50) NOT NULL,
  `ponummer` varchar(50) NOT NULL,
  `maat1x` float NOT NULL,
  `maat1y` float NOT NULL,
  `maat2x` float NOT NULL,
  `maat2y` float NOT NULL,
  `maat3x` float NOT NULL,
  `maat3y` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;