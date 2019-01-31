<?php
class Astral_Statuscheck_Model_Observer {
	
	public function checkStatus(){
		Mage::log(__METHOD__, false, 'Order_Process.log'); 
        $query  = "SELECT * ";
        $query .= "FROM `sales_flat_order` as t1 ";
        $query .= "JOIN `signifyd_connect_case` as t2 ";
        $query .= "ON t1.increment_id = t2.order_increment ";
        $query .= "WHERE t1.status = 'processing' ";
		$query .= "OR t1.status = 'pending' ";
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read'); 		
        $collection = $readConnection->fetchall($query);
            if(is_array($collection) || is_object($collection)){
			    if(isset($collection) && !empty($collection)) {
                $collection_count = count($collection);
                    if ($collection_count > 0) {
                        foreach ($collection as $order) {
                        $bpf = $this->checkForBypassFlag($order);
                        $bypassFlag = $this->checkForBypassFlag($order);
                            if (isset($order['score']) && !empty($order['score'])) {
                                if (!$order['bypass_score']) {
                                    if (!$bpf && $order['score'] < 700) {
                                    Mage::log(__METHOD__ . ' Order set to hold. IncrementID: '.$order['increment_id'].' Score: '.$order['score'], false, 'Order_Process.log');
                                    //$this->setToHold($order);
                                    }
                                }
                            }
                        }
                    }
                }
			}
	}				

	public function checkForBypassFlag($order){
	    $bypassFlag = Mage::getModel('statuscheck/scc')->load($order['increment_id']);
        $bpf = $bypassFlag->getBypass_score();
	        if(isset($bpf)&&!empty($bpf)){
	            $bp_state = true;
            }else{
                $bp_state = false;
            }
	    return $bp_state;
    }

	public function setToHold($order){
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
		$orderObject->hold();
		$orderObject->setState(Mage_Sales_Model_Order::STATE_HOLDED);
		$orderObject->setStatus(Mage_Sales_Model_Order::STATE_HOLDED);			
		$orderObject->addStatusHistoryComment("Signifyd: order held because of low score. ".$order['score']);
		$orderObject->save();  
	}
		
	public function setToProcessing($order){
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
		$orderObject->hold();
		$orderObject->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
		$orderObject->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);			
		$orderObject->addStatusHistoryComment("Order moved to processing due to satisfactory score. ".$order['score']);
		$orderObject->save();  
	}
}