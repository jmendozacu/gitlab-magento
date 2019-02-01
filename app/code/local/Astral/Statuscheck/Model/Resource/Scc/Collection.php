<?php
class Astral_Statuscheck_Model_Resource_Scc_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('statuscheck/scc');
        $this->_isPkAutoIncrement = false;
    }
}