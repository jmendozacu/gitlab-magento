<?php

/**
 * Class Born_Package_Adminhtml_IndexController
 */
class Born_Package_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @return bool
     * @throws Exception
     */
    public function bypassOrderAction(){
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
                $bpd_iid = $BypassObject->getIncrement_id();
					if(!isset($bpd_iid)||empty($bpd_iid)){
                        $NewBypassObject = Mage::getModel('statuscheck/scc');
                        $NewBypassObject->setBypass_score(1);
                        $NewBypassObject->setIncrement_id($increment_id);
                        $NewBypassObject->setCheck_count(1);
                        $NewBypassObject->save();
                    }elseif(isset($bpd_iid)&&!empty($bpd_iid)){
                        $bps = $BypassObject->getBypass_score();
                        $cc = $BypassObject->getCheck_count();
                            if (isset($bps) && !empty($bps)&&$bps==1) {
                            $bp_state = true;
                            } else {
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
			    if($bp_state) {
				$comments = $order->getStatusHistoryCollection(true)->getLastItem();
				$state='';
				$status = 'processing';
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