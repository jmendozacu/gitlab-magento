<?php 
class Born_Package_Model_Catalog_Product_View_Data extends Varien_Object 
{
    private function _getConnection($resource='core/resource',$name='core_write') {
        return  Mage::getSingleton($resource)->getConnection($name);

    }

    public function getSubscriptionRowByProductId($productId)
    {
        if (!$productId) {
            return;
        }

        $_tableName = 'aw_sarp2_subscription';
        $_query = "SELECT  * FROM {$_tableName} WHERE product_id='{$productId}';";

        $_attributes = null;

        try {
            $_attributes = $this->_getConnection()->fetchAll($_query);
        } catch (Exception $e) {
            Mage::logException($e);
            return;
        }

        return $_attributes;
    }
}