CREATE TABLE IF NOT EXISTS `vanda_production` (
  `removed` int(1) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikelnummer` varchar(50) NOT NULL,
  `kwaliteit` varchar(50) NOT NULL,
  `gewicht` float NOT NULL,
  `ordernr` varchar(50) NOT NULL,
  `datum` datetime NOT NULL,
  `geleverd` datetime NOT NULL,
  `shipping_id` varchar(30) NOT NULL,
  `barcode` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=36806 DEFAULT CHARSET=latin1;