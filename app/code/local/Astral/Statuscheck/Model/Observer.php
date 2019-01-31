<?php
class Astral_Statuscheck_Model_Observer {
	
	public function checkStatus(){
		Mage::log(__METHOD__, false, 'Order_Process.log'); 
        $query  = "SELECT * ";
        $query .= "FROM `sales_flat_order` as t1 ";
        $query .= "JOIN `signifyd_connect_case` as t2 ";
        $query .= "ON t1.increment_id = t2.order_increment ";
        $query .= "JOIN `astral_statuscheck_scc` as t3 ";
        $query .= "ON t2.case_id = t3.case_id ";		
        $query .= "WHERE t1.status = 'processing' ";
		$query .= "OR t1.status = 'pending' ";
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read'); 		
        $collection = $readConnection->fetchall($query);
			if(isset($collection) && !empty($collection)){
			$collection_count = count($collection);
				if (count($collection_count) > 0) {
					foreach ($collection as $order) {	
					Mage::log('Order : '.$order['increment_id'].' Score: '.$order['score'].' Check Count: '.$order['check_count'].' Bypass Score: '.$order['bypass_score'], false, 'Order_Process.log');  
						if(isset($order['score'])&&!empty($order['score']){
							if(!$order['bypass_score']){
								if($order['score'] < 700){
								Mage::log(__METHOD__.' setToHold', false, 'Order_Process.log'); 	
								//$this->setToHold($order);
								}	
							}		
						}else{
						$orderFlag = Mage::getModel('statuscheck/scc')->load($order['sc_id']);
						$orderFlagData = $orderFlag->getData();
						Mage::log($orderFlagData, false, 'Order_Process.log');
						// Increment check count for this case
						}
					}
				}			
			}
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