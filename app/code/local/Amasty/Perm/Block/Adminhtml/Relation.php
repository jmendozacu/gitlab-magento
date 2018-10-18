<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Perm
 */
class Amasty_Perm_Block_Adminhtml_Relation extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ampermGridCustomers');
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_customers') {
            $ids = $this->_getSelectedCustomers();
            if (empty($ids)) {
                $ids = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$ids));
            }
            elseif(!empty($ids)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$ids));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }     
    
    protected function _prepareCollection()
    {
        if ($this->_getUserId()) {
            $this->setDefaultFilter(array('perm'=>1));
        } 
        
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_customers', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'perm',
            'values'    => $this->_getSelectedCustomers(),
            'align'     => 'right',
            'index'     => 'entity_id',
        ));         
        
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('amperm')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'      => 'number',
        ));

        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('amperm')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('amperm')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $this->addColumn('am_uid', array(
            'header'   => Mage::helper('amperm')->__('Dealer'),
            'index'    => 'am_uid',
            'type'     => 'options',
            'options'  => Mage::helper('amperm')->getSalesPersonList(),
            'sortable' => false,
            'align'    => 'center',
            'filter_condition_callback' => array($this, 'dealerFilter'),
        ));
                
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/relation', array('_current' => true));
    }

    public function getRowUrl($row)
    {
       return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getEntityId()));
    }
    
    // current selection
    protected function _getSelectedCustomers()
    {
        $customers = $this->getSelectedCustomers();
        if (!is_array($customers)) {
            $customers = $this->getSavedCustomers();
        }
        return $customers;
    } 

    // selection in db
    public function getSavedCustomers()
    {
        return Mage::getModel('amperm/perm')->getCustomers($this->_getUserId());        
    }   
    
    protected function _getUserId()
    {
        return $this->getRequest()->getParam('user_id', 0);   
    }

    public static function dealerFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        
        if (-1 == $value) {
            $value = 0;
            if (false !== strpos(get_class($collection), 'Customer_Collection')) {
                $collection->getSelect()
                    ->where('amperm.uid = ?', $value)
                    ->orWhere('amperm.uid IS NULL');
            } else {
                $collection->getSelect()
                    ->joinLeft(
                        array('amperm_order' => Mage::getSingleton('core/resource')->getTableName('amperm/order')),
                        'main_table.entity_id = amperm_order.oid',
                        array('am_uid' => 'uid')
                    );
                $collection->getSelect()
                    ->where('amperm_order.uid = ?', $value)
                    ->orWhere('amperm_order.uid IS NULL');
            }
        } elseif ($value) {
            if (false !== strpos(get_class($collection), 'Customer_Collection')) {
                $collection->getSelect()->where('amperm.uid = ?', $value);
            } else {
                $collection->getSelect()
                    ->joinLeft(
                        array('amperm_order' => Mage::getSingleton('core/resource')->getTableName('amperm/order')),
                        'main_table.entity_id = amperm_order.oid',
                        array('am_uid' => 'uid')
                    );
                $collection->getSelect()->where('amperm_order.uid = ?', $value);
            }
        }
        
        return $collection;
    }
}