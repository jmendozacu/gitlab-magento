<?php

class WeltPixel_Custom_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Add review filter to product collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        $this->_productCollection = parent::_getProductCollection();
        $reviewSummaryTable = Mage::getSingleton('core/resource')->getTableName('review_entity_summary');

        try {
            $this->_productCollection->joinField(
                'rating_summary',
                $reviewSummaryTable,
                'rating_summary',
                'entity_pk_value=entity_id',
                array('entity_type' => 1, 'store_id' => Mage::app()->getStore()->getId()),
                'left'
            );
        } catch (Exception $ex) {

        }

        return $this->_productCollection;
    }

    /**
     * This is copied from WeltPixel_QuickView module
     * @param $product
     * @param array $additional
     * @return mixed
     */
    public function getQuickViewUrl($product, $additional = array()) {
        return Mage::helper('weltpixel_quickview')->getProductUrl($product, $additional);
    }
}
