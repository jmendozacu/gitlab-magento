<?php
/**
 * Class Astral_BlockCache_Catalog_Product_View
 */
class Astral_BlockCache_Block_Catalog_Category_View extends Mage_Catalog_Block_Category_View
{
    /**
     *
     */
    protected function _construct()
    {
        $this->addData(array(
        	'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG . "_" . $this->getCurrentCategory()->getId()),
        ));
    }
    /**
     * @return mixed|string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            $cacheKey = $this->getNameInLayout().'_STORE'.Mage::app()->getStore()->getId().'_CATEGORY'.$this->getCurrentCategory()->getId();
        	$this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }
    /**
     * @return int|null
     */
    public function getCacheLifetime()
    {	  
    	  if($this->getNameInLayout()!='category.info') return null;
    	  return 9999999999;
    }
}