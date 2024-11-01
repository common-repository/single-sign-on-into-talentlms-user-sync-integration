<?php

namespace MSTUSI\Handler;

use MSTUSI\Helper\Traits\Instance;
use MSTUSI\Helper\Utilities\MoIDPUtility;
use MSTUSI\Helper\Utilities;
use MSTUSI\Helper\Utilities\TalentLMSAPImessages;
use WP_User;

class TalentLMSRestAPIHandler extends BaseHandler{

	use Instance;

	private function __construct()
	{
		$this->_nonce = 'mo_idp_talentlms_api' ;
	}
	
	function mo_idp_tlms_init($REQUEST)
	{
		switch($REQUEST['operation'])
		{
			case 'syncWithTalentLMS': $this->mo_idp_tlms_initiate_Sync($REQUEST);
			break;

			case 'user_sync_status' : $this->mo_idp_tlms_show_message($REQUEST);
			break;
		}
	}


	function mo_idp_tlms_initiate_Sync($REQUEST)
	{
		$user = $REQUEST['user'];
		$user_meta = get_user_meta($user);															//WordPress does not have same table for the email and user-meta.	
		$userTable = new WP_User($user);					
		$TalentLMSPassword = self::password_generate(12);											//User signin requires password.														

		$UserExistsInTLMS = self::mo_idp_tlms_call_api('get' , '/api/v1/users/email:'.$userTable->user_email,null);
		$UserExistsInTLMS = json_decode($UserExistsInTLMS['body'],true);
	
		if(isset($UserExistsInTLMS['error']))
		{
			switch($UserExistsInTLMS['error']['message'])
			{
				case TalentLMSAPImessages::USER_TEMP_DELETED :
					$message = base64_encode($UserExistsInTLMS['error']['message']) ;
					break;
					
				case TalentLMSAPImessages::USER_NOT_EXIST :
					$message = TalentLMSRestAPIHandler::mo_idp_tlms_UserSignup($user_meta,$userTable,$TalentLMSPassword);
					break;
		
				default :	$message = base64_encode($UserExistsInTLMS['error']['message']);
					break;
			}
		}
		else if(isset($UserExistsInTLMS))
		{			
			$message = TalentLMSRestAPIHandler::mo_idp_tlms_UserUpdate($UserExistsInTLMS , $user_meta , $userTable) ;
		}
		else{
			$message = base64_encode('REQUEST FAILED');
		}
	
		wp_safe_redirect(add_query_arg( array ('operation' => 'user_sync_status' , 'message' => $message) , admin_url().'users.php'));
	}


	/**
	 * Function responsible for collecting user profile attributes and calling api request to create user / User Sign-up in TalentLMS. 
	 */
	function mo_idp_tlms_UserSignup($user_meta,$userTable,$TalentLMSPassword)
	{
		$user_signup_params = array('first_name'=>$user_meta['first_name'][0], 
									'last_name'=>$user_meta['last_name'][0], 
									'email'=>$userTable->user_email, 
									'login'=>$userTable->user_login, 
									'password'=>$TalentLMSPassword	);

		$result = self::mo_idp_tlms_call_api('post' , '/api/v1/usersignup' ,$user_signup_params);
		$result = (json_decode($result['body'],true));
		if(isset($result['error']))
		{
			$message = base64_encode($result['error']['message']);
		}
		else
		{
			$message = base64_encode('User created successfully in TalentLMS');	
		}
		return $message ;
	}

	/**
	 * Function responsile  for collecting user profile attributes and calling api request for the User Update request.
	 */
	function mo_idp_tlms_UserUpdate($UserExistsInTLMS , $user_meta , $userTable)
	{
		$user_update_params = array('user_id'=> $UserExistsInTLMS['id'], 
		"first_name"=>$user_meta['first_name'][0], 
		"last_name"=>$user_meta['last_name'][0], 
		"email"=>$userTable->user_email, 
		"login"=>$userTable->user_login	);

		$result = self::mo_idp_tlms_call_api('post' , '/api/v1/edituser' ,$user_update_params);
		if(isset($result['error']))
		{
			$message = base64_encode($result['error']['message']);
		}
		else{
			$message = base64_encode('User updated successfully in TalentLMS');
		}
		return $message ;
	}

	/**
	 * API calling function, responsible for making all the api calls to the TalentLMS for user-sync.
	 */
    public static function mo_idp_tlms_call_api($method, $url, $params){

		$TalentLMSAPI = sanitize_text_field( get_site_option('mo_idp_talentlms_api') ) ;
		$auth = base64_encode( $TalentLMSAPI . ':');
		$absUrl = ($method === 'get') ? self::get_API_URL($url.$params) : self::get_API_URL($url) ;
		$args = [
			'sslverify' => false,
			'headers' 	=>[
				'Authorization' => "Basic $auth",
			],
			'body' => $params,	
		];
		
		$response = wp_remote_post($absUrl, $args);

		if ( is_wp_error( $response ) ) {
			wp_die(esc_attr("Something went wrong: <br/>{esc_attr($response->get_error_message())}"));
		}

		return $response;
	}
	
	/**
	 * TalentLMS requires a random password while signing up user with API.
	 * This function is responsible for generating the random passoword.
	 */
	public static function password_generate($chars) 
	{
  		$passContents = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
  		return substr(str_shuffle($passContents), 0, $chars);
	}  

	/**
	 * Function responsible for showing the message to the administrator regrading the user-sync.
	 * Includes the cURL as well as the settings message as notices.
	 */
	function mo_idp_tlms_show_message($REQUEST)
	{
		$message = isset($REQUEST['message']) ? base64_decode($REQUEST['message']) : null; 
		if(!empty($message))
		do_action('mo_idp_show_message',$message,'NOTICE');
	}

	/**
	 * Function responsible for returning the TalentLMS API URL.
	 * Differerent requests have different endpoints according to the request.
	 */
	protected static function get_API_URL($url=''){
		$apiBase = sanitize_url(get_site_option('mo_idp_talentlms_url'));
		return $apiBase.$url;
	}

}