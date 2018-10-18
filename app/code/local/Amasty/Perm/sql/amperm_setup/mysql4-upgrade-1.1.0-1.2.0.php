<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Perm
 */

$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `uid` INT NOT NULL; 
    UPDATE `{$this->getTable('sales/order_grid')}` AS o, {$this->getTable('amperm/perm')} AS p SET o.uid=p.uid WHERE o.customer_id=p.cid;
");

$this->endSetup();