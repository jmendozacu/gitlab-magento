<?php
/**
 * Class Astral_BlockCache_Catalog_Product_View
 */
class WeltPixel_LayeredNavigation_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    /**
     *
     */
    protected function _construct()
    {
        $this->addData(array(
        	//'cache_key' 		=>	
            //'cache_lifetime'  => 
            'cache_tags'        => array(WeltPixel_LayeredNavigation_Catalog_Model_Layer::CACHE_TAG . "_" . $this->getCurrentCategory()->getId()),
        ));
    }
    /**
     * @return mixed|string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            $cacheKey = $this->getNameInLayout().'_STORE'.Mage::app()->getStore()->getId().'_CATEGORY'.$this->getLayer()->getId();
        	//.'_'.Mage::getDesign()->getPackageName().'_'.Mage::getDesign()->getTheme('template'). //_PACKAGE_THEME ?
        	$this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }
    /**
     * @return int|null
     */
    public function getCacheLifetime()
    {	  //to prevent sub-blocks caching
//    	  if($this->getNameInLayout()!='category.info') return null;
    	  //return false; //false creates default lifetime (7200)
    	  return 9999999999;
    }
}