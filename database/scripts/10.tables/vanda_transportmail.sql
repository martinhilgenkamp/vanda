CREATE TABLE IF NOT EXISTS `vanda_transportmail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `to` varchar(250) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `verstuurd` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1800 DEFAULT CHARSET=latin1;