<?php

namespace MSTUSI\Helper\Utilities;


use MSTUSI\Helper\Traits\Instance;

final class TabDetails
{
    use Instance;

    /**
     * Array of PluginPageDetails Object detailing
     * all the page menu options.
     *
     * @var array[PluginPageDetails] $_tabDetails
     */
    public $_tabDetails;

    /**
     * The parent menu slug
     * @var string $_parentSlug
     */
    public $_parentSlug;

    /** Private constructor to avoid direct object creation */
    private function __construct()
    {
        $this->_parentSlug = 'idp_settings';
        $this->_tabDetails = [
            Tabs::IDP_CONFIG => new PluginPageDetails(
                'SAML IDP - Configure IDP',
                'idp_configure_idp',
                'Service Providers',
                'Service Providers',
                "This Tab is the section where you Configure your Service Provider's details needed for SSO."
            ),
            Tabs::METADATA => new PluginPageDetails(
                'SAML IDP - Metadata',
                'idp_metadata',
                'IDP Metadata',
                'IDP Metadata',
                "This Tab is where you will find information to put in your Service Provider's configuration page."
            ),
            Tabs::ATTR_SETTINGS => new PluginPageDetails(
                'SAML IDP - Attribute Settings',
                'idp_attr_settings',
                'Attribute/Role Mapping',
                'Attribute/Role Mapping',
                "This Tab is where you configure the User Attributes and Role that you want to send out to your Service Provider."
            ),  
        ];
    }
}