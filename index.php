<?php
//$_SERVER['MAGE_IS_DEVELOPER_MODE']=true;
// Pineapple is not meant for pizza.
mb_internal_encoding("UTF-8");
    if (version_compare(phpversion(), '5.3.0', '<')===true) {
    echo '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
        <h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
        Whoops, it looks like you have an invalid PHP version.</h3></div>';
    exit;
    }
    if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {    
//    $includeMagentoDebugger = '../magento_debugger/index.php';
        if (!defined('MAGENTO_DEBUGGER_VERSION') && is_file($includeMagentoDebugger)){
  //      require_once($includeMagentoDebugger);
  //      return;
        }
    }
define('MAGENTO_ROOT', getcwd());
$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
    if (file_exists($compilerConfig)) {
    include $compilerConfig;
    }
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';
    if (!file_exists($mageFilename)) {
        if (is_dir('downloader')) {
        header("Location: downloader");
        } else {
        echo $mageFilename." was not found";
        }
    exit;
    }
    if (file_exists($maintenanceFile)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
    }
require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;
    if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Varien_Profiler::enable();
    Mage::setIsDeveloperMode(true);
    ini_set('display_errors', 1);
    }
umask(0);
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
Mage::run($mageRunCode, $mageRunType);
