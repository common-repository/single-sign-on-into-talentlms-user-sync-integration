<?php

namespace MSTUSI\Handler;

use MSTUSI\Exception\JSErrorException;
use MSTUSI\Exception\RequiredFieldsException;
use MSTUSI\Exception\SupportQueryRequiredFieldsException;
use MSTUSI\Helper\Constants\MoIDPMessages;
use MSTUSI\Helper\Utilities\MoIDPUtility;

class BaseHandler
{
    /** @var string $_nonce */
    public $_nonce;

	public function checkIfValidPlugin()
	{
		return TRUE;
	}

	public function isValidRequest()
    {
        if (!current_user_can('manage_options') || !check_admin_referer($this->_nonce)) {
            wp_die(MoIDPMessages::showMessage('INVALID_OP'));
        }
        return TRUE;
    }

    /**
     * @param $array
     * @param string $key
     * @throws JSErrorException
     */
    public function checkIfJSErrorMessage($array, $key='error_message')
	{
		if(array_key_exists($key,$array) && $array[$key]) throw new JSErrorException($array[$key]);
	}

    /**
     * @param $array
     * @throws RequiredFieldsException
     */
    public function checkIfRequiredFieldsEmpty($array)
	{
		foreach ($array as $key => $value)
		{
			if(
				(is_array($value) && ( !array_key_exists($key,$value) || MoIDPUtility::isBlank($value[$key])) )
				|| MoIDPUtility::isBlank($value)
			  )
				throw new RequiredFieldsException();
		}
	}

    /**
     * @param $array
     * @throws SupportQueryRequiredFieldsException
     */
    public function checkIfSupportQueryFieldsEmpty($array)
	{
		try{
			$this->checkIfRequiredFieldsEmpty($array);
		}catch(RequiredFieldsException $e){
			throw new SupportQueryRequiredFieldsException();
		}
	}
}