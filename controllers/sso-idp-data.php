<?php

	use MSTUSI\Helper\Utilities\MoIDPUtility;
	use MSTUSI\Helper\Utilities\SAMLUtilities;

    // $metadata_url 		= MSTUSI_URL . 'metadata.xml';
	$metadata_url		= home_url( '/?option=mo_idp_metadata' );
    $metadata_dir		= MSTUSI_DIR . "metadata.xml";

	$protocol_type 		= esc_html(get_site_option('mo_idp_protocol'));
	$plugins_url 		= MSTUSI_URL;
	$blogs 				= is_multisite() ? get_sites() : null;
	$site_url 			= is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
	$certificate_url 	= MoIDPUtility::getPublicCertURL();
	$new_certificate_url= MoIDPUtility::getNewPublicCertURL();
	$certificate 		= SAMLUtilities::desanitize_certificate(MoIDPUtility::getPublicCert());
	$idp_settings 		= add_query_arg( array('page' => $spSettingsTabDetails->_menuSlug ), sanitize_url($_SERVER['REQUEST_URI']));
	$idp_entity_id 		= get_site_option('mo_idp_entity_id') ?  esc_html(get_site_option('mo_idp_entity_id')) : $plugins_url;

	$wsfed_command 		= 'Set-MsolDomainAuthentication -Authentication Federated -DomainName '.
                            ' <b>&lt;your_domain&gt;</b> '.
                            '-IssuerUri "'.$idp_entity_id.
                            '" -LogOffUri "'.$site_url.
                            '" -PassiveLogOnUri "'.$site_url.
                            '" -SigningCertificate "'.$certificate.
                            '" -PreferredAuthenticationProtocol WSFED';

	$expired_cert		= get_site_option("mo_idp_new_certs") ? esc_html(get_site_option("mo_idp_new_certs")) : FALSE;

	//Generate the metadata file if no file exists.
	if(!file_exists($metadata_dir) || filesize($metadata_dir) == 0 ) 
	{
        MoIDPUtility::createMetadataFile();
	}
	
	if(!get_site_option("mo_idp_new_certs"))
	{
		MoIDPUtility::createMetadataFile();
	}
	else
	{
		$useNewCert 	= MoIDPUtility::checkCertExpiry();
		if ($useNewCert == TRUE)
			update_site_option ("mo_idp_new_certs", FALSE);
	}

include MSTUSI_DIR . 'views/idp-data.php';