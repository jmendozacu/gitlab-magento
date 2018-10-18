<?php 

class Born_Package_Helper_Aw_Sarp2_Subscription extends AW_Sarp2_Helper_Subscription
{
    /**
     * @param AW_Sarp2_Model_Subscription $subscription
     * @param Mage_Catalog_Model_Product  $product
     * @param string                      $title
     * @param string                      $subscriptionTypeSelectorType
     *
     * @return AW_Sarp2_Helper_Subscription
     */
    public function addSubscriptionTypeSelectorToSubscription(
        AW_Sarp2_Model_Subscription $subscription, Mage_Catalog_Model_Product $product,
        $title, $subscriptionTypeSelectorType = Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
    )
    {
        $productHelper = Mage::helper('aw_sarp2/product');
        $subscriptionTypeSelector = $productHelper->createProductOption(
            $product, $subscriptionTypeSelectorType,
            self::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID, $title
        );

        $subscriptionItemCollection = $this->getSubscriptionItemCollectionForDisplayingOnStore($subscription);
        $defaultCheckedValue = $subscriptionItemCollection->getFirstItem()->getId();
        if (!$subscription->getIsSubscriptionOnly()) {
            $productHelper->addProductOptionValue(
                $subscriptionTypeSelector, self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE,
                Mage::helper('aw_sarp2')->__(self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_TITLE)
            );
            $defaultCheckedValue = self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE;
        }
        /** ++ hack for default checked option in 1.7.x*/
        $preconfiguredValuesObject = $product->getPreconfiguredValues();
        if (!is_null($preconfiguredValuesObject)) {
            $currentOptionValue = $preconfiguredValuesObject
                ->getData('options/' . $subscriptionTypeSelector->getOptionId())
            ;
            if (is_null($currentOptionValue)) {
                $values = $product->getPreconfiguredValues()
                    ->setData('options', array($subscriptionTypeSelector->getOptionId() => $defaultCheckedValue))
                ;
                $product->setPreconfiguredValues($values);
            }
        }
        /** -- hack for default checked option in 1.7.x*/
        foreach ($subscriptionItemCollection as $item) {

            $productHelper->addProductOptionValue(
                $subscriptionTypeSelector,
                $item->getId(),
                $item->getTypeModel()->getTitle(),
                $item->getTypeModel()->getMessage(),
                $item->getTypeModel()->getInformation()
            );
        }
        $product->addOption($subscriptionTypeSelector);
        return $this;
    }
}

 ?>