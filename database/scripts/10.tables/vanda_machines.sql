CREATE TABLE IF NOT EXISTS `vanda_machines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon` varchar(250) NOT NULL,
  `kwaliteit` varchar(250) NOT NULL,
  `machine` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `verwijderd` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85834 DEFAULT CHARSET=utf8mb4;