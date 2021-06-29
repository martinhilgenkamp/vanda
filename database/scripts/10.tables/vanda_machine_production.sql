CREATE TABLE IF NOT EXISTS `vanda_machine_production` (
  `machine` int(11) NOT NULL,
  `kwaliteit` varchar(250) NOT NULL,
  `operator` varchar(250) NOT NULL,
  `datum` datetime NOT NULL,
  `verwijderd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;