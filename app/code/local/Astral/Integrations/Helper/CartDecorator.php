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
                $row['id'] = $item->getProduct()->getData('sku');
                $row['price'] = number_format($item->getPrice(), 2);
                $row['quantity'] = $item->getQty();
                $criteoCart['item'][] = $row;
            }
        }
        
        return $criteoCart;
    }
    
}
