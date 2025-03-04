<?php

echo '
<div class="mo_idp_divided_layout mo-idp-full">
	<div class="mo_idp_table_layout mo-idp-center">
	    <h3>'."PLUGIN DETAILS".'</h3><hr>
	    <table style="width:100%">
	        <tr>
                <td>Plugin Tab Details. Click on the tour button below to learn more about each section.</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="background-color:#CBCBCB;padding:1%;border-radius:2px;">
                        <i>
                            New to SAML? &nbsp; Looking for a documentation? &nbsp;
                            <a href="'.esc_url($saml_doc).'" target="_blank">Click Here to view the guides.</a>
                        </i>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="background-color:#CBCBCB;padding:1%;border-radius:2px;">
                        <i>
                        New to WS-FED? &nbsp; Looking for a documentation?
                        Here is a guide which details how you can setup SSO between the plugin and
                        your Microsoft Federated Domain. &nbsp;
                        <a href="'.esc_url($wsfed_doc).'" target="_blank">Click Here to view the guides.</a></i>
                    </div>
                    <br>
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <table class="idp-tab-table-list" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="idp-tab-table-list-status" style="width:20px;">Tab</th>
                            <th class="idp-tab-table-list-name">Description</th>
                            <th class="idp-tab-table-list-actions" style="width:10px;">Tour</th>						
                        </tr>
                    </thead>
                    <tbody>';

                        /** @var \MSTUSI\Helper\Utilities\PluginPageDetails $tabs */
                        foreach ($tabDetails as $tabs) {
                            $link = add_query_arg( array('page' => $tabs->_menuSlug), sanitize_url($_SERVER['REQUEST_URI']));
                            echo '<tr>
                                    <td class="addon-table-list-status">'
                                        . esc_attr($tabs->_tabName).
                                    '</td>
                                    <td class="addon-table-list-name">
                                        <i>'
                                            .esc_attr($tabs->_description).
                                        '</i>
                                    </td>
                                    <td class="addon-table-list-actions">
                                        <a  class="button-primary button tips" 
                                            href="'.esc_url($link).'">
                                            Go there
                                        </a>
                                    </td>
                                </tr>';
                        }
echo'               
                    </tbody>
                </table>
            </tr>
        </table>
	</div>
</div>
';