<?php
echo'
    <div class="mo-visual-tour-overlay" id="overlay" hidden></div>
    <div class="wrap">
            <div><img style="float:left;" src="'.esc_attr(MSTUSI_LOGO_URL).'"></div>
            <h1>
            Single sign on into TalentLMS User Sync Integration
                <div id="idp-quicklinks">
                    <a class="add-new-h2" href="'.esc_url($help_url).'" target="_blank">FAQs</a>
                </div>
            </h1>			
    </div>';

    mstusi_check_is_curl_installed();

echo'<div id="tab">
        <h2 class="nav-tab-wrapper">
            <a  class="nav-tab 
                '.($active_tab == $spSettingsTabDetails->_menuSlug ? 'nav-tab-active' : '').'" 
                href="'.esc_attr($idp_settings).'">
                '.esc_attr( $spSettingsTabDetails->_tabName ).'
            </a>
            <a  class="nav-tab 
                '.($active_tab == $metadataTabDetails->_menuSlug ? 'nav-tab-active' : '').'" 
                href="'.esc_attr($sp_settings).'">
                '.esc_attr($metadataTabDetails->_tabName).'
            </a>
            <a class="nav-tab 
                '.($active_tab == $attrMapTabDetails->_menuSlug ? 'nav-tab-active' : '').'" 
                href="'.esc_attr($attr_settings).'">
                '.esc_attr($attrMapTabDetails->_tabName).'
            </a>
    </div>';

    if (!get_site_option("mo_idp_new_certs"))    
    echo"<div style='display:block; width:62%; margin:auto; margin-top:10px; color:black; background-color:rgba(251, 232, 0, 0.15); 
    padding:15px 15px 15px 15px; border:solid 1px rgba(204, 204, 0, 0.36); font-size:large; line-height:normal'>
    <span style='color:red;'><span class='dashicons dashicons-warning'></span> <b>WARNING</b>:</span> The existing certificates have expired. Please update the certificates ASAP to secure your SSO.<br> Go to the <a href='admin.php?page=idp_metadata'><b>IDP Metadata</b></a> tab
    of the plugin to update your certificates. Make sure to update your Service Provider with the new certificate to ensure your SSO does not break.
</div>";