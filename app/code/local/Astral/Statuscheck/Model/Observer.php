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
                Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                $collection_count = count($collection);
                if ($collection_count > 0) {
                    Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                    $bpf = checkForBypassFlag($order);
                    Mage::log(__METHOD__ . ' ' . __LINE__ . ' bpf: ' . $bpf, false, 'Order_Process.log');
                    foreach ($collection as $order) {
                        $bypassFlag = $this->checkForBypassFlag($order);
                        Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                        Mage::log('Order : ' . $order['increment_id'] . ' Score: ' . $order['score'] . ' Check Count: ' . $order['check_count'] . ' Bypass Score: ' . $order['bypass_score'], false, 'Order_Process.log');
                        if (isset($order['score']) && !empty($order['score'])) {
                            Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                            if (!$order['bypass_score']) {
                                Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                                if ($order['score'] < 700) {
                                    Mage::log(__METHOD__ . ' ' . __LINE__, false, 'Order_Process.log');
                                    Mage::log(__METHOD__ . ' setToHold', false, 'Order_Process.log');
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
	    return $bypassFlag->getBypass_score();
    }

	public function setToHold($order){
		Mage::log(__METHOD__, false, 'Order_Process.log'); 
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
		$orderObject->hold();
		$orderObject->setState(Mage_Sales_Model_Order::STATE_HOLDED);
		$orderObject->setStatus(Mage_Sales_Model_Order::STATE_HOLDED);			
		$orderObject->addStatusHistoryComment("Signifyd: order held because of low score. ".$order['score']);
		$orderObject->save();  
	}
		
	public function setToProcessing($order){
		Mage::log(__METHOD__, false, 'Order_Process.log'); 
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
		$orderObject->hold();
		$orderObject->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
		$orderObject->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);			
		$orderObject->addStatusHistoryComment("Order moved to processing due to satisfactory score. ".$order['score']);
		$orderObject->save();  
	}
}