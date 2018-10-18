<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Perm
 */
 
$this->startSetup();

$this->run("
	ALTER TABLE `{$this->getTable('admin/user')}` ADD `emails` TEXT DEFAULT NULL;
");

$this->endSetup();	