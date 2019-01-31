<?php
$this->startSetup();
$this->register();
$this->run("
DROP TABLE IF EXISTS `{$this->getTable('astral_statuscheck')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('astral_statuscheck')}` (
	`sc_id` int(10) unsigned NOT NULL auto_increment,
	`case_id` int(10) unsigned NOT NULL,
	`check_count` int(1) unsigned NOT NULL,
	`bypass_score` int(1) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$this->endSetup();