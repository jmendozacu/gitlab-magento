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
					    foreach($items as $i) {
                        $skus[] = $i->getSku();
                        }
                        if(isset($_SERVER['X-Forwarded-For'])){
                        $ip = $_SERVER['X-Forwarded-For'];
                        }else{
                        $ip = $_SERVER['REMOTE_ADDR'];
                        }
                    $products_purchased = implode('|', $skus);
                    $tracking_url = Mage::getStoreConfig('idevaffiliate/idevaffiliate/idev_tracking_url') . 'sale.php';
                    $data = array();
					$data['profile'] = '54';
                    $data['ip_address'] = $ip;
                    $data['idev_saleamt'] = $idev_saleamt;
                    $data['idev_ordernum'] = $_orderId;
                    $data['products_purchased'] = $products_purchased;
                    $data['coupon_code'] = $coupon_code;
                    $query = http_build_query($data);
					Mage::log($tracking_url, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
                    Mage::log($query, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
                    Mage::log($tracking_url."?".$query, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $tracking_url."?".$query);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$return = curl_exec($ch);
					curl_close($ch);
				}
			}

}