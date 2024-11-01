<?php

namespace MSTUSI\Exception;

use MSTUSI\Helper\Constants\MoIDPMessages;

class InvalidServiceProviderException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_SP');
		$code 		= 119;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}