<?php
class Astral_Optionswatch_Model_Mysql4_Swatch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('optionswatch/swatch');
    }
    
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, 'id'); //mark
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}