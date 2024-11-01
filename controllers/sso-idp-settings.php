<?php

    /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */

use MSTUSI\Helper\Utilities\MoIDPUtility;

    global $dbIDPQueries;
	$sp_list 		= $dbIDPQueries->get_sp_list();
	$action 		= isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

    $protocol_inuse = $action=="add_wsfed_app" ? "WSFED" : ( $action=="add_jwt_app" ? "JWT" : "SAML" );

	$goback_url		= remove_query_arg (array('action','id'), sanitize_url($_SERVER['REQUEST_URI']));
	$post_url 		= remove_query_arg (array('action','id'), sanitize_url($_SERVER['REQUEST_URI']));

	$sp_page_url	= esc_url(add_query_arg( array('page' 	=> $spSettingsTabDetails->_menuSlug ), sanitize_url($_SERVER['REQUEST_URI'] )));
	$delete_url		= esc_url(add_query_arg( array('action' => 'delete_sp_settings'	            ), sanitize_url($_SERVER['REQUEST_URI'] )).'&id=');
	$settings_url 	= esc_url(add_query_arg( array('action' => 'show_idp_settings'	            ), sanitize_url($_SERVER['REQUEST_URI'] )).'&id=');

	$saml_doc  		= 'https://plugins.miniorange.com/step-by-step-guide-for-wordpress-saml-idp';
	$wsfed_doc		= 'https://www.miniorange.com/office-365-single-sign-on-(sso)-using-wsfed-protocol';

	$sp_exists 		= TRUE;
	$disabled       = "";

	$TalentLMS_URL = empty(get_site_option('mo_idp_talentlms_url')) ? "" : sanitize_url(get_site_option('mo_idp_talentlms_url')) ;
	$TalentLMS_API = empty(get_site_option('mo_idp_talentlms_api')) ? "" : sanitize_text_field(get_site_option('mo_idp_talentlms_api')) ; 

	if(isset($action) && $action=='delete_sp_settings')
	{
		$sp 		 = $dbIDPQueries->get_sp_data(sanitize_text_field($_GET['id']));
		include MSTUSI_DIR . 'views/idp-delete.php';
	}
	else if(!empty($sp_list))
	{
		$sp 		= $sp_list[0];
		$header		= 'EDIT '.(!empty($sp) ? $sp->mo_idp_sp_name : 'IDP' ).' SETTINGS';
		$sp_exists	= FALSE;
		$test_window= site_url(). '/?option=testConfig'.
                                    '&acs='.$sp->mo_idp_acs_url.
                                    '&issuer='.$sp->mo_idp_sp_issuer.
                                    '&defaultRelayState='.$sp->mo_idp_default_relayState;

        if($sp->mo_idp_protocol_type=="JWT")
            include MSTUSI_DIR . 'views/idp-jwt-settings.php';
        else if($sp->mo_idp_protocol_type=="WSFED")
            include MSTUSI_DIR . 'views/idp-wsfed-settings.php';
        else
            include MSTUSI_DIR . 'views/idp-settings.php';
	}
	else
	{
        $sp          = empty($sp_list) ? '' : $sp_list[0];
        $header		 = $protocol_inuse=="SAML" ? 'ADD NEW SAML SERVICE PROVIDER' :
                        ($protocol_inuse=="JWT" ? 'ADD NEW JWT APP' : 'ADD NEW WS-FED SERVICE PROVIDER' );
		$test_window = '';
        if($protocol_inuse=="JWT")
            include MSTUSI_DIR . 'views/idp-jwt-settings.php';
        else if($protocol_inuse=="WSFED")
            include MSTUSI_DIR . 'views/idp-wsfed-settings.php';
        else
            include MSTUSI_DIR . 'views/idp-settings.php';
	}