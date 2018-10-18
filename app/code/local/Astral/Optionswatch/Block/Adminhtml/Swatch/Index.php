<?php
class Astral_Optionswatch_Block_Adminhtml_Swatch_Index extends Mage_Adminhtml_Block_Widget_Grid_Container{
	
    public function __construct(){		
		$this->_controller = 'adminhtml_swatch_index';
		$this->_blockGroup = 'optionswatch';
    	$this->_headerText = Mage::helper('optionswatch')->__('Product Attribute Options'); //mark
   		$this->_addButtonLabel = Mage::helper('optionswatch')->__('New Option');
        parent::__construct();
    }
 
}