<?php

use MSTUSI\Helper\Utilities\MoIDPUtility;
use MSTUSI\Helper\Utilities\TabDetails;
use MSTUSI\Helper\Utilities\Tabs;

$verified 	= MoIDPUtility::iclv();
$controller = MSTUSI_DIR . 'controllers/';
/** @var TabDetails $tabs */
$tabs = TabDetails::instance();
$tabDetails = $tabs->_tabDetails;
$parentSlug = $tabs->_parentSlug;

/** @var \MSTUSI\Helper\Utilities\PluginPageDetails $metadataTabDetails */
$metadataTabDetails = $tabDetails[Tabs::METADATA];
/** @var \MSTUSI\Helper\Utilities\PluginPageDetails $spSettingsTabDetails */
$spSettingsTabDetails = $tabDetails[Tabs::IDP_CONFIG];
/** @var \MSTUSI\Helper\Utilities\PluginPageDetails $attrMapTabDetails */
$attrMapTabDetails = $tabDetails[Tabs::ATTR_SETTINGS];



include MSTUSI_DIR 	 . 'views/common-elements.php';
include MSTUSI_DIR 	 . 'controllers/sso-idp-navbar.php';

if( isset( $_GET[ 'page' ]))
{
    switch(sanitize_text_field($_GET['page']))
    {
        case $metadataTabDetails->_menuSlug:
            include $controller . 'sso-idp-data.php';			break;
        case $spSettingsTabDetails->_menuSlug:
            include $controller . 'sso-idp-settings.php';		break;
        case $attrMapTabDetails->_menuSlug:
            include $controller . 'sso-attr-settings.php';		break;
        case $parentSlug:
            include $controller . 'plugin-details.php';         break; 
    }
}



