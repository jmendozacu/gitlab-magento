<?php
$this->startSetup();
$this->run("
DROP TABLE IF EXISTS `{$this->getTable('astral_statuscheck_scc')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('astral_statuscheck_scc')}` (
	`increment_id` varchar(255) NOT NULL,
	`check_count` int(1) unsigned NOT NULL DEFAULT '0',
	`bypass_score` int(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$this->endSetup();