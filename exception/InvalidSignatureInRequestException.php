<?php

namespace MSTUSI\Exception;

use MSTUSI\Helper\Constants\MoIDPMessages;

class InvalidSignatureInRequestException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_REQUEST_SIGNATURE');
		$code 		= 120;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}