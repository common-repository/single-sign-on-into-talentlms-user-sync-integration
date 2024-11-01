<?php

namespace MSTUSI\Helper\Constants;

class MoIDPMessages
{
	//General Flow Messages
	const REQUIRED_FIELDS  				= 'Please fill in the required fields.';
	const ERROR_OCCURRED 				= 'An error occured while processing your request. Please try again.';
	const INVALID_OP 					= 'Invalid Operation. Please Try Again.';

	//Save Settings Error
	const ISSUER_EXISTS 				= 'You seem to already have a Service Provider for that issuer configured under : <i>{{name}}</i>';
	const SP_EXISTS						= 'You have already configured a Service Provider under that name.';
	const INVALID_ENCRYPT_CERT			= 'You have not provided a certificate for encrypted assertion.';
	const NO_SP_CONFIG					= 'Please Configure a Service Provider.';

	const SETTINGS_SAVED				= 'Settings saved successfully.';
	const SP_DELETED 					= 'Service Provider settings deleted successfully.';
	const IDP_ENTITY_ID_CHANGED 		= 'IdP Entity ID changed successfully.';
	const IDP_ENTITY_ID_NULL			= 'IdP EntityID/Issuer cannot be NULL.';

	const SP_NAME_REQUIRED              = 'Service Provider Name cannot be NULL.';
	const SP_NAME_INVALID               = 'Please match the requested format for Service Provider Name. Only alphabets, numbers and underscore is allowed.';
	const METADATA_INVALID              = 'Please provide a valid Metadata File/URL.';
	const METADATA_FILE_INVALID         = 'Please provide a valid metadata File.';
	const METADATA_URL_INVALID          = 'Please provide a valid metadata URL.';
	const METADATA_FILE_URL_NOT_UPLOADED = "Please provide a URL or upload a File.";

	//SAML SSO Error Messages
	const INVALID_REQUEST_INSTANT 		= '<strong>INVALID_REQUEST: </strong>Request time is greater than the current time.<br/>';
	const INVALID_SAML_VERSION 			= 'We only support SAML 2.0! Please send a SAML 2.0 request.<br/>';
	const INVALID_SP 					= '<strong>INVALID_SP: </strong>No Service Provider configuration found. Please configure your Service Provider.<br/>';
	const INVALID_REQUEST_SIGNATURE 	= '<strong>INVALID_SIGNATURE: </strong>Invalid Signature!<br/>';
	const SAML_INVALID_OPERATION 		= '<strong>INVALID_OPERATION: </strong>Invalid Operation! Please contact your site administrator.<br/>';
	const INVALID_USER 					= 'SSO Failed. Please contact your Administrator for more details.';
	const MISSING_NAMEID 				= 'Missing <saml:NameID> or <saml:EncryptedID> in <samlp:LogoutRequest>.';
	const INVALID_NO_OF_NAMEIDS 		= 'More than one <saml:NameID> or <saml:EncryptedD> in <samlp:LogoutRequest>.';
	const MISSING_ID_FROM_REQUEST 		= 'Missing ID attribute on SAML message.';
	const MISSING_ISSUER_VALUE 			= 'Missing <saml:Issuer> in assertion.';

	//WS FED SSO Error Messages
	const MISSING_WA_ATTR 				= 'The WS-Fed request has missing wa attribute.';
	const MISSING_WTREALM_ATTR 			= 'The WS-Fed request has missing wtrealm attribute.';

	public static function showMessage($message , $data=array())
	{
		$message = constant( "self::".$message );
		foreach($data as $key => $value)
		{
			$message = str_replace("{{" . $key . "}}", $value , $message);
		}
		return esc_attr($message);
	}
}