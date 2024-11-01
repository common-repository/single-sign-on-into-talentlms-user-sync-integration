<?php

namespace MSTUSI\Helper\Database;

use MSTUSI\Helper\Traits\Instance;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

final class MoDbQueries
{
    use Instance;

    /** @var string $spDataTableName */
    private $spDataTableName;
    /** @var string $spAttrTableName */
    private $spAttrTableName;
    /** @var string $userMetaTable */
    private $userMetaTable;

    /** Private constructor to prevent direct object creation */
    private function __construct()
    {
        global $wpdb;
        $this->spDataTableName =  is_multisite() ? 'mo_sp_data' : $wpdb->prefix . 'mo_sp_data';
        $this->spAttrTableName =  is_multisite() ? 'mo_sp_attributes' : $wpdb->prefix . 'mo_sp_attributes';
        $this->userMetaTable   =  $wpdb->prefix . 'usermeta';
    }

    function generate_tables()
    {
        global $wpdb;
        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ){
            if ( ! empty( $wpdb->charset ) )
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            if ( ! empty( $wpdb->collate ) )
                $collate .= " COLLATE $wpdb->collate";
        }

        $table1 = "CREATE TABLE ".$this->spDataTableName." (
                    id bigint(20) NOT NULL auto_increment,
                    mo_idp_sp_name text NOT NULL,
                    mo_idp_sp_issuer longtext NOT NULL,
                    mo_idp_acs_url longtext NOT NULL,
                    mo_idp_cert longtext NULL,
                    mo_idp_cert_encrypt longtext NULL,
                    mo_idp_nameid_format longtext NOT NULL,
                    mo_idp_nameid_attr varchar(55) DEFAULT 'emailAddress' NOT NULL,
                    mo_idp_response_signed smallint NULL,
                    mo_idp_assertion_signed smallint NULL,
                    mo_idp_encrypted_assertion smallint NULL,
                    mo_idp_enable_group_mapping smallint NULL,
                    mo_idp_default_relayState longtext NULL,
                    mo_idp_logout_url longtext NULL,
                    mo_idp_logout_binding_type varchar(15) DEFAULT 'HttpRedirect' NOT NULL,
                    mo_idp_protocol_type longtext NOT NULL,
                    PRIMARY KEY  (id)
                )$collate;";

        $table2 = "CREATE TABLE ".$this->spAttrTableName." (
                    id bigint(20) NOT NULL auto_increment,
                    mo_sp_id bigint(20),
                    mo_sp_attr_name longtext NOT NULL,
                    mo_sp_attr_value longtext NOT NULL,
                    mo_attr_type smallint DEFAULT 0 NOT NULL,
                    PRIMARY KEY  (id),
                    FOREIGN KEY  (mo_sp_id) REFERENCES $this->spDataTableName (id)
                )$collate;";

        dbDelta($table1);
        dbDelta($table2);
    }

    function checkTablesAndRunQueries()
    {
        $old_version = get_site_option('mo_saml_idp_plugin_version');
        if(!$old_version)
        {
            update_site_option('mo_saml_idp_plugin_version', MSTUSI_DB_VERSION );
            $this->generate_tables();
            if (ob_get_contents()) ob_clean();
        }
        else
        {
            if($old_version < MSTUSI_DB_VERSION)
                update_site_option('mo_saml_idp_plugin_version', MSTUSI_DB_VERSION );
            $this->checkVersionAndUpdate($old_version);
        }
    }

    function checkVersionAndUpdate($old_version)
    {
        if(strcasecmp($old_version, '1.0') == 0)
        {
            $this->mo_update_logout();
            $this->mo_update_cert();
            $this->mo_update_relay();
            $this->mo_update_custom_attr();
            $this->mo_update_protocol_type();
        }
        else if(strcasecmp($old_version, '1.0.2') == 0)
        {
            $this->mo_update_logout();
            $this->mo_update_relay();
            $this->mo_update_custom_attr();
            $this->mo_update_protocol_type();
        }
        else if(strcasecmp($old_version, '1.0.4') == 0)
        {
            $this->mo_update_logout();
            $this->mo_update_custom_attr();
            $this->mo_update_protocol_type();
        }
        else if(strcasecmp($old_version, '1.2') == 0)
        {
            $this->mo_update_custom_attr();
            $this->mo_update_protocol_type();
        }
        else if(strcasecmp($old_version, '1.3') == 0)
        {
            $this->mo_update_protocol_type();
        }
    }

    function mo_update_protocol_type()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_protocol_type longtext NOT NULL" , $this->spDataTableName));
        $wpdb->query($wpdb->prepare("UPDATE %1s SET mo_idp_protocol_type = 'SAML'" , $this->spDataTableName));
    }

    function mo_update_logout()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_logout_url longtext NULL" , $this->spDataTableName));
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_logout_binding_type varchar(15) DEFAULT 'HttpRedirect' NOT NULL" , $this->spDataTableName));
    }

    function mo_update_cert()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_cert_encrypt longtext NULL" , $this->spDataTableName));
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_encrypted_assertion smallint NULL" , $this->spDataTableName));
    }

    function mo_update_relay()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_idp_default_relayState longtext NULL" , $this->spDataTableName));
    }

    function mo_update_custom_attr()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s ADD COLUMN mo_attr_type smallint DEFAULT 0 NOT NULL" , $this->spAttrTableName));
        $wpdb->update(  $this->spAttrTableName, array('mo_attr_type'=>'1'), array('mo_sp_attr_name'=>'groupMapName') );
    }

    function get_sp_list()
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s ", $this->spDataTableName ));
    }

    function get_sp_data($id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM %1s WHERE id = %2d ", $this->spDataTableName , $id));
    }

    function get_sp_count()
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT COUNT(*) FROM %1s",$this->spDataTableName);
        return $wpdb->get_var( $sql );
    }

    function get_sp_attributes($id)
    {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare("SELECT * FROM %1s WHERE mo_sp_id = %2d AND mo_sp_attr_name <> 'groupMapName' AND mo_attr_type = 0" , $this->spAttrTableName , $id));
    }

    function get_sp_role_attribute($id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM %1s WHERE mo_sp_id = %2d AND mo_sp_attr_name = 'groupMapName'" , $this->spAttrTableName , $id));
    }

    function get_all_sp_attributes($id)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s WHERE mo_sp_id = %2d " , $this->spAttrTableName , $id));
    }

    function get_sp_from_issuer($issuer)    
    {
        global $wpdb;
        return $wpdb-> get_row($wpdb->prepare("SELECT * FROM %1s WHERE mo_idp_sp_issuer = '%2s' " , $this->spDataTableName , $issuer ));
    }

    function get_sp_from_name($name)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM %1s WHERE mo_idp_sp_name = '%2s'" , $this->spDataTableName, $name ));
    }

    function get_sp_from_acs($acs)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM %1s WHERE mo_idp_acs_url = '%2s'" , $this->spDataTableName , $acs) );
    }

    function insert_sp_data($data)
    {
        global $wpdb;
        return $wpdb->insert(  $this->spDataTableName, $data );
    }

    function update_metdata_data()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM %1s ", $this->spDataTableName));
        $wpdb->query($wpdb->prepare("ALTER TABLE %1s AUTO_INCREMENT=0" , $this->spDataTableName));
    }

    function update_sp_data($data,$where)
    {
        global $wpdb;
        $wpdb->update( $this->spDataTableName, $data, $where );
    }

    function delete_sp($spWhere,$spAttrWhere)
    {
        global $wpdb;

        $this->delete_sp_attributes($spAttrWhere);
        $wpdb->delete( $this->spDataTableName, $spWhere, $where_format = null );
    }

    function delete_sp_attributes($attrWhere)
    {
        global $wpdb;
        $wpdb->delete( $this->spAttrTableName, $attrWhere, $where_format = null );
    }

    function insert_sp_attributes($data_attr)
    {
        global $wpdb;
        $wpdb->insert($this->spAttrTableName, $data_attr);
    }

    function get_custom_sp_attr($id)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s WHERE mo_sp_id = %2d  AND mo_attr_type = 2" , $this->spAttrTableName , $id ));
    }

    function get_users()
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s usermeta WHERE meta_key='mo_idp_user_type'" , $wpdb->prefix));
    }

    function get_protocol()
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT mo_idp_protocol_type FROM %1s " , $this->spDataTableName ));
    }

    function getDistinctMetaAttributes()
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_key FROM %1s" , $this->userMetaTable ));
    }

    function get_all_user_meta_attr($userID)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s WHERE user_id = %2d " , $this->userMetaTable , $userID));
    }
}