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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

namespace Enterprise\Rma\Test\Block\Returns\View;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Client\Locator;
use Enterprise\Rma\Test\Block\Returns\View\Items\Item;

/**
 * Items block on RMA view page.
 */
class Items extends Block
{
    /**
     * Xpath selector for item row.
     *
     * @var string
     */
    protected $itemRow = './/tbody/tr';

    /**
     * Get Items data.
     *
     * @return array
     */
    public function getData()
    {
        $result = [];
        $rows = $this->_rootElement->getElements($this->itemRow, Locator::SELECTOR_XPATH);
        foreach ($rows as $row) {
            $result[] = $this->getItemRowBlock($row)->getRowData();
        }

        return $result;
    }

    /**
     * Get item row block.
     *
     * @param ElementInterface $element
     * @return Item
     */
    protected function getItemRowBlock(ElementInterface $element)
    {
        return $this->blockFactory->create(
            'Enterprise\Rma\Test\Block\Returns\View\Items\Item',
            ['element' => $element]
        );
    }
}
