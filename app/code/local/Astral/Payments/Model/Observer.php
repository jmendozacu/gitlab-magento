<?php
class Astral_Payments_Model_Observer{
    public function addPaymentMethod( Varien_Event_Observer $observer ){
        Mage::log(__METHOD__);
        Mage::log($observer->getEvent()->getMethodInstance()->getCode());
    }

}