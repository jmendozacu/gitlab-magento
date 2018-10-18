<?php 

class Born_Package_Helper_Aw_Sarp2_Product extends AW_Sarp2_Helper_Product
{

	//Add message variable
	public function addProductOptionValue(
		Mage_Catalog_Model_Product_Option $productOption,
		$id, $title, $message = null, $information = null
		)
	{
        /**
         * @var Mage_Catalog_Model_Product_Option_Value $value
         */
        

        $value = Mage::getModel('catalog/product_option_value');
        $value->setData(
        	array(
        		'option_type_id'     => $id,
        		'option_id'          => $productOption->getOptionId(),
        		'sku'                => null,
        		'sort_order'         => '0',
        		'default_title'      => $title,
        		'store_title'        => $title,
        		'title'              => $title,
        		'message'            => $message,
                'information'        => $information,
        		'default_price'      => null,
        		'default_price_type' => null,
        		'store_price'        => null,
        		'store_price_type'   => null,
        		'price'              => null,
        		'price_type'         => null,
        		)
        	);
        $value->setProduct($productOption->getProduct());
        $productOption->addValue($value);
        return $value;
    }
}

?>