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
                $BypassObject = Mage::getModel('statuscheck/scc')->load($increment_id);
                $bpd = $BypassObject->getData();
                    if(!isset($bpd)||empty($bpd)){
                        Mage::log(__METHOD__.' '.__LINE__.' Save new bypass flag. IncrementId: '.$increment_id, false, 'Order_Process.log');
                        $NewBypassObject = Mage::getModel('statuscheck/scc');
                        $NewBypassObject->setBypass_score(1);
                        $NewBypassObject->setIncrement_id($increment_id);
                        $NewBypassObject->setCheck_count(1);
                        $NewBypassObject->save();
                        Mage::log($NewBypassObject, false, 'Order_Process.log');
                    }elseif(isset($bpd)&&!empty($bpd)){
                        Mage::log(__METHOD__.' '.__LINE__.' Save bypass flag. IncrementId: '.$increment_id, false, 'Order_Process.log');
                        $bps = $BypassObject->getBypass_score();
                        $cc = $BypassObject->getCheck_count();
                            if (isset($bps) && !empty($bps)&&$bps==1) {
                                Mage::log(__METHOD__.' '.__LINE__.' Save bypass flag. Flag exists. IncrementId: '.$increment_id, false, 'Order_Process.log');
                            $bp_state = true;
                            } else {
                                Mage::log(__METHOD__.' '.__LINE__.' Save bypass flag. IncrementId: '.$increment_id, false, 'Order_Process.log');
                            $cc++;
                            $BypassObject->setCheck_count($cc);
                            $BypassObject->setBypass_score(1);
                            $BypassObject->save();
                            $bp_state = true;
                            }
                    }

                }
            } catch(Exception $e) {
                Mage::log(__METHOD__.' Error processing statuscheck');
            }
			    exit;
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

		$this->_redirectReferer();                        
        }	
}