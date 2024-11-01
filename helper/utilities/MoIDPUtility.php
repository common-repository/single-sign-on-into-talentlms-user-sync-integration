<?php

namespace MSTUSI\Helper\Utilities;

use MSTUSI\Helper\Constants\MoIDPConstants;
use MSTUSI\Helper\SAML2\MetadataGenerator;
use MSTUSI\Exception\InvalidSSOUserException;
use MSTUSI\Exception\InvalidOperationException;

class MoIDPUtility
{

	public static function isBlank( $value )
	{
		if( ! isset( $value ) || empty( $value ) ) return TRUE;
		return FALSE;
	}

	public static function isCurlInstalled()
	{
		if  (in_array  ('curl', get_loaded_extensions())) {
			return 1;
		} else
			return 0;
	}

	public static function startSession()
	{
		if( ! session_id() || session_id() == '' || !isset($_SESSION) ) {
			session_start();
		}
	}

	public static function addSPCookie($issuer)
	{
		if(isset($_COOKIE['mo_sp_count'])){
			for($i=1;$i<=$_COOKIE['mo_sp_count'];$i++){
				if($_COOKIE['mo_sp_' . $i . '_issuer'] == $issuer)
					return;
			}
		}
		$sp_count = isset($_COOKIE['mo_sp_count']) ? sanitize_text_field($_COOKIE['mo_sp_count']) + 1 : 1;
		setcookie('mo_sp_count', $sp_count);
		setcookie('mo_sp_' . $sp_count . '_issuer', $issuer);
	}

	public static function unsetCookieVariables($vars)
	{
		foreach ($vars as $var)
		{
			unset($_COOKIE[$var]);
			setcookie($var, '', time() - 3600);
		}
	}

	public static function getPublicCertPath()
	{
		return MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
	}

	public static function getPrivateKeyPath()
	{
		return MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key';
	}

	public static function getPublicCert()
	{
		return file_get_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt');
	}

	public static function getNewPublicCert()
	{
		return file_get_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt');
	}

	public static function getPrivateKey()
	{
		return file_get_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key');
	}

	public static function getPublicCertURL()
	{
		return MSTUSI_URL . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
	}

	public static function getNewPublicCertURL()
	{
		return MSTUSI_URL . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt';
	}

	public static function mo_debug($message)
	{
		error_log("[MO-MSTUSI-LOG][".date('m-d-Y', time())."]: " . $message);
	}

	public static function createMetadataFile()
	{
		$blogs 		 = is_multisite() ? get_sites() : null;
		$login_url   = esc_url(is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/'));
		$logout_url  = esc_url(is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/'));
		$entity_id   = esc_url(get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSTUSI_URL);
		$certificate = self::getPublicCert();
		$newCert	 = NULL;
		
		if(!get_site_option("mo_idp_new_certs"))
		{
			$newCert = self::getNewPublicCert();
		}

		$generator 	= new MetadataGenerator($entity_id,TRUE,$certificate,$newCert,$login_url,$login_url,$logout_url,$logout_url);
		$metadata 	= $generator->generateMetadata();
		if(MSTUSI_DEBUG) MoIDPUtility::mo_debug("Metadata Generated: " . $metadata);
		$metadataFile = fopen(MSTUSI_DIR . "metadata.xml", "w");
		fwrite($metadataFile,$metadata);
		fclose($metadataFile);
	}

	public static function showMetadata()
	{
		$blogs 		 = is_multisite() ? get_sites() : null;
		$login_url   = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$logout_url  = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$entity_id   = get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSTUSI_URL;
		$certificate = self::getPublicCert();
		$newCert	 = NULL;
		
		if(!get_site_option("mo_idp_new_certs"))
		{
			$newCert = self::getNewPublicCert();
		}

		$generator 	= new MetadataGenerator($entity_id,TRUE,$certificate,$newCert,$login_url,$login_url,$logout_url,$logout_url);
		$metadata 	= $generator->generateMetadata();
		
		if(ob_get_contents())
			ob_clean();
		
		header( 'Content-Type: text/xml' );
		echo esc_html($metadata);
		exit;
	}

	public static function generateRandomAlphanumericValue($length)
	{
		$chars = "abcdef0123456789";
		$chars_len = strlen($chars);
		$uniqueID = "";
		for ($i = 0; $i < $length; $i++)
			$uniqueID .= substr($chars,rand(0,15),1);
		return 'a'.$uniqueID;
	}

	/**
	 * This function is created to check whether a person is having 
	 * admin access or not
	 * 
	 * @return True or False
	 */
	public static function isAdmin(){
		$user = wp_get_current_user();
		if ( in_array( 'administrator', (array) $user->roles )  ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * This function is created to update the certificates. The validity of the certificates
	 * is set to 365 days, and this function will replace the existing certificates with the 
	 * new certificates. This will also update the plugin metadata XML file once the certificates
	 * are updated.
	 *
	 * @return void
	 */
	public static function useNewCerts()
	{
		$oldCert = fopen(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt',"w");
		$newCert = fopen(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt',"r");
		$oldKey = fopen(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key',"w");
		$newKey = fopen(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.key',"r");

		file_put_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt', file_get_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt'));
		file_put_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key', file_get_contents(MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.key'));

		fclose($oldCert);
		fclose($newCert);
		fclose($oldKey);
		fclose($newKey);

		update_site_option("mo_idp_new_certs",TRUE);

		$metadata_dir		= MSTUSI_DIR . "metadata.xml";
		if(file_exists($metadata_dir) && filesize($metadata_dir) > 0 ) {
			unlink($metadata_dir);
            MoIDPUtility::createMetadataFile();
		}
	}

	/**
	 * This function compares the expiry of the old and the new certificate files in the plugin.
	 * If the new certificate expiry is longer than the current certificate, it will return True, else False.
	 *
	 * @return boolean
	 */
	public static function checkCertExpiry()
	{
        $currentCert = MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
        $newCert = MSTUSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt';

        $currentCertExpiry =  (openssl_x509_parse(file_get_contents($currentCert))['validTo_time_t']) - time();
		$newCertExpiry =  (openssl_x509_parse(file_get_contents($newCert))['validTo_time_t']) - time();

        if ( $newCertExpiry > $currentCertExpiry )
            return TRUE;
        else
            return FALSE;
	}

    public static function iclv()
    {
        return TRUE;
    }

	// SP MetaData Upload 
	public static function mo_saml_wp_remote_get($url, $args = array()){
		$response = wp_remote_get($url, $args);
	
		if(!is_wp_error($response)){
				return $response;
		} else {
				return null;   
		}
	} 
}