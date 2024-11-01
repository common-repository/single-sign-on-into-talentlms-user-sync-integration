<?php

namespace MSTUSI\Actions;

use MSTUSI\Exception\InvalidEncryptionCertException;
use MSTUSI\Exception\InvalidOperationException;
use MSTUSI\Exception\IssuerValueAlreadyInUseException;
use MSTUSI\Exception\JSErrorException;
use MSTUSI\Exception\NoServiceProviderConfiguredException;
use MSTUSI\Exception\NotRegisteredException;
use MSTUSI\Exception\RequiredFieldsException;
use MSTUSI\Exception\SPNameAlreadyInUseException;
use MSTUSI\Exception\MetadataFileException;
use MSTUSI\Exception\RequiredSpNameException;
use MSTUSI\Handler\IDPSettingsHandler;
use MSTUSI\Handler\SPSettingsHandler;
use MSTUSI\Helper\Traits\Instance;
use MSTUSI\Helper\Utilities\MoIDPUtility;


class SettingsActions extends BasePostAction
{
    use Instance;

    /** @var SPSettingsHandler $handler */
    private $handler;
    /** @var IDPSettingsHandler $idpSettingsHandler */
    private $idpSettingsHandler;



    public function __construct()
    {
		$this->handler = SPSettingsHandler::instance();
		$this->idpSettingsHandler = IDPSettingsHandler::instance();
		$this->_nonce = 'idp_settings';
		parent::__construct();
    }

    private $funcs = array(
		'mo_add_idp',
        'mo_edit_idp',
        'mo_show_sp_settings',
        'mo_idp_delete_sp_settings',
        'mo_idp_entity_id',
        'change_name_id',
        'mo_idp_use_new_cert',
        'saml_idp_upload_metadata', 
	);

	public function handle_post_data()
	{
		if ( current_user_can( 'manage_options' ) and isset( $_POST['option'] ) )
		{
			$option = sanitize_text_field(trim($_POST['option']));
			try{
				$this->route_post_data($option);
				$this->changeSPInSession($_POST);
			}catch (NotRegisteredException $e) {
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (NoServiceProviderConfiguredException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (JSErrorException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (RequiredFieldsException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (SPNameAlreadyInUseException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (IssuerValueAlreadyInUseException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (InvalidEncryptionCertException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (InvalidOperationException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (MetadataFileException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (RequiredSpNameException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (\Exception $e){
				if(MSTUSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
				wp_die(esc_attr($e->getMessage()));
			}
		}
	}

    /**
     * @param $option
     * @throws InvalidEncryptionCertException
     * @throws IssuerValueAlreadyInUseException
     * @throws NoServiceProviderConfiguredException
     * @throws RequiredFieldsException
     * @throws SPNameAlreadyInUseException
     * @throws \MSTUSI\Exception\SupportQueryRequiredFieldsException
     * @throws \MSTUSI\Exception\MetadataFileException
     * @throws \MSTUSI\Exception\RequiredSpNameException
     */
    public function route_post_data($option)
    {
        $POSTED = array();
		foreach($_POST as $key => $value) {
            $POSTED[$key] = sanitize_text_field($value);
        }
        switch ($option) {
            case $this->funcs[0]:
                $this->handler->_mo_idp_save_new_sp($POSTED);
                break;
            case $this->funcs[1]:
                $this->handler->_mo_idp_edit_sp($POSTED);
                break;
            case $this->funcs[2]:
                $this->handler->_mo_sp_change_settings($POSTED);
                break;
            case $this->funcs[3]:
                $this->handler->mo_idp_delete_sp_settings($POSTED);
                break;
            case $this->funcs[4]:
                $this->idpSettingsHandler->mo_change_idp_entity_id($POSTED);
                break;
            case $this->funcs[5]:
                $this->handler->mo_idp_change_name_id($POSTED);
                break;
            case $this->funcs[6]:
                MoIDPUtility::useNewCerts();
                break;
            case $this->funcs[7]:
                $this->handler->_mo_idp_metadata_new_sp($POSTED);
                break;       
        }
    }

	public function changeSPInSession($POSTED)
    {
        MoIDPUtility::startSession();
        $_SESSION['SP'] = array_key_exists('service_provider', $POSTED) &&
        !MoIDPUtility::isBlank($POSTED['service_provider']) ? sanitize_text_field($POSTED['service_provider']) : 1;
    }
}