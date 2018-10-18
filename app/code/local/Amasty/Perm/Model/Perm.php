<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Perm
 */
class Amasty_Perm_Model_Perm extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amperm/perm');
    }
    
    //ids only
    public function getCustomers($userId)
    {
        return $this->getResource()->getCustomerIds($userId);
    }
    
    public function assignCustomers($userId, $customerIds)
    {
        $this->getResource()->assignCustomers($userId, $customerIds);
        return $this;
    }
    
    public function assignOneCustomer($userId, $customerId)
    {
        $this->getResource()->assignOneCustomer($userId, $customerId);
        return $this;
    }

    public function addOrdersRestriction($collection, $userId, $select = false, $column = 'entity_id')
    {
        $orders = $this->getResource()->getOrderIds($userId);
        if ($select) {
            $collection->where('main_table.' . $column . ' IN (?)', $orders);
        } else {
            $collection->addFieldToFilter($column, array('in' => $orders));
        }
        return $this;
    }
    
    public function addCustomersRestriction($collection, $userId)
    {
        $collection->joinField(
            'perm',            // alias
            'amperm/perm',     // table
            'uid',             // field
            'cid=entity_id',   // bind
            array ('uid' => $userId), // conditions
            'inner'            // join type
        ); 
        return $this;
    }    
    
    public function assignOneOrder($userId, $orderId)
    {
        $this->getResource()->assignOneOrder($userId, $orderId);
        return $this;
    } 
    
    public function assignOrderByCustomer($customerId, $orderId)
    {
        $userId = $this->getUserByCustomer($customerId);
        $this->assignOneOrder($userId, $orderId);
        
        return $userId; 
    }     
    
    public function getUserByOrder($orderId)
    {
        return $this->getResource()->getUserByOrder($orderId); 
    }
    
    public function getUserByCustomer($customerId)
    {
        return $this->getResource()->getUserByCustomer($customerId);
    }

    public function removeOneCustomer($customerId)
    {
        $this->getResource()->removeOneCustomer($customerId);
        return $this;
    }

    public function massAssignCustomers($userId, $customerIds)
    {
        $this->getResource()->massAssignCustomers($userId, $customerIds);
        return $this;
    }
}