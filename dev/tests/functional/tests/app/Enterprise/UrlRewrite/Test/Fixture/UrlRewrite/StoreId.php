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

namespace Enterprise\UrlRewrite\Test\Fixture\UrlRewrite;

use Mage\Adminhtml\Test\Fixture\Store;
use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Prepare StoreId for Url rewrite.
 */
class StoreId extends DataSource
{

    /**
     * Store fixture.
     *
     * @var Store
     */
    public $store;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;
        if (isset($data['dataset'])) {
            $store = $fixtureFactory->createByCode('store', ['dataset' => $data['dataset']]);
            /** @var Store $store */
            if (!$store->getStoreId()) {
                $store->persist();
            }
            $this->store = $store;
            $this->data = $store->getGroupId() . '/' . $store->getName();
        }
        if (isset($data['data'])) {
            $this->data = $data['data'];
        }
    }

    /**
     * Return Store fixture.
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
