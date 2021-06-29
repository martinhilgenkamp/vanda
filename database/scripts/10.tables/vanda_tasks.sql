CREATE TABLE IF NOT EXISTS `vanda_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `adres` text NOT NULL,
  `date` date NOT NULL,
  `filename` varchar(250) NOT NULL,
  `complete` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3151 DEFAULT CHARSET=latin1;