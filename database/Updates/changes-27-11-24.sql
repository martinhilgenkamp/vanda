ALTER TABLE `vanda_user` ADD `isresource` INT DEFAULT '0' NOT NULL AFTER `active`;
ALTER TABLE `vanda_user` CHANGE `isresource` `isresource` INT(11) NOT NULL ;
ALTER TABLE `vanda_work_orders` CHANGE `resource1` `resource1` INT(11) NULL DEFAULT NULL, CHANGE `resource2` `resource2` INT(11) NULL DEFAULT NULL; 