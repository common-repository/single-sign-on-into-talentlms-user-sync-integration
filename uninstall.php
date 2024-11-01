<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	    exit();

		global $wpdb;
		delete_site_option('mo_idp_message');
		delete_site_option('mo_saml_idp_plugin_version');
		delete_site_option('mo_idp_usr_lmt');
		delete_site_option('mo_idp_entity_id');
		delete_site_option('mo_idp_talentlms_url');
		delete_site_option('mo_idp_talentlms_api');

		//plugin settings

		$sql =  is_multisite() ? $wpdb->prepare("DROP TABLE mo_sp_attributes") : $wpdb->prepare("DROP TABLE %1s".'mo_sp_attributes', $wpdb->prefix);
		$wpdb->query($sql);

		$sql = is_multisite() ? $wpdb->prepare("DROP TABLE mo_sp_data") : $wpdb->prepare("DROP TABLE %1s".'mo_sp_data' , $wpdb->prefix);
		$wpdb->query($sql);
?>