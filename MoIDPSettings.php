<?php

/*
Plugin Name: Single Sign-On into TalentLMS User Sync Integration
Description: Convert your WordPress into an IDP for TalentLMS and Synchronize the users from WordPress to TalentLMS.
Version: 1.0.0
Author: miniOrange
Author URI: https://plugins.miniorange.com/
*/

if(! defined( 'ABSPATH' )) exit;
define('MSTUSI_PLUGIN_NAME', plugin_basename(__FILE__));
$dirName = substr(MSTUSI_PLUGIN_NAME, 0, strpos(MSTUSI_PLUGIN_NAME, "/"));
define('MSTUSI_NAME', $dirName);
include 'autoload.php';
\MSTUSI\MoIDP::instance();
