<?php

/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    RocketWeb
 */
class RocketWeb_ShoppingFeeds_Model_Source_Directive_Product_Categorypath extends Varien_Object
{

    public function toHtml()
    {
        return '<div style="float:left;">'. Mage::helper('rocketshoppingfeeds')->__('Paths limit:'). '</div>
                <div style="float:right;"><input type="text" name="config[#{field_name}][#{_id}][param]" value="#{param}" class="input-text" style="width:180px;"></div>
                <p class="note" style="clear:both;"><span>' . Mage::helper('rocketshoppingfeeds')->__('Uses product categories to create a comma separated list of category paths. Deeper categories are listed first. Limit paths defines how many comma separated values to output.'). '</span></p>';
    }
}