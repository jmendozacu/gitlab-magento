<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Perm
 */
if (Mage::helper('core')->isModuleEnabled('Amasty_Customerattr')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Perm_Model_Core_Email_Template_Customerattr');
} else {
    class Amasty_Perm_Model_Core_Email_Template_Pure extends Mage_Core_Model_Email_Template {}
}