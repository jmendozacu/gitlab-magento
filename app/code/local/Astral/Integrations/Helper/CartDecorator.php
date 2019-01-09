<?php
/**
 * @author Astral Brands Team
 * @package Astral_Integrations
 */


class Astral_Integrations_Helper_CartDecorator extends Mage_Core_Helper_Abstract {

    public function getCriteoCartViewEventArray($cart) {
        
        $criteoCart = array();
        if(isset($cart) && !empty($cart)) {

            $criteoCart['event'] = 'viewBasket';
            $criteoCart['item'] = array();
            foreach($cart->getAllVisibleItems() as $item) {
                $row = array();
                $row['id'] = $item->getSku();
                $row['price'] = number_format($item->getPrice(), 2);
                $row['quantity'] = $item->getQty();
                $criteoCart['item'][] = $row;
            }
        }
        
        return $criteoCart;
    }
 
    public function getPixelInitiatCheckoutArray($cart) {

        $pixelCart = array();

        if(isset($cart) && !empty($cart)) {

            $pixelCart['currency'] = 'USD';
            $pixelCart['value'] = $cart->getGrandTotal();
            $pixelCart['num_items'] = 0;
            $pixelCart['content_ids'] = array();
            $pixelCart['contents'] = array();

            foreach($cart->getAllVisibleItems() as $item) {
                //Get Contents
                $isBundled = $item->getTypeId() == 
                Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;

                Mage::log($isBundled, null,'astral_integrations.log');

                $row = array();
                $row['id'] = $item->getSku();
                $row['price'] = number_format($item->getPrice(), 2);
                $row['quantity'] = $item->getQty();
                $pixelCart['contents'][] = $row;
                $pixelCart['content_ids'][] = $item->getSku();
                $pixelCart['num_items'] += $item->getQty();
            }
        }
        return $pixelCart;
    }

}
