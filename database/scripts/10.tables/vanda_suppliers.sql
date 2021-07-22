CREATE TABLE IF NOT EXISTS `vanda_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_desc` varchar(250) NOT NULL,
  `transportmail` int(1) NOT NULL,
  `transporttype` varchar(250) NOT NULL,
  `zichtbaar` int(1) NOT NULL,
  `volgorde` int(11) NOT NULL,
  `verwijderd` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;