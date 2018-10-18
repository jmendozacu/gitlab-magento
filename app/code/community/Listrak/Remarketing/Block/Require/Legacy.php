<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.1.9
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2014 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

/**
 * Class Listrak_Remarketing_Block_Require_Legacy
 */
class Listrak_Remarketing_Block_Require_Legacy
    extends Listrak_Remarketing_Block_Base
{
    private $_canRender = null;

    /**
     * Render block
     *
     * @return string
     */
    public function _toHtml()
    {
        try {
            if (!$this->canRender()) {
                return '';
            }

            return parent::_toHtml();
        } catch(Exception $e) {
            $this->getLogger()->addException($e);

            return '';
        }
    }

    /**
     * Can render
     *
     * @return bool
     */
    public function canRender()
    {
        if ($this->_canRender == null) {
            /* @var Listrak_Remarketing_Helper_Data $helper */
            $helper = Mage::helper('remarketing');

            $this->_canRender = $helper->legacyTracking();
        }

        return $this->_canRender;
    }
}
