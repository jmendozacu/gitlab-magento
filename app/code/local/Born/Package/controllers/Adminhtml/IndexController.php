<?php
class Born_Package_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function changeStatusAction(){
		$data = $this->getRequest()->getParams();
			if(isset($data['order_id'])){
			$order_id = $data['order_id'];
			}else{
			throw new Exception('No order ID supplied.');
			}		
			try {
			$order = Mage::getModel('sales/order')->load($data['order_id']);
				if($order->getId()) {
						
				$comments = $order->getStatusHistoryCollection(true)->getLastItem();
				$state='';
				$status = 'processing';
				$system_status = Mage::getResourceModel('sales/order_status_collection')->addStatusFilter($status);
				$system_data = $system_status->getData();
				$state = 'processing';
				$label = 'Processing';
				$order->setState($state, true);
				$order->setStatus($status, true);
				$order->save();
				Mage::getSingleton('adminhtml/session')->addSuccess('Order status changed to '.$label);
				}
			} catch(Exception $e) {
			//Mage::log($e->getMessage(),NULL,'order_review_exception.log');
			Mage::getSingleton('adminhtml/session')->addError('Some error occured');
			}
		$this->_redirectReferer();
    }
	
	public function cancelOrderAction(){
		$data = $this->getRequest()->getParams();
			if(isset($data['order_id'])){
			$order_id = $data['order_id'];
			}else{
			throw new Exception('No order ID supplied.');
			}
			try {
			$order = Mage::getModel('sales/order')->load($data['order_id']);
				if($order->getId()) {
				$comments = $order->getStatusHistoryCollection(true)->getLastItem();
				$state='';
				$status = 'canceled';
				$system_status = Mage::getResourceModel('sales/order_status_collection')->addStatusFilter($status);
				$system_data = $system_status->getData();
				$state = 'canceled';
				$label = 'Canceled';
				$order->setState($state, true);
				$order->setStatus($status, true);
				$order->save();
				Mage::getSingleton('adminhtml/session')->addSuccess('Order status changed to '.$label);					
				}				
			} catch(Exception $e) {
			//Mage::log($e->getMessage(),NULL,'order_review_exception.log');
			Mage::getSingleton('adminhtml/session')->addError('Some error occured');
			}
		$this->_redirectReferer();				
	}
	public function bypassOrderAction(){
        Mage::log(__METHOD__, false, 'Order_Process.log');
        $data = $this->getRequest()->getParams();
			if(isset($data['order_id'])){
			$order_id = $data['order_id'];
			}else{
			throw new Exception('No order ID supplied.');
			}
			try {
			$order = Mage::getModel('sales/order')->load($data['order_id']);
			$increment_id = $order->getIncrement_id();
			    if(isset($increment_id)&&!empty($increment_id)) {
                $bypassFlag = Mage::getModel('statuscheck/scc')->load($increment_id);
                $bpf = $bypassFlag->getBypass_score();
                $cc = $bypassFlag->getCheck_count();
                    if (isset($bpf) && !empty($bpf)&&$bpf==1) {
                    $bp_state = true;
                    } else {
                    $cc++;
                    $bypassFlag->setBypass_score(1);
                    $bypassFlag->setIncrement_id($increment_id);
                    $bypassFlag->setCheck_count($cc);
                    $bypassFlag->save();
                    $bp_state = true;
                    }
                }
			    return $bp_state;

                Mage::log(__METHOD__.' increment_id '.$increment_id, false, 'Order_Process.log');
                Mage::log(__METHOD__.' bp_state '.$bp_state, false, 'Order_Process.log');
                if($order->getId()) {
				$comments = $order->getStatusHistoryCollection(true)->getLastItem();
				$state='';
				$status = 'bypassscore';
				$system_status = Mage::getResourceModel('sales/order_status_collection')->addStatusFilter($status);
				$system_data = $system_status->getData();
				$state = 'processing';
				$label = 'Bypass Score';
				$order->setState($state, true);
				$order->setStatus($status, true);
				$order->save();
				Mage::getSingleton('adminhtml/session')->addSuccess('Order status changed to '.$label);					
				}				
			} catch(Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e);
			}
		$this->_redirectReferer();                        
        }	
}