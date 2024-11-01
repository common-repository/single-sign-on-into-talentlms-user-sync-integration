<?php

namespace MSTUSI;

use MSTUSI\Actions\SettingsActions;
use MSTUSI\Actions\SSOActions;
use MSTUSI\Helper\Constants\MoIdPDisplayMessages;
use MSTUSI\Helper\Database\MoDbQueries;
use MSTUSI\Helper\Traits\Instance;
use MSTUSI\Helper\Utilities\MenuItems;
use MSTUSI\Helper\Utilities\MoIDPUtility;
use MSTUSI\Helper\Utilities\RewriteRules;
use MSTUSI\Helper\Utilities\TabDetails;
use MSTUSI\Helper\Utilities\Tabs;
use MSTUSI\Handler\TalentLMSRestAPIHandler;

final class MoIDP
{
    use Instance;

    /** Private constructor to avoid direct object creation */
    private function __construct()
    {
        $this->initializeGlobalVariables();
        $this->initializeActions();
        $this->addHooks();
    }

    function initializeGlobalVariables()
    {
        global $dbIDPQueries;
        $dbIDPQueries = MoDbQueries::instance();
    }

    function addHooks()
    {
        add_action( 'mo_idp_show_message',  		        array( $this, 'mo_show_message' 				), 1 , 2 );
        add_action( 'admin_menu', 					        array( $this, 'mo_idp_menu' 					) 		 );
        add_action( 'admin_enqueue_scripts', 		        array( $this, 'mo_idp_plugin_settings_style' 	) 		 );
        add_action( 'admin_enqueue_scripts', 		        array( $this, 'mo_idp_plugin_settings_script' 	) 		 );
        add_action( 'enqueue_scripts', 				        array( $this, 'mo_idp_plugin_settings_style' 	) 		 );
        add_action( 'enqueue_scripts', 				        array( $this, 'mo_idp_plugin_settings_script' 	) 		 );
        add_filter( 'plugin_action_links_'.MSTUSI_PLUGIN_NAME, array($this , 'mo_idp_plugin_anchor_links'      )        );
        register_activation_hook  ( MSTUSI_PLUGIN_NAME, 	    array( $this, 'mo_plugin_activate'			    ) 		 );
        add_filter( 'user_row_actions',                     array( $this, 'Sync_with_TalentLMS'             ), 10, 2 );
    }

    function initializeActions()
    {
        RewriteRules::instance();
        SettingsActions::instance();
        SSOActions::instance();
    }

    function mo_idp_menu()
    {
        new MenuItems($this);
    }

    function mo_sp_settings()
    {
        include 'controllers/sso-main-controller.php';
    }

    function mo_idp_plugin_settings_style()
    {
        wp_enqueue_style( 'mo_idp_admin_settings_style'	,MSTUSI_CSS_URL				 );
        wp_enqueue_style( 'wp-pointer' );
    }

    function mo_idp_plugin_settings_script()
    {
        wp_enqueue_script( 'mo_idp_admin_settings_script', MSTUSI_JS_URL, array('jquery') );
    }


    function mo_plugin_activate()
    {
        /** @var MoDbQueries $dbIDPQueries */
        global $dbIDPQueries;
        $dbIDPQueries->checkTablesAndRunQueries();
        if (!get_site_option("mo_idp_new_certs"))
        {
            MoIDPUtility::useNewCerts();
        }
        $metadata_dir		= MSTUSI_DIR . "metadata.xml";
        if (file_exists($metadata_dir) && filesize($metadata_dir) > 0) {
            unlink($metadata_dir);
            MoIDPUtility::createMetadataFile();
        }
        if (get_site_option("idp_keep_settings_intact", NULL) === NULL)
        {
            update_site_option( "idp_keep_settings_intact", TRUE );
        }
    }

    function mo_show_message($content,$type)
    {
        new MoIdPDisplayMessages($content, $type);
    }


    function mo_idp_plugin_anchor_links( $links ) 
    {
        if(array_key_exists("deactivate", $links))
        {
            $arr = array();
            $data = [
                'Settings'          => 'idp_configure_idp',
            ];

            foreach ($data as $key => $val) {
                $url = esc_url(add_query_arg(
                    'page',
                    $val,
                    get_admin_url() . 'admin.php?'
                ));
                $anchor_link = "<a href='$url'>" . __($key) . '</a>' ;
                array_push($arr, $anchor_link);
            }
            $links = $arr + $links;
        }
        return $links ;
    }

    function Sync_with_TalentLMS( $actions, $user_object ) {

        if (current_user_can( 'administrator', $user_object->ID ) and !empty(get_site_option('mo_idp_talentlms_url'))){
            $actions['Sync_With_TLMS'] = '<a href="'.add_query_arg( array('operation' => 'syncWithTalentLMS' , 'user' => $user_object->ID ), admin_url()).'">Sync With TalentLMS</a>';
        }
        return $actions;
    }
}