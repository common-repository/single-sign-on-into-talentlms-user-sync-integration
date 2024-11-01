<?php

namespace MSTUSI\Exception;

use MSTUSI\Helper\Constants\MoIDPMessages;

class InvalidRequestVersionException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_SAML_VERSION');
		$code 		= 118;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}