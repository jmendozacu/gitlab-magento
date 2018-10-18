<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Footer extends Mage_Core_Block_Template
{

    protected $_copyright;

    protected function _construct(){
        $this->addData(array('cache_lifetime' => false));
        $this->addCacheTag(array(
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Cms_Model_Block::CACHE_TAG
        ));
    }

    public function getCacheKeyInfo(){
        return array(
            'PAGE_FOOTER',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->isLoggedIn()
        );
    }

    public function setCopyright($copyright){
        $this->_copyright = $copyright;
        return $this;
    }

    public function getCopyright(){
            if (!$this->_copyright) {
            $this->_copyright = Mage::getStoreConfig('design/footer/copyright');
            }
        return $this->_copyright;
    }

    public function getChildHtml($name='', $useCache=true, $sorted=true){
        return parent::getChildHtml($name, $useCache, $sorted);
    }

    public function addCss($name, $params = ""){
        $this->addItem('skin_css', $name, $params);
        return $this;
    }

    public function addJs($name, $params = ""){
        $this->addItem('js', $name, $params);
        return $this;
    }

    public function addCssIe($name, $params = ""){
        $this->addItem('skin_css', $name, $params, 'IE');
        return $this;
    }

    public function addJsIe($name, $params = ""){
        $this->addItem('js', $name, $params, 'IE');
        return $this;
    }


    public function addLinkRel($rel, $href){
        $this->addItem('link_rel', $href, 'rel="' . $rel . '"');
        return $this;
    }

    public function addItem($type, $name, $params=null, $if=null, $cond=null){
            if ($type==='skin_css' && empty($params)) {
            $params = 'media="all"';
            }
        $this->_data['items'][$type.'/'.$name] = array(
            'type'   => $type,
            'name'   => $name,
            'params' => $params,
            'if'     => $if,
            'cond'   => $cond,
       );
        return $this;
    }

    public function removeItem($type, $name){
        unset($this->_data['items'][$type.'/'.$name]);
        return $this;
    }

    public function getCssJsHtml(){
        $lines  = array();
        $html   = '';
            if(isset($this->_data)&&!empty($this->_data)&&isset($this->_data['items'])&&!empty($this->_data['items'])){
                foreach ($this->_data['items'] as $item) {
                    if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                    continue;
                    }
                $if     = !empty($item['if']) ? $item['if'] : '';
                $params = !empty($item['params']) ? $item['params'] : '';
                    switch ($item['type']) {
                        case 'js':        // js/*.js
                        case 'skin_js':   // skin/*/*.js
                        case 'js_css':    // js/*.css
                        case 'skin_css':  // skin/*/*.css
                        $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                        break;
                        default:
                        $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                        break;
                    }
                }
            $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
            $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
                foreach ($lines as $if => $items) {
                    if (empty($items)) {
                    continue;
                    }
                    if (!empty($if)) {
                        if (strpos($if, "><!-->") !== false) {
                        $html .= $if . "\n";
                        } else {
                        $html .= '<!--[if '.$if.']>' . "\n";
                        }
                    }
                $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
                    empty($items['js_css']) ? array() : $items['js_css'],
                    empty($items['skin_css']) ? array() : $items['skin_css'],
                    $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
                    );
                $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                    );
                    if (!empty($items['other'])) {
                    $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
                    }
                    if (!empty($if)) {
                        if (strpos($if, "><!-->") !== false) {
                        $html .= '<!--<![endif]-->' . "\n";
                        } else {
                        $html .= '<![endif]-->' . "\n";
                        }
                    }
                }
            }
        return $html;
    }

    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe){
        $params = $itemParams ? ' ' . $itemParams : '';
        $href   = $itemName;
            switch ($itemType) {
                case 'rss':
                $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />',
                    $href, $params
                );
                break;
                case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
                break;
            }
    }

    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null){
        $designPackage = Mage::getDesign();
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = array();
            if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
            }
            foreach ($staticItems as $params => $rows) {
                foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
                }
            }
            foreach ($skinItems as $params => $rows) {
                foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $designPackage->getFilename($name, array('_type' => 'skin'))
                    : $designPackage->getSkinUrl($name, array());
                }
            }
        $html = '';
            foreach ($items as $params => $rows) {
            $mergedUrl = false;
                if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
                }
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
                if ($mergedUrl) {
                $html .= sprintf($format, $mergedUrl, $params);
                } else {
                    foreach ($rows as $src) {
                    $html .= sprintf($format, $src, $params);
                    }
                }
            }
        return $html;
    }

    protected function _prepareOtherHtmlHeadElements($items){
        return implode("\n", $items);
    }

    public function getChunkedItems($items, $prefix = '', $maxLen = 450){
        $chunks = array();
        $chunk  = $prefix;
            foreach ($items as $item) {
                if (strlen($chunk.','.$item)>$maxLen) {
                $chunks[] = $chunk;
                $chunk = $prefix;
                }
            $chunk .= ','.$item;
            }
        $chunks[] = $chunk;
        return $chunks;
    }

    public function getContentType(){
            if (empty($this->_data['content_type'])) {
            $this->_data['content_type'] = $this->getMediaType().'; charset='.$this->getCharset();
            }
        return $this->_data['content_type'];
    }

    public function getMediaType(){
            if (empty($this->_data['media_type'])) {
            $this->_data['media_type'] = Mage::getStoreConfig('design/head/default_media_type');
            }
        return $this->_data['media_type'];
    }

    public function getCharset(){
            if (empty($this->_data['charset'])) {
            $this->_data['charset'] = Mage::getStoreConfig('design/head/default_charset');
            }
        return $this->_data['charset'];
    }    
}