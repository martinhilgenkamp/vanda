-- Transportknop activeren en wagen definieren in suppliers.
UPDATE `vanda_suppliers` SET `supplier_desc` = 'Condor Grass', `transportmail` = '1', `transporttype` = 'oplegger' WHERE `vanda_suppliers`.`id` = 8;

-- Optietabel aanpassen voor de dynamische mail functionaliteit.
ALTER TABLE `vanda_options` ADD `TransportEmailAddress` VARCHAR(250) NOT NULL AFTER `maat3y`;
ALTER TABLE `vanda_options` ADD `TransportName` VARCHAR(250) NOT NULL AFTER `TransportEmailAddress`;
ALTER TABLE `vanda_options` ADD `TransportFromEmailAddress` VARCHAR(250) NOT NULL AFTER `TransportName`;
ALTER TABLE `vanda_options` ADD `TransportFromName` VARCHAR(250) NOT NULL AFTER `TransportFromEmailAddress`;

-- Standaard waarde voor de nieuwe opties toevoegen.
UPDATE `vanda_options` SET `TransportEmailAddress` = 'expeditie@verhoek-europe.com', `TransportName` = 'Verhoek Expeditie' WHERE `vanda_options`.`id` = 1;
UPDATE `vanda_options` SET `TransportFromEmailAddress` = 'magazijn@vandacarpets.nl', `TransportFromName` = 'Vanda Carpets' WHERE `vanda_options`.`id` = 1;

-- Machine optie toevoegen\
ALTER TABLE `vanda_options` ADD `MachineCount` INT(2) NOT NULL AFTER `TransportFromName`;