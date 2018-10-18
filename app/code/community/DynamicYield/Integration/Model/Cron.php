<?php
/**
 * DynamicYield_Integration
 *
 * @category     DynamicYield
 * @package      DynamicYield_Integration
 * @author       Dynamic Yield Ltd <support@dynamicyield.com.com>
 * @copyright    Copyright (c) 2017 Dynamic Yield (https://www.dynamicyield.com)
 **/

/**
 * Class DynamicYield_Integration_Model_Cron
 */
class DynamicYield_Integration_Model_Cron
{
    /**
     * Exports the feed
     */
    public function exportFeed() {
        $export = Mage::getModel('dynamicyield_integration/export');

        return $export->export();
    }

    /**
     * Run cron validator
     *
     * @return bool
     */
    public function run()
    {
        return true;
    }
}
