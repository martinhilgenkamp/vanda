CREATE TABLE IF NOT EXISTS `vanda_rolls` (
  `rollid` int(10) NOT NULL AUTO_INCREMENT,
  `rolnummer` varchar(15) NOT NULL,
  `deelnummer` int(15) NOT NULL,
  `snijlengte` float NOT NULL,
  `snijbreedte` float NOT NULL,
  `ean` varchar(50) NOT NULL,
  `omschrijving` varchar(250) NOT NULL,
  `kleur` varchar(250) NOT NULL,
  `backing` varchar(250) NOT NULL,
  `referentie` varchar(250) NOT NULL,
  `ingevoerd` datetime NOT NULL,
  `gewijzigd` datetime NOT NULL,
  `verzonden` int(1) NOT NULL,
  `verwijderd` int(1) NOT NULL,
  PRIMARY KEY (`rollid`)
) ENGINE=InnoDB AUTO_INCREMENT=17032 DEFAULT CHARSET=latin1;