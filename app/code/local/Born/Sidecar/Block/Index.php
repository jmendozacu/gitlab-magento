<?php   
class Born_Sidecar_Block_Index extends Mage_Core_Block_Template{   

	public function getProductIdConfig() {
		return Mage::getStoreConfig('sidecar_pdp/sidecar_pdp_group/product_id');
	}
	public function getProduct() {
		return Mage::registry('current_product');
	}
	public function getProductId() {
		$config = $this->getProductIdConfig();
		$product = $this->getProduct();
		switch($config) {
			case 1: $val = $product->getSku(); break;
			case 2: $val = $product->getId(); break;
		}
		return preg_replace('/[^A-Za-z0-9\-]/', '', $val);
	}
	
	public function getOrderDetails() {
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);
		
		return $order;
	}
	
	public function getRuleDetails($ruleId) {
		//Load the rule object
		//$rule = Mage::getModel('catalogrule/rule')->load($ruleId);
		return $rule = Mage::getModel('salesrule/rule')->load($ruleId);
		
	}
}