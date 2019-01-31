<?php
class Astral_Statuscheck_Model_Resource_Scc extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('statuscheck/scc', 'sc_id');
    }
}