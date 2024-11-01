<?php

namespace MSTUSI\Exception;

use MSTUSI\Helper\Constants\MoIDPMessages;

class OTPRequiredException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('REQUIRED_OTP');
		$code 		= 113;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}