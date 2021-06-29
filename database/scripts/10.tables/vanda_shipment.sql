CREATE TABLE IF NOT EXISTS `vanda_shipment` (
  `ship_id` int(11) NOT NULL AUTO_INCREMENT,
  `klant` varchar(250) NOT NULL,
  `datum` datetime NOT NULL,
  `verzonden` int(11) NOT NULL,
  PRIMARY KEY (`ship_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1360 DEFAULT CHARSET=latin1;
