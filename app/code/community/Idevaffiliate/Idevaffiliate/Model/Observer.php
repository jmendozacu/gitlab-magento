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
                    Mage::log($data, false, 'idev_conversion_log_'.date('Y-m-d').'.log');
                    $this->curl_post_async($tracking_url,$data);
					//$ch = curl_init();
					//curl_setopt($ch, CURLOPT_URL, $tracking_url);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					//$return = curl_exec($ch);
					//curl_close($ch);
				}
			}

    function curl_post_async($url, $params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);

        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }


}