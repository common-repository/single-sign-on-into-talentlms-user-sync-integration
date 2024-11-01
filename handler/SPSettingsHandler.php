<?php

namespace MSTUSI\Handler;

use MSTUSI\Helper\Constants\MoIDPMessages;
use MSTUSI\Helper\Traits\Instance;
use MSTUSI\Helper\Utilities\MoIDPUtility;
use MSTUSI\Helper\Utilities\SAMLUtilities;
use MSTUSI\Helper\SAML2\MetadataReader;
use MSTUSI\Exception\MetadataFileException;
use MSTUSI\Exception\RequiredSpNameException;

final class SPSettingsHandler extends SPSettingsUtility
{
    use Instance;

    /** Private constructor to avoid direct object creation */
    private function __construct(){}

	/**
     * @param $POSTED
     * @throws \MSTUSI\Exception\MetadataFileException
     * @throws \MSTUSI\Exception\RequiredSpNameException
     */
	public function _mo_idp_metadata_new_sp($POSTED)
	{
		$file ="";

		if (! empty( $POSTED['idp_sp_name'] )) {
			if (isset($_FILES['metadata_file'] ) || isset( $POSTED['metadata_url'] ) ) {
				if(!is_null($file))
					$this->upload_metadata($file,$POSTED);
			}
			else {
				throw new MetadataFileException();
			}
		}
		else {
			throw new RequiredSpNameException();
		}	
	}

	function upload_metadata($file,$POSTED) {

		global $dbIDPQueries;

		$old_error_handler = set_error_handler( array( $this, 'handleXmlError' ) );
		$document = new \DOMDocument();
		$document->loadXML($file);
		restore_error_handler();

		$first_child = $document->firstChild;

		if ( ! empty( $first_child ) ) {

			$metadata = new MetadataReader($document);
			$service_providers = $metadata->getServiceProviders();
			
			if ( ! preg_match( "/^\w*$/", $POSTED['idp_sp_name'] ) ) {
				do_action('mo_idp_show_message',MoIDPMessages::showMessage('SP_NAME_INVALID'),'ERROR');
				return;
			}
			if ( empty( $service_providers ) && !empty( $_FILES['metadata_file']['tmp_name']) ) {
				do_action('mo_idp_show_message',MoIDPMessages::showMessage('METADATA_FILE_INVALID'),'ERROR');
				return;
			}
			if ( empty( $service_providers ) && !empty($POSTED['metadata_url']) ) {
				do_action('mo_idp_show_message',MoIDPMessages::showMessage('METADATA_URL_INVALID'),'ERROR');
				return;
			}
			
			foreach ( $service_providers as $key => $sp ) {
				$entityID = $sp->getEntityID();
				$acsUrl = $sp->getAcsURL();
				$nameID = $sp->getNameID();
				$signed = $sp->getSignedRequest();				
			} 
			
			$where = $data = array();
			$check = $where['mo_idp_sp_name'] = $data['mo_idp_sp_name'] = sanitize_text_field($POSTED['idp_sp_name']);
	
			$this->checkNameALreadyInUse($check);
			$this->checkIssuerAlreadyInUse($entityID,NULL,$check);


			$data['mo_idp_protocol_type']		= sanitize_text_field($POSTED['mo_idp_protocol_type']);
			$data['mo_idp_sp_issuer']			= $entityID;
			$data['mo_idp_acs_url'] 			= $acsUrl;
			$data['mo_idp_nameid_format'] 		= $nameID;
	
			$data['mo_idp_logout_url'] 			= NULL;
			$data['mo_idp_cert'] 				= NULL;
			$data['mo_idp_cert_encrypt'] 		= NULL;
			$data['mo_idp_default_relayState'] 	= NULL;
			$data['mo_idp_logout_binding_type'] = 'HttpRedirect';
	
			$data['mo_idp_response_signed'] 	= NULL;
			$data['mo_idp_assertion_signed'] 	= ($signed == "true") 		? 1 		: NULL;
			$data['mo_idp_encrypted_assertion'] = NULL;

			$count=$dbIDPQueries->get_sp_count();
			if($count>=1){
				$dbIDPQueries->update_metdata_data();
			}

			$insert = $dbIDPQueries->insert_sp_data($data);

			do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS'); 
		} 
		else{
			if(!empty( $_FILES['metadata_file']['tmp_name'])){
				do_action('mo_idp_show_message',MoIDPMessages::showMessage('METADATA_FILE_INVALID'),'ERROR');
			}
			if(!empty($POSTED['metadata_url'])){
				do_action('mo_idp_show_message',MoIDPMessages::showMessage('METADATA_URL_INVALID'),'ERROR');
			}
		}
		
	}

