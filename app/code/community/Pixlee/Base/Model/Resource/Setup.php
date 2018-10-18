<?php

class Pixlee_Base_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup {
	public function send_to_pardot() {
		$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
		$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin

		if ($from_email != "owner@example.com") {
		    $data = json_encode(array(
		        "name" => $from_name,
		        "email" => $from_email,
		        "company" => $store_name,
		        "source" => "magento_1_download"
		    ));

		    $ch = curl_init(); 
		    curl_setopt($ch, CURLOPT_URL, "https://app.pixlee.com/leads/add");
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		        'Content-Type: application/json',                                                                                
		        'Content-Length: ' . strlen($data))                                                                       
		    );                                       

		    $output = curl_exec($ch);
		    curl_close($ch);
		}
	}
}