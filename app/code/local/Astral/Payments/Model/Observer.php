<?php
class Astral_Payments_Model_Observer{
    public function paymentMethodIsActive( Varien_Event_Observer $observer ){
        $event           = $observer->getEvent();
        $method          = $event->getMethodInstance();
        $result          = $event->getResult();
		Mage::log($result);
        $currencyCode    = Mage::app()->getStore()->getCurrentCurrencyCode();

    }

}