<?php
/**
 * @author Astral Brands Team
 * @package Astral_Integrations
 */


class Astral_Integrations_Helper_ProductCollectionDecorator extends Mage_Core_Helper_Abstract {

    /**
     * Returns the array used for the Criteo View List Event
     * @param  [type] $productCollection [description]
     * @return [type]                    [description]
     */
    public function getCriteoViewListEventArray($productCollection) {
        $criteoViewList = array();
        if(isset($productCollection) && $productCollection) {

            $criteoViewList['event'] = 'viewList';

            if(isset($productCollection) && !empty($productCollection)) {
                foreach($productCollection as $product) {
                    $criteoViewList['item'][] = $product->getSku();
                }
            }

        }
        return $criteoViewList;
    }
}
