<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.0.0
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2011 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

/**
 * Class Listrak_Remarketing_Model_Apiextension_Api
 */
class Listrak_Remarketing_Model_Apiextension_Api
    extends Mage_Api_Model_Resource_Abstract
{
    private $_customerAttributes = array('entity_id' => '', 'firstname' => '',
        'lastname' => '', 'email' => '', 'website_id' => '', 'store_id' => '',
        'group_id' => '', 'gender_name' => '', 'dob' => '', 'group_name' => '',
        'gender' => '');

    /**
     * Retrieve subscriber information
     *
     * @param int      $storeId   Magento store ID
     * @param datetime $startDate Lower date constraint
     * @param int      $perPage   Page size
     * @param int      $page      Cursor
     *
     * @return array
     *
     * @throws Exception
     */
    public function subscribers(
        $storeId = 1, $startDate = null, $perPage = 50, $page = 1
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        Mage::app()->setCurrentStore($storeId);
        $helper->requireCoreEnabled();

        if ($startDate === null || !strtotime($startDate)) {
            $this->_fault('incorrect_date');
        }

        try {
            $result = array();

            /* @var Listrak_Remarketing_Model_Mysql4_Apiextension $resource */
            $resource = Mage::getModel("listrak/apiextension")
                ->getResource();

            $collection = $resource
                ->subscribers($storeId, $startDate, $perPage, $page);

            foreach ($collection as $item) {
                $result[] = $item;
            }

            return $result;
        } catch (Exception $e) {
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }

    /**
     * Purge old subscriber log entries
     *
     * @param datetime $endDate Upper date constraint
     *
     * @return int
     *
     * @throws Exception
     */
    public function subscribersPurge($endDate = null)
    {
        if ($endDate === null || !strtotime($endDate)) {
            $this->_fault('incorrect_date');
        }

        try {
            $updates = Mage::getModel("listrak/subscriberupdate")
                ->getCollection()
                ->addFieldToFilter('updated_at', array('lt' => $endDate));

            $count = 0;

            /* @var Listrak_Remarketing_Model_Subscriberupdate $update */
            foreach ($updates as $update) {
                $update->delete();
                $count++;
            }

            return $count;
        } catch (Exception $e) {
            /* @var Listrak_Remarketing_Helper_Data $helper */
            $helper = Mage::helper('remarketing');
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }

    /**
     * Retrieve customer information
     *
     * @param int $storeId   Magento store ID
     * @param int $websiteId Magento website ID (deprecated)
     * @param int $perPage   Page size
     * @param int $page      Cursor
     *
     * @return array
     *
     * @throws Exception
     */
    public function customers($storeId = 1, $websiteId = 1, $perPage = 50, $page = 1)
    {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        Mage::app()->setCurrentStore($storeId);
        $helper->requireCoreEnabled();

        try {
            $collection = Mage::getModel('customer/customer')->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->addAttributeToSelect('*')
                ->setPageSize($perPage)
                ->setCurPage($page);

            $results = array();

            foreach ($collection as $customer) {
                $results[] = $this->_getCustomerArray($storeId, $customer);
            }

            return $results;
        } catch (Exception $e) {
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }

    /**
     * Transform product object into array
     *
     * @param int                          $storeId  Magento store ID
     * @param Mage_Customer_Model_Customer $customer Customer
     *
     * @return array
     */
    private function _getCustomerArray(
        $storeId, Mage_Customer_Model_Customer $customer
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        $helper->setGroupNameAndGenderNameForCustomer($customer);
        $result = array_intersect_key($customer->toArray(), $this->_customerAttributes);
        
        $metas = $this->_getCustomerMetas($storeId, $customer);
        if ($metas) {
            if (isset($metas['meta2'])) {
                $result['meta2'] = $metas['meta2'];
            }
            if (isset($metas['meta3'])) {
                $result['meta3'] = $metas['meta3'];
            }
            if (isset($metas['meta4'])) {
                $result['meta4'] = $metas['meta4'];
            }
            if (isset($metas['meta5'])) {
                $result['meta5'] = $metas['meta5'];
            }
        }
        
        return $result;
    }

    /**
     * Retrieve order status updates
     *
     * @param int      $storeId   Magento store ID
     * @param datetime $startDate Lower date constraint
     * @param datetime $endDate   Upper date constraint
     * @param int      $perPage   Page size
     * @param int      $page      Cursor
     *
     * @return array
     *
     * @throws Exception
     */
    public function orderStatus(
        $storeId = 1, $startDate = null, $endDate = null,
        $perPage = 50, $page = 1
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        Mage::app()->setCurrentStore($storeId);
        $helper->requireCoreEnabled();

        try {
            /* @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel("sales/order")->getCollection();

            $collection->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter(
                    'updated_at', array('from' => $startDate, 'to' => $endDate)
                )
                ->addFieldToFilter('status', array('neq' => 'pending'))
                ->setPageSize($perPage)->setCurPage($page)
                ->setOrder('updated_at', 'ASC');

            if (!$helper->getMetaDataProvider()) {
                $collection->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('increment_id')
                    ->addAttributeToSelect('status')
                    ->addAttributeToSelect('updated_at');
            }

            $results = array();
            
            $allQuantities = $this->_getProductQtyInfoForOrders($storeId, $collection);

            /* @var Mage_Sales_Model_Order $order */
            foreach ($collection as $order) {
                $result = array();
                $result['increment_id'] = $order->getIncrementId();
                $result['status'] = $order->getStatus();
                $result['updated_at'] = $order->getUpdatedAt();

                $metas = $this->_getOrderMetas($storeId, $order);
                if ($metas) {
                    if (isset($metas['meta1'])) {
                        $result['meta1'] = $metas['meta1'];
                    }
                    if (isset($metas['meta2'])) {
                        $result['meta2'] = $metas['meta2'];
                    }
                    if (isset($metas['meta3'])) {
                        $result['meta3'] = $metas['meta3'];
                    }
                    if (isset($metas['meta4'])) {
                        $result['meta4'] = $metas['meta4'];
                    }
                    if (isset($metas['meta5'])) {
                        $result['meta5'] = $metas['meta5'];
                    }
                }

				/* @var Mage_Sales_Model_Order $_tracks */
                $tracks = $order->getTracksCollection();
                
                if ($tracks && $tracks->getSize() > 0) {
                    $trackingData = $tracks->getData();
                    $result['tracking_number'] = $trackingData[0]['number'];
                    $result['carrier_code'] = $trackingData[0]['carrier_code'];              
                }	
                
                $result['quantities'] = array();
                $productIds = $this->_getOrderProductIds($order);
                foreach($productIds as $productId) {
                    if (array_key_exists($productId, $allQuantities)) {
                        $result['quantities'][] = $allQuantities[$productId];
                        unset($allQuantities[$productId]);
                    }
                }

                $results[] = $result;
            }

            return $results;
        } catch (Exception $e) {
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }
    
    /**
     * Retrieve inventory information for all products in all orders
     * 
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return array
     */
    private function _getProductQtyInfoForOrders($storeId, $collection)
    {
        /* @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        
        $productIds = $this->_getAllProductIds($collection);
        
        if (sizeof($productIds) == 0) {
            return array();
        }
        
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('entity_id', $productIds)
            ->addAttributeToSelect('*');
        
        Mage::getModel('cataloginventory/stock')
            ->addItemsToProducts($products);
        
        $result = array();
        foreach($products as $productId => $product) {
            $item = array(
                'sku' => $product->getSku(),
                'in_stock' => $product->isAvailable() ? "true" : "false"
            );
            
            /* @var Mage_Cataloginventory_Model_Stock_Item $stockItem */
            $stockItem = $product->getStockItem();
            if ($stockItem) {
                $item['qty_on_hand'] = $stockItem->getStockQty();
            }
            
            $result[$productId] = $item;
        }
        
        return $result;
    }
    
    /**
     * Retrieve all product IDs for order collection
     * 
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return array
     */
    private function _getAllProductIds($collection)
    {
        $ids = array();
        foreach($collection as $order) {
            $ids = array_merge($ids, $this->_getOrderProductIds($order));
        }
        return array_unique($ids);
    }
    
    /**
     * Retrieve all product IDs in order
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    private function _getOrderProductIds($order)
    {
        $ids = array();
        foreach($order->getAllItems() as $item)
        {
            $ids[] = $item->getProductId();
        }
        return $ids;
    }

    /**
     * Retrieve orders
     *
     * @param int      $storeId   Magento store ID
     * @param datetime $startDate Lower date constraint
     * @param datetime $endDate   Upper date constraint
     * @param int      $perPage   Page size
     * @param int      $page      Cursor
     *
     * @return array
     *
     * @throws Exception
     */
    public function orders(
        $storeId = 1, $startDate = null, $endDate = null,
        $perPage = 50, $page = 1
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        Mage::app()->setCurrentStore($storeId);
        $helper->requireCoreEnabled();

        if ($startDate === null || !strtotime($startDate)) {
            $this->_fault('incorrect_date');
        }

        if ($endDate === null || !strtotime($endDate)) {
            $this->_fault('incorrect_date');
        }

        try {
            /* @var Mage_Sales_Model_Resource_Order_Collection $orders */
            $orders = Mage::getModel('sales/order')->getCollection();

            $orders
                ->addFieldToFilter(
                    'created_at', array('from' => $startDate, 'to' => $endDate)
                )
                ->addFieldToFilter('store_id', $storeId)
                ->setPageSize($perPage)->setCurPage($page)
                ->setOrder('created_at', 'ASC');

            $customerIds = array();
            foreach ($orders as $order) {
                $customerIds[] = $order->getCustomerId();
            }

            $customerMap = array();
            if (sizeof($customerIds) > 0) {
                /* @var Mage_Customer_Model_Customer $customers */
                $customers = Mage::getModel('customer/customer')->getCollection();
                $customers
                    ->addAttributeToSelect(array_keys($this->_customerAttributes))
                    ->addFieldToFilter('entity_id', $customerIds);

                $customerMap = $customers->getItems();
            }
            
            $stockInfo = $this->_getProductQtyInfoForOrders($storeId, $orders);

            $results = array();

            /* @var Mage_Sales_Model_Order $order */
            foreach ($orders as $order) {
                $result = array();
                $result['info']['entity_id'] = $order->getEntityId();
                $result['info']['order_id'] = $order->getIncrementId();
                $result['info']['status'] = $order->getStatus();;
                $result['info']['customer_firstname']
                    = $order->getCustomerFirstname();
                $result['info']['customer_lastname'] = $order->getCustomerLastname();
                $result['info']['customer_email'] = $order->getCustomerEmail();
                $result['info']['subtotal'] = $order->getSubtotal();
                $result['info']['discount_amount'] = $order->getDiscountAmount();
                $result['info']['tax_amount'] = $order->getTaxAmount();
                $result['info']['shipping_amount'] = $order->getShippingAmount();
                $result['info']['grand_total'] = $order->getGrandTotal();
                $result['info']['coupon_code'] = $order->getCouponCode();
                $result['info']['billing_firstname'] = $order->getBillingFirstname();
                $result['info']['created_at'] = $order->getCreatedAt();
                $result['info']['updated_at'] = $order->getUpdatedAt();

                $metas = $this->_getOrderMetas($storeId, $order);
                if ($metas) {
                    if (isset($metas['meta1'])) {
                        $result['info']['meta1'] = $metas['meta1'];
                    }
                    if (isset($metas['meta2'])) {
                        $result['info']['meta2'] = $metas['meta2'];
                    }
                    if (isset($metas['meta3'])) {
                        $result['info']['meta3'] = $metas['meta3'];
                    }
                    if (isset($metas['meta4'])) {
                        $result['info']['meta4'] = $metas['meta4'];
                    }
                    if (isset($metas['meta5'])) {
                        $result['info']['meta5'] = $metas['meta5'];
                    }
                }

                /* @var Mage_Sales_Model_Order_Address $shipping */
                $shipping = $order->getShippingAddress();
                if ($shipping) {
                    $result['shipping_address']['firstname']
                        = $shipping->getFirstname();
                    $result['shipping_address']['lastname']
                        = $shipping->getLastname();
                    $result['shipping_address']['company'] = $shipping->getCompany();
                    $result['shipping_address']['street']
                        = implode(', ', $shipping->getStreet());
                    $result['shipping_address']['city'] = $shipping->getCity();
                    $result['shipping_address']['region'] = $shipping->getRegion();
                    $result['shipping_address']['postcode']
                        = $shipping->getPostcode();
                    $result['shipping_address']['country'] = $shipping->getCountry();
                }

                /* @var Mage_Sales_Model_Order_Address $billing */
                $billing = $order->getBillingAddress();
                if ($billing) {
                    $result['billing_address']['firstname']
                        = $billing->getFirstname();
                    $result['billing_address']['lastname'] = $billing->getLastname();
                    $result['billing_address']['company'] = $billing->getCompany();
                    $result['billing_address']['street']
                        = implode(', ', $billing->getStreet());
                    $result['billing_address']['city'] = $billing->getCity();
                    $result['billing_address']['region'] = $billing->getRegion();
                    $result['billing_address']['postcode'] = $billing->getPostcode();
                    $result['billing_address']['country'] = $billing->getCountry();
                }

                if ($helper->trackingTablesExist()) {
                    $result['session']
                        = Mage::getModel("listrak/session")
                            ->load($order->getQuoteId(), 'quote_id');
                }

                $result['product'] = array();
                foreach ($order->getAllVisibleItems() as $item) {
                    $result['product'][]
                        = $this->_getOrderItemProductEntity($storeId, $order, $item, $stockInfo);
                }

                if ($order->getCustomerId()) {
                    if (array_key_exists($order->getCustomerId(), $customerMap)) {
                        $result['customer']
                            = $this->_getCustomerArray($storeId, $customerMap[$order->getCustomerId()]);
                    }
                }

                $results[] = $result;
            }

            return $results;
        } catch (Exception $e) {
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }

    /**
     * Extract order item information for Magento objects
     *
     * @param int                         $storeId
     * @param Mage_Sales_Model_Order      $order
     * @param Mage_Sales_Model_Order_Item $item
     * @param array                       $stockByProduct
     *
     * @return array
     */
    private function _getOrderItemProductEntity(
        $storeId, Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Item $item,
        $stockByProduct
    ) {
        /* @var Listrak_Remarketing_Helper_Product $productHelper */
        $productHelper = Mage::helper('remarketing/product');

        $info = $productHelper
            ->getProductInformationFromOrderItem($item, array('product'));

        /* @var Mage_Catalog_Model_Product $productModel */
        $productModel = $info->getProduct();

        $product = array();
        if ($productModel && $productModel->getId()) {
            $product['sku'] = $productModel->getSku();
            $product['name'] = $productModel->getName();
            $product['product_price'] = $productModel->getPrice();
            
            if (array_key_exists($productModel->getId(), $stockByProduct)) {
                $stock = $stockByProduct[$productModel->getId()];
                $product['in_stock'] = $stock['in_stock'];
                $product['qty_on_hand'] = $stock['qty_on_hand'];
            }
        } else {
            $product['sku'] = $item->getProductOptionByCode('simple_sku')
                ? $item->getProductOptionByCode('simple_sku')
                : $item->getSku();
            $product['name'] = $item->getName();
        }

        $product['price'] = $item->getPrice();
        $product['qty_ordered'] = $item->getQtyOrdered();

        $metas = $this->_getOrderItemMetas(
            $storeId, $order, $item, $info->getProduct()
        );
        if ($metas) {
            if (isset($metas['meta1'])) {
                $product['meta1'] = $metas['meta1'];
            }
            if (isset($metas['meta2'])) {
                $product['meta2'] = $metas['meta2'];
            }
            if (isset($metas['meta3'])) {
                $product['meta3'] = $metas['meta3'];
            }
            if (isset($metas['meta4'])) {
                $product['meta4'] = $metas['meta4'];
            }
            if (isset($metas['meta5'])) {
                $product['meta5'] = $metas['meta5'];
            }
        }

        if ($info->getIsBundle()) {
            $product['bundle_items'] = array();
            foreach ($item->getChildrenItems() as $childItem) {
                $product['bundle_items'][] = $this->_getOrderItemProductEntity(
                    $storeId, $order, $childItem, $stockByProduct
                );
            }
        }

        return $product;
    }

    /**
     * Retrieve customer meta data from external provider
     *
     * @param int                          $storeId  Magento store ID
     * @param Mage_Customer_Model_Customer $customer Customer
     *
     * @return array|null
     */
    private function _getCustomerMetas(
        $storeId, Mage_Customer_Model_Customer $customer
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        try {
            $provider = $helper->getMetaDataProvider();
            if ($provider) {
                return $provider->customer($storeId, $customer);
            }
        }
        catch(Exception $e) {
            $helper->generateAndLogException(
                'Error retrieving customer meta data.', $e
            );
        }

        return null;
    }

    /**
     * Retrieve order meta data form external provider
     *
     * @param int                    $storeId Magento store ID
     * @param Mage_Sales_Model_Order $order   Order
     *
     * @return array|null
     */
    private function _getOrderMetas($storeId, Mage_Sales_Model_Order $order)
    {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        try {
            $provider = $helper->getMetaDataProvider();
            if ($provider) {
                return $provider->order($storeId, $order);
            }
        }
        catch(Exception $e) {
            $helper->generateAndLogException(
                'Error retrieving order meta data.', $e
            );
        }

        return null;
    }

    /**
     * Retrieve order item meta data from external provider
     *
     * @param int                         $storeId   Magento store ID
     * @param Mage_Sales_Model_Order      $order     Order
     * @param Mage_Sales_Model_Order_Item $orderItem Order item
     * @param Mage_Catalog_Model_Product  $product   Ordered product
     *
     * @return array|null
     */
    private function _getOrderItemMetas(
        $storeId,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Item $orderItem,
        Mage_Catalog_Model_Product $product = null
    ) {
        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        try {
            $provider = $helper->getMetaDataProvider();
            if ($provider) {
                return $provider->orderItem($storeId, $order, $orderItem, $product);
            }
        }
        catch(Exception $e) {
            $helper->generateAndLogException(
                'Error retrieving order item meta data.', $e
            );
        }

        return null;
    }

    /**
     * Retrieves information about the store
     *
     * @param int $storeId Magento store ID
     *
     * @return array
     *
     * @throws Exception
     */
    public function info($storeId)
    {
        Mage::app()->setCurrentStore($storeId);

        /* @var Listrak_Remarketing_Helper_Data $helper */
        $helper = Mage::helper('remarketing');

        try {
            $result = array();
            $result["magentoVersion"] = Mage::getVersion();
            
            $module = Mage::getConfig()->getNode('modules')->Listrak_Remarketing;

            /* @var Mage_Core_Model_Resource_Resource $resourceResource */
            $resourceResource = Mage::getModel('core/resource_resource');

            $result['listrakExtension']['active'] = (string)$module->active;
            $result['listrakExtension']['output']
                = Mage::getStoreConfig(
                    "advanced/modules_disable_output/Listrak_Remarketing"
                ) == '1' ? 'false' : 'true';
            $result['listrakExtension']['version'] = (string)$module->version;
            if ($resourceResource) {
                $result['listrakExtension']['install_version']
                    = $resourceResource->getDbVersion('listrak_remarketing_setup');
                $result['listrakExtension']['data_version']
                    = $resourceResource->getDataVersion('listrak_remarketing_setup');
            }

            $result["listrakSettings"] = array(
                "coreEnabled" => $helper->coreEnabled() ? "true" : "false",
                "onescriptEnabled" => $helper->onescriptEnabled() ? "true" : "false",
                "onescriptReady" => $helper->onescriptReady() ? "true" : "false",
                "trackingID" => Mage::getStoreConfig(
                    'remarketing/modal/listrakMerchantID'
                ),
                "scaEnabled" => $helper->scaEnabled() ? "true" : "false",
                "activityEnabled" => $helper->activityEnabled() ? "true" : "false",
                "reviewsApiEnabled" => $helper->reviewsEnabled() ? "true" : "false",
                "trackingTablesExist" =>
                    $helper->trackingTablesExist() ? "true" : "false",
                "skipCategoriesText" => Mage::getStoreConfig(
                    'remarketing/productcategories/categories_skip'
                ),
                "skipCategories" => implode(",", $helper->getCategoriesToSkip())
            );
            $result["ini"] = array();

            $subModel = Mage::getModel("newsletter/subscriber");
            $orderModel = Mage::getModel("sales/order");
            $productModel = Mage::getModel('catalog/product');

            $result["classes"] = get_class($subModel) . ','
                . get_class($orderModel) . ','
                . get_class($orderModel->getCollection()) . ','
                . get_class($productModel) . ','
                . get_class($productModel->getCollection());

            $numSubUpdates = Mage::getModel("listrak/subscriberupdate")
                    ->getCollection()
                    ->getSize();
            
            if ($helper->trackingTablesExist()) {
                $numSessions = Mage::getModel("listrak/session")
                        ->getCollection()
                        ->getSize();
                $numClicks = Mage::getModel("listrak/click")
                        ->getCollection()
                        ->getSize();

                $result["counts"] = $numSessions . ','
                    . $numSubUpdates . ','
                    . $numClicks;
            } else {
                $result["counts"] = $numSubUpdates;
            }

            $result["modules"] = array();
            $modules = (array)Mage::getConfig()->getNode('modules')->children();

            foreach ($modules as $key => $value) {
                $valueArray = $value->asCanonicalArray();
                $version = isset($valueArray["version"])
                    ? $valueArray["version"]
                    : '';
                $active = isset($valueArray["active"])
                    ? $valueArray["active"]
                    : '';
                $result["modules"][]
                    = "name=$key, version=$version, isActive=$active";
            }

            $ini = array("session.gc_maxlifetime", "session.cookie_lifetime",
                "session.gc_divisor", "session.gc_probability");

            foreach ($ini as $iniParam) {
                $result["ini"][] = "$iniParam=" . ini_get($iniParam);
            }

            return $result;
        } catch (Exception $e) {
            throw $helper->generateAndLogException(
                "Exception occurred in API call: " . $e->getMessage(), $e
            );
        }
    }
}
