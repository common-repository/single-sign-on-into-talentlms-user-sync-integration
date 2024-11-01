<?php

    /** @global \MSTUSI\Helper\Database\MoDbQueries $dbIDPQueries */
	global $dbIDPQueries;
	$sp_list 				= $dbIDPQueries->get_sp_list();
	$disabled				= NULL;
	$sp                     = empty($sp_list) ? '' : $sp_list[0];

	include MSTUSI_DIR . 'views/attr-settings.php';