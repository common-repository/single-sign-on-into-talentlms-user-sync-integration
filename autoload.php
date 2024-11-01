<?php

use MSTUSI\Helper\Utilities\TabDetails;
use MSTUSI\Helper\Utilities\Tabs;
use MSTUSI\SplClassLoader;

define('MSTUSI_VERSION', '1.0.0');
define('MSTUSI_DB_VERSION', '1.4');
define('MSTUSI_DIR', plugin_dir_path(__FILE__));
define('MSTUSI_URL', plugin_dir_url(__FILE__));
define('MSTUSI_CSS_URL', MSTUSI_URL . 'includes/css/mo_idp_style.min.css?version='.MSTUSI_VERSION);
define('MSTUSI_JS_URL', MSTUSI_URL . 'includes/js/settings.min.js?version='.MSTUSI_VERSION);
define('MSTUSI_ICON', MSTUSI_URL . 'includes/images/miniorange_icon.png');
define('MSTUSI_LOGO_URL', MSTUSI_URL . 'includes/images/logo.png');
define('MSTUSI_LOADER', MSTUSI_URL . 'includes/images/loader.gif');
define('MSTUSI_TEST', FALSE);
define('MSTUSI_DEBUG', FALSE);
define('MSTUSI_LK_DEBUG', FALSE);

includeLibFiles();

function includeLibFiles()
{
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecurityKey")) include 'helper/common/XMLSecurityKey.php';
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecEnc")) include 'helper/common/XMLSecEnc.php';
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecurityDSig")) include 'helper/common/XMLSecurityDSig.php';
}


include "SplClassLoader.php";
/** @var SplClassLoader $idpClassLoader */
$idpClassLoader = new SplClassLoader('MSTUSI', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
$idpClassLoader->register();


