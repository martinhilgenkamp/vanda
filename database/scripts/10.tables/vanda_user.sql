CREATE TABLE IF NOT EXISTS `vanda_user` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `level` int(1) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `isresource` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `vanda_user`
  ADD PRIMARY KEY (`id`);
ENT voor een tabel `vanda_user`
--
ALTER TABLE `vanda_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;