    /**
     * @param $POSTED
     * @throws \MSTUSI\Exception\IssuerValueAlreadyInUseException
     * @throws \MSTUSI\Exception\RequiredFieldsException
     * @throws \MSTUSI\Exception\SPNameAlreadyInUseException
     * @throws \MSTUSI\Exception\InvalidEncryptionCertException
     */
    public function _mo_idp_save_new_sp($POSTED)
	{
        /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfRequiredFieldsEmpty(array('idp_sp_name'=>$POSTED,'idp_sp_issuer'=>$POSTED,
												'idp_acs_url'=>$POSTED,'idp_nameid_format'=>$POSTED));

		$where = $data = array();
		$check = $where['mo_idp_sp_name'] = $data['mo_idp_sp_name'] = sanitize_text_field($POSTED['idp_sp_name']);
		$issuer = $data['mo_idp_sp_issuer'] = sanitize_text_field($POSTED['idp_sp_issuer']);

		$this->checkIssuerAlreadyInUse($issuer,NULL,$check);
		$this->checkNameALreadyInUse($check);

		$data = $this->collectData($POSTED,$data);

		$insert = $dbIDPQueries->insert_sp_data($data);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

    /**
     * @param $POSTED
     * @throws \MSTUSI\Exception\IssuerValueAlreadyInUseException
     * @throws \MSTUSI\Exception\NoServiceProviderConfiguredException
     * @throws \MSTUSI\Exception\RequiredFieldsException
     * @throws \MSTUSI\Exception\SPNameAlreadyInUseException
     * @throws \MSTUSI\Exception\InvalidEncryptionCertException
     */
    public function _mo_idp_edit_sp($POSTED)
	{
        /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfRequiredFieldsEmpty(array('idp_sp_name'=>$POSTED,'idp_sp_issuer'=>$POSTED,
												'idp_acs_url'=>$POSTED,'idp_nameid_format'=>$POSTED));
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');

		$data 		= $where 					= array();
		$id 		= $where['id'] 				= sanitize_text_field( $POSTED['service_provider']);
		$check 		= $data['mo_idp_sp_name'] 	= sanitize_text_field( $POSTED['idp_sp_name']	);
		$issuer 	= $data['mo_idp_sp_issuer'] = sanitize_text_field( $POSTED['idp_sp_issuer']	);

		$this->checkIfValidServiceProvider($dbIDPQueries->get_sp_data($id));
		$this->checkIssuerAlreadyInUse($issuer,$id,NULL);
		$this->checkNameALreadyInUse($check,$id);

		$data = $this->collectData($POSTED,$data);

		$dbIDPQueries->update_sp_data($data,$where);

		$TalentLMS_URL = sanitize_url($POSTED["mo_idp_talentlms_url"]);

		$TalentLMS_API = sanitize_text_field($POSTED["mo_idp_talentlms_api"]);

		update_site_option('mo_idp_talentlms_url' , $TalentLMS_URL);
		update_site_option('mo_idp_talentlms_api' , $TalentLMS_API);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

	public function mo_idp_delete_sp_settings($POSTED)
	{
        /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;

		MoIDPUtility::startSession();
		$this->checkIfValidPlugin();

		$spWhere 					= array();
		$spWhere['id'] 				= sanitize_text_field($POSTED['sp_id']);
		$spAttrWhere['mo_sp_id'] 	= sanitize_text_field($POSTED['sp_id']);

		$dbIDPQueries->delete_sp($spWhere,$spAttrWhere);

		if(array_key_exists('SP',$_SESSION)) unset($_SESSION['SP']);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SP_DELETED'),'SUCCESS');
	}

    /**
     * @param $POSTED
     * @throws \MSTUSI\Exception\NoServiceProviderConfiguredException
     */
    public function mo_idp_change_name_id($POSTED)
	{
        /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');

		$data 						= $where 		= array();
		$sp_id 						= $where['id'] 	= sanitize_text_field($POSTED['service_provider']);
		$data['mo_idp_nameid_attr'] = sanitize_text_field($POSTED['idp_nameid_attr']);
		$dbIDPQueries->update_sp_data($data,$where);
		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

    /**
     * @param $POSTED
     * @throws \MSTUSI\Exception\NoServiceProviderConfiguredException
     */
    public function _mo_sp_change_settings($POSTED)
	{
		$this->checkIfValidPlugin();
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');
	}

    /**
     * @param $POSTED
     * @param $data
     * @return mixed
     * @throws \MSTUSI\Exception\InvalidEncryptionCertException
     */
    private function collectData($POSTED, $data)
	{
		$data['mo_idp_acs_url'] 			= sanitize_url($POSTED['idp_acs_url']);
		$data['mo_idp_nameid_format'] 		= sanitize_text_field($POSTED['idp_nameid_format']);
		$data['mo_idp_protocol_type']		= sanitize_text_field($POSTED['mo_idp_protocol_type']);

		$data['mo_idp_logout_url'] 			= NULL;
		$data['mo_idp_cert'] 				= !empty($POSTED['mo_idp_cert']) 				? SAMLUtilities::sanitize_certificate(trim($POSTED['mo_idp_cert'])) 		: NULL;
		$data['mo_idp_cert_encrypt'] 		= NULL;
		$data['mo_idp_default_relayState'] 	= !empty($POSTED['idp_default_relayState']) 	? sanitize_url($POSTED['idp_default_relayState'])                            : NULL;
		$data['mo_idp_logout_binding_type'] = !empty($POSTED['mo_idp_logout_binding_type']) ? sanitize_text_field($POSTED['mo_idp_logout_binding_type']) 				: 'HttpRedirect';

		$data['mo_idp_response_signed'] 	= NULL;
		$data['mo_idp_assertion_signed'] 	= isset($POSTED['idp_assertion_signed']) 		? sanitize_text_field($POSTED['idp_assertion_signed']) 		: NULL;
		$data['mo_idp_encrypted_assertion'] = NULL;

		$this->checkIfValidEncryptionCertProvided($data['mo_idp_encrypted_assertion'],$data['mo_idp_cert_encrypt']);

		return $data;
	}

	function handleXmlError( $errno, $errstr, $errfile, $errline ) {
		if ( $errno == E_WARNING && ( substr_count( $errstr, "DOMDocument::loadXML()" ) > 0 ) ) {
			return;
		} else {
			return false;
		}
	}
}