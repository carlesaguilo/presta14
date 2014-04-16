CREATE TABLE IF NOT EXISTS `PREFIX_megacustomers_groups`(
			`id_rule` INT(10) unsigned NOT NULL auto_increment,
			`field` varchar(256) NOT NULL,
			`reference` varchar(256) NOT NULL,
			`operator` varchar(32) NOT NULL,
			`id_group` int(10) NOT NULL default '0',
			PRIMARY KEY (`id_rule`)
			) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_megacustomers_taxes`(
			`id_tax_group` INT(10) unsigned NOT NULL auto_increment,
			`id_group` int(10) NOT NULL default '0',
			`id_tax_rules_group` int(10) NOT NULL default '0',
			`id_tax` int(10) NOT NULL default '0',
			PRIMARY KEY (`id_tax_group`)
			) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_megacustomers_customers` (
			`id_megacustomer` int(10) unsigned NOT NULL auto_increment,
			`id_customer` int(11) unsigned NOT NULL,
			`config` text,
			PRIMARY KEY(`id_megacustomer`)
			) DEFAULT CHARSET=utf8;	
ALTER TABLE `PREFIX_order_detail` 
ADD `extra_tax` decimal(20,6) NOT NULL default '0';	