<?php

	namespace MSTUSI\Helper\Constants;

	class MoIdPDisplayMessages
	{
		private $message;
		private $type;

		function __construct( $message,$type )
		{
	        $this->_message = $message;
	        $this->_type = $type;
	        add_action( 'admin_notices', array( $this, 'render' ) );
	    }

	    function render()
	    {
	    	switch ($this->_type)
	    	{
	    		case 'CUSTOM_MESSAGE':
	    			echo  esc_html($this->_message);																									break;
	    		case 'NOTICE':
	    			echo '<div style="margin-top:1%;" class="is-dismissible notice notice-warning"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    		case 'ERROR':
	    			echo '<div  style="margin-top:1%;" class="notice notice-error is-dismissible"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    		case 'SUCCESS':
	    			echo '<div  style="margin-top:1%;" class="notice notice-success is-dismissible"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    	}
	    }
	}