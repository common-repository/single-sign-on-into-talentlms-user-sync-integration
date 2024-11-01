<?php
	//start
	echo "<div class='mo_idp_divided_layout mo-idp-full'>
            <div class='mo_idp_table_layout mo-idp-center'>
            <fieldset>
            <legend>
                <h2>    
                    ATTRIBUTE MAPPING (OPTIONAL)";
                  //  mstusi_restart_tour();
    echo        "</h2></legend><hr>";
	//start of form
	echo '      
                <form name="f" method="post" id="mo_name_idp_attr_settings">
                    <input type="hidden" name="option" value="change_name_id" />
                    <input type="hidden" name="error_message" id="error_message" />
                    <input type="hidden" name="service_provider" value="'. esc_attr((isset($sp) && !empty($sp) ? $sp->id : '')).'"/>
    
                    <table class="mo_idp_settings_table">
                        <tr id="nameIdTable" style="background-color: white">
                            <td style="width:150px;"><strong>NameID Attribute:</strong></td>
                            <td>';

                                mstusi_get_nameid_select_box($disabled,$sp);

	echo'					
                            </td>
                            <td>
                                <input  type="submit" 
                                        name="submit" 
                                        style="margin-left:20px;width:100px;" 
                                        value="Save" 
                                        class="button button-primary button-large"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">
                                <i>
                                    <span style="color:red">NOTE: </span>
                                    This attribute value is sent in SAML Response. 
                                    Users in your Service Provider will be searched (existing users) or created 
                                    (new users) based on this attribute. Use EmailAddress by default.
                                </i>
                            </td>
                        </tr>
                    </table>
                </form>
             </div> </fieldset>';