<?php
/**
 * @author Astral Brands Team
 * @package Astral_Integrations
 */


class Astral_Integrations_Helper_ProductDecorator extends Mage_Core_Helper_Abstract {

    /**
     * Returns criteo event for viewing a product
     * @param  product $product Magento Product
     * @return Array Criteo view item event
     */
    public function getCriteoViewItemEventArray($product) {
        
        $criteoItemView = array();
        if(isset($product) && !empty($product)) {
            $criteoItemView['event'] = 'viewItem';
            $criteoItemView['item'] = $product->getData('sku');
        }
        return $criteoItemView;
    }
    
    public function getPixelViewContentEventArray($product, $quantity = 1) {

        $pixelViewContentEvent = array();
        if(isset($product) && !empty($product)) {
            $value = number_format($product->getPrice(), 2);
            $id = array(
                $product->getData('sku')
            );
            $content = array(
                [
                    'id' => $product->getData('sku'),
                    'item_price' => $value,
                    'quantity' => $quantity
                ]
            );

            $value = number_format($product->getPrice(), 2);

            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $childIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($product->getId());
                if (!Mage::helper('astral_integrations_helper')->recursiveIsEmpty($childIds)) {
                    $childProductCollection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addIdFilter ($childIds)
                        ->addAttributeToSelect('price')
                        ->addAttributeToSelect('sku');

                    foreach($childProductCollection as $childProduct) {
                        Mage::log($childProduct, 1, 'astral_integrations.log');

                        $simplePrice = number_format($childProduct->getData('price'), 2);
                        if ($simplePrice < $value) {
                            $value = $simplePrice;
                        }
                        $id[] = $childProduct->getSku();
                        //Get each child product
                        $content[] = [
                            'id' => $childProduct->getSku('sku'),
                            'item_price' => $simplePrice,
                            'quantity' => $quantity
                        ];
                    }
                }
            } else if($product->getTypeId() == 
                Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                //NOTE THIS NEEDS A BUNDLED PRODUCT FOR TESTING
                $childProductCollection = $product->getTypeInstance(true)
                    ->getSelectionsCollection($product->getTypeInstance(true)
                                                ->getOptionsIds($product), $product);
                //Get each child product
                foreach($childProductCollection as $childProduct) {
                    $id[] = $childProduct->getData('sku');
                    $content[] = [
                        'id' => $childProduct->getData('sku'),
                        'item_price' => number_format($childProduct->getPrice(), 2),
                        'quantity' => $childProduct->selection_qty
                    ];
                }
            }

            $pixelViewContentEvent['content_name'] = $product->getName();
            $pixelViewContentEvent['content_type'] = 'product';
            $pixelViewContentEvent['content_ids'] = $id;
            $pixelViewContentEvent['content'] = $content;
            $pixelViewContentEvent['value'] = $value;
            $pixelViewContentEvent['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        }

        return $pixelViewContentEvent;
    }

}
