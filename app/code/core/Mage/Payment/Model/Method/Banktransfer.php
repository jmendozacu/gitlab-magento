<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Bank Transfer payment method model
 */
class Mage_Payment_Model_Method_Banktransfer extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_METHOD_BANKTRANSFER_CODE = 'banktransfer';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_BANKTRANSFER_CODE;

    /**
     * Bank Transfer payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'payment/form_banktransfer';
    protected $_infoBlockType = 'payment/info_banktransfer';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

}
