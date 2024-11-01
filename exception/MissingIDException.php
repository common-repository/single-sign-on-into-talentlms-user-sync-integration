<?php

namespace MSTUSI\Exception;

use MSTUSI\Helper\Constants\MoIDPMessages;

class MissingIDException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('MISSING_ID_FROM_REQUEST');
		$code 		= 125;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}