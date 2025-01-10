ALTER TABLE vanda_work_orders DROP COLUMN opdrachtnr;
ALTER TABLE vanda_work_orders ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'New';
ALTER TABLE `vanda_work_orders` DROP `resource2` ;
ALTER TABLE `vanda_work_orders` DROP `resource1` ;
ALTER TABLE `vanda_work_orders` ADD `resources` JSON NOT NULL AFTER `end`;