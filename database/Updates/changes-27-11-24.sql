ALTER TABLE `vanda_user` ADD `isresource` INT DEFAULT '0' NOT NULL AFTER `active`;
ALTER TABLE `vanda_user` CHANGE `isresource` `isresource` INT(11) NOT NULL ;