<?php
class Idevaffiliate_Idevaffiliate_Model_Observer
{

			public function track(Varien_Event_Observer $observer)
			{
				$orderIds = $observer->getData('order_ids');
				foreach($orderIds as $_orderId){	
					$order_details = Mage::getModel('sales/order')->load($_orderId);
					$idev_subtotal = $order_details->base_subtotal; 
					$idev_discount = $order_details->base_discount_amount;
					$idev_saleamt = $idev_subtotal + $idev_discount;
					$coupon_code = $order_details->coupon_code;
					
					$items = $order_details->getAllVisibleItems();
					$skus = array();
					foreach($items as $i):
						$skus[] = $i->getSku();
					endforeach;
					$products_purchased = implode('|', $skus);
										
					$tracking_url = Mage::getStoreConfig('idevaffiliate/idevaffiliate/idev_tracking_url') . 'sale.php';
					$tracking_fields = 'profile=54&ip_address='.$_SERVER['REMOTE_ADDR'].'&idev_saleamt='.$idev_saleamt.'&idev_ordernum='.$_orderId.'&products_purchased='.$products_purchased.'&coupon_code='.$coupon_code;
					
					//mail('farazahmedmemon@gmail.com', 'Tracking Pixel', $tracking_url.'?'.$tracking_fields, 'From: farazahmedmemon@gmail.com');
					////Mage::log($tracking_url, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
                                        ////Mage::log($tracking_fields, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $tracking_url);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $tracking_fields);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$return = curl_exec($ch);
					curl_close($ch);
				}
			}
		
}
