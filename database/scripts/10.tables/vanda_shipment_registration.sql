CREATE TABLE IF NOT EXISTS `vanda_shipment_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_id` varchar(30) NOT NULL,
  `klant` varchar(250) NOT NULL,
  `barcode` varchar(20) NOT NULL,
  `tijd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tijd` (`tijd`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;