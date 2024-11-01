<?php

use MSTUSI\Helper\Constants\MoIDPConstants;
use MSTUSI\Helper\Utilities\MoIDPUtility;

$idp_settings	= add_query_arg( array('page' => $spSettingsTabDetails->_menuSlug   ), sanitize_url($_SERVER['REQUEST_URI']));
$sp_settings	= add_query_arg( array('page' => $metadataTabDetails->_menuSlug	    ), sanitize_url($_SERVER['REQUEST_URI']));
$attr_settings	= add_query_arg( array('page' => $attrMapTabDetails->_menuSlug		), sanitize_url($_SERVER['REQUEST_URI'] ));
$help_url       = MoIDPConstants::FAQ_URL;

$active_tab 	= sanitize_text_field($_GET['page']);

$useNewCert 	= MoIDPUtility::checkCertExpiry();
if ($useNewCert == TRUE)
    update_site_option ("mo_idp_new_certs", FALSE);

include MSTUSI_DIR . 'views/navbar.php';