<?php
echo 	'<div class="mo_idp_divided_layout mo-idp-full">';
// mstusi_able_to_write_files($registered,$verified);
echo    "<div id='successDiv' style='display:none; width:64%; margin:auto; margin-top:10px; color:black; background-color:rgba(44, 252, 2, 0.15); 
            padding:15px 15px 15px 15px; border:solid 1px rgba(55, 242, 07, 0.36); font-size:large; line-height:normal'>
                <span style='color:green;'>
                    <span class='dashicons dashicons-yes-alt'></span> 
                    <b>SUCCESS</b>:
                </span> 
                You have successfully updated your certificates.
        </div>";

echo '            <div class="mo_idp_table_layout mo-idp-center">
                <fieldset>
                <legend>
                <h2>
                    IDP METADATA';
                   // mstusi_restart_tour();
        echo    '</h2></legend> <hr>
                <h4>You will need the following information to configure your Service Provider. Copy it and keep it handy:</h4>
                <form name="f" method="post" action="" id="mo_idp_settings">
                    <input type="hidden" name="option" value="mo_idp_entity_id" />
                    <table>
                        <tr>
                            <td style="width:20%">IdP EntityID / Issuer:</td>
                            <td>
                                <input  type="text" 
                                        name="mo_saml_idp_entity_id" 
                                        placeholder="Entity ID of your IdP" 
                                        style="width: 95%;" 
                                        value="'.esc_attr($idp_entity_id).'" 
                                        required="">
                            </td>
                            <td style="width:10%">
                                <input  type="submit" 
                                        name="submit" 
                                        style="width:100px;" 
                                        value="Update" 
                                        class="button button-primary button-large">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="mo_idp_note">
                                    <i>
                                        <span style="color:red"><b>Note:</b></span> 
                                        If you have already shared the below URLs or Metadata with your SP, 
                                        do <b>NOT</b> change IdP EntityID. It might break your existing login flow.
                                    </i>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
                <table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px; border-collapse: collapse; width:98%" id = "idpInfoTable">
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>IDP-EntityID / Issuer</b></td>
                        <td style="width:60%; padding: 15px;"><span id="idp_entity_id">'.esc_attr($idp_entity_id).'</span>
						<span class="dashicons dashicons-admin-page copytooltip" style="float:right" onclick="copyToClipboard(this, \'#idp_entity_id\', \'#idp_entity_id_copy\');"><span id="idp_entity_id_copy" class="copytooltiptext">Copy to Clipboard</span></span></td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>SAML Login URL / Passive Login URL</b></td>
                        <td style="width:60%;  padding: 15px;"><span id="saml_login_url">'.esc_url($site_url).'</span>
						<span class="dashicons dashicons-admin-page copytooltip" style="float:right" onclick="copyToClipboard(this, \'#saml_login_url\', \'#saml_login_url_copy\');"><span id="saml_login_url_copy" class="copytooltiptext">Copy to Clipboard</span></span></td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Certificate (Optional)</b></td>';

if ($expired_cert) 
echo					'<td style="width:60%;  padding: 15px;"><a href="'.esc_url($certificate_url).'" download>Download</a></td>'; 

if (!$expired_cert) 
echo                        '<td style="width:60%;  padding: 15px;">
                            <div class="mo_idp_note" style="border-width: 10px; border-color: red;">
                                <form name="f" method="post" action="" id="mo_idp_new_cert_form">
                                    <input type="hidden" name="option" value="mo_idp_use_new_cert" /></form>
                                <span style="color:red;">The existing certificates have expired.</span>
                                <br>
                                Download the latest certificate from <a href="'.esc_url($new_certificate_url).'" download>here</a>.
                                <br>
                                After updating your Service Provider, 
                                <br>
                                Click <button id="myBtn" style="background: orange; text-decoration: none; cursor: pointer; text-shadow: none; border-width: 2px; border-color: red; border-style: solid; "><b>HERE</b></button> to update the certificates in the plugin.
                            </div>';
echo			        '</td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Response Signed</b></td>
                        <td style="width:60%;  padding: 15px;">You can choose to sign your response in <a href="'.esc_attr( $idp_settings).'">Identity Provider</a></td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Assertion Signed</b></td>
                        <td style="width:60%;  padding: 15px;">You can choose to sign your assertion in <a href="'.esc_attr( $idp_settings).'">Identity Provider</a></td>
                    </tr>
                </table>';

echo            '<p style="text-align: center;font-size: 13pt;font-weight: bold;">OR</p>
                <p>You can provide this metadata URL to your Service Provider or <a href="'.esc_url($metadata_url).'" download="mo-idp-metadata.xml">download</a> it.</p>
                <div id="metadataXML" class="mo_idp_note">
                    <b><a id="idp_metadata_url" target="_blank" href="'.esc_url($metadata_url).'">'.esc_url( $metadata_url).'</a></b>
                    <span class="dashicons dashicons-admin-page copytooltip" style="float:right" onclick="copyToClipboard(this, \'#idp_metadata_url\', \'#idp_metadata_url_copy\');"><span id="idp_metadata_url_copy" class="copytooltiptext">Copy to Clipboard</span></span>
                </div>
            </div>
            <div class="mo_idp_table_layout mo-idp-center">
                <h4>You can run the following command to set WS-FED as your Authentication Protocol for your Federated Domain:</h4>
                <div class="mo_idp_note" style="color:red">
                    NOTE: Make sure to replace `&lt;your_domain&gt;` with your domain before running the command. 
                </div>
                <div class="copyClip" style="float:right;"> 
                    <h4> Copy to ClipBoard <span class="dashicons dashicons-admin-page" > </span> </h4> 
                </div>
                <textarea style="width:100%;height:100px;font-family:monospace;" class="copyBody">'.esc_textarea($wsfed_command).'</textarea>
            </div>
          </div>
          </fieldset>';

echo '
<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">

        <span class="modal-close-x">&times;</span>

        <div class="modal-header">
            <h1 class="modal-title">CAUTION</h1>
        </div>

        <div class="modal-body">
            <p id="isSPupdated" style="font-size:large; line-height:normal; display:block;">Certificate mismatch will <span style="color:red;"><b>BREAK</b></span> your current SSO! <br>Did you update your Service Provider with the latest metadata XML/certificate?</p>
            <p id="confirmation" style="font-size:large; line-height:normal; display:none;">Click on the <span style="color:#0071a1;"><b>Confirm</b></span> button to update the certificates in the plugin.</p> 
            <p id="downloadMsg" style="font-size:large; line-height:normal; display:none;">Please update your Service Provider with the latest metadata XML / certificate.</p>
        </div>

        <div class="modal-footer">
            <button type="button" id="q1yes" style="margin-right:5%; display:inline-block;" class="button button-primary button-large modal-button">Yes</button>
            <button type="button" id="q1no" class="button button-primary button-large modal-button" style="display:inline-block;">No</button>
            <button type="button" id="confirmUpdate" style="margin-right:5%; display:none;" class="button button-primary button-large modal-button">Confirm</button>
            <button type="button" id="goBack" class="button button-primary button-large modal-button" style="display:none;">Go Back</button>
            <button type="button" id="getMetadata" style="margin-right:5%; display:none; width:auto;" class="button button-primary button-large modal-button">Download metadata XML</button>
            <button type="button" id="getCert" class="button button-primary button-large modal-button" style="display:none; width:auto;">Download certificate</button>
        </div>

    </div>
</div>

<style>

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
        transition: all 1s;
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 45%; /* Could be more or less, depending on screen size */

        border-radius: 20px;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
    }

    .modal-title {
        text-align: center; 
        color: red;
    }

    .modal-body {
        position: relative;
        padding: 15px;
        text-align:center;
    }

    .modal-footer {
        padding: 15px;
        text-align: center;
        border-top: 1px solid #e5e5e5;
    }

    /* The Close Button */
    .modal-close-x {
        color: #aaa;
        float: right;
        font-size: 30px;
        font-weight: bold;
    }

    .modal-close-x:hover,
    .modal-close-x:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    } 

    .modal-button {
        width: 14%;
        height: 40px;
        font-size: 18px !important;
        white-space: normal;
        word-wrap: break-word;
    }

</style>

<script>';
if ($expired_cert==1){ 
echo '    window.onload = function() {
        document.getElementById("successDiv").style.display = "block";
        setTimeout(function(){
            document.getElementById("successDiv").style.display = "none";
        }, 105000);
    }';
    update_site_option("mo_idp_new_certs",'2');
}

echo '
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    var SPupdatedmsg = document.getElementById("isSPupdated");
    var confirmmsg = document.getElementById("confirmation");
    var downloadmsg = document.getElementById("downloadMsg");

    var yesBtn = document.getElementById("q1yes");
    var noBtn = document.getElementById("q1no");
    var updateBtn = document.getElementById("confirmUpdate");
    var goBackBtn = document.getElementById("goBack");
    var metadataBtn = document.getElementById("getMetadata");
    var certBtn = document.getElementById("getCert");

    // Get the <span> element that closes the modal
    var closex = document.getElementsByClassName("modal-close-x")[0];

    // When the user clicks on the button, open the modal
    if(btn){    
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }

    // When the user clicks on <span> (x), close the modal
    if(closex){    
        closex.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
        }
    }

    if(yesBtn){    
        yesBtn.onclick = function() {
            yesBtn.style.display = "none";
            noBtn.style.display = "none";
            updateBtn.style.display = "inline-block";
            goBackBtn.style.display = "inline-block";
            SPupdatedmsg.style.display = "none";
            confirmmsg.style.display = "block";
        }
    }

    if(noBtn){    
        noBtn.onclick = function() {
            yesBtn.style.display = "none";
            noBtn.style.display = "none";
            metadataBtn.style.display = "inline-block";
            certBtn.style.display = "inline-block";
            SPupdatedmsg.style.display = "none";
            downloadmsg.style.display = "block";
        }
    }

    if(metadataBtn){
        metadataBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
            window.open("'.esc_url($metadata_url).'","_blank");
        }
    }

    if(certBtn){
        certBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
            var ncurl = "' .esc_attr( addslashes($new_certificate_url)).'";
            console.log(ncurl);
            
            window.location.href="'.esc_attr( addslashes($new_certificate_url)).'";
        }
    }

    if(updateBtn){
        updateBtn.onclick = function() {
            document.getElementById(\'mo_idp_new_cert_form\').submit();
        }
    }

    if(goBackBtn){
        goBackBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            modal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
        }
    } 

    function confirmUpdate()
    {
        modal.style.display = "none";
        var txt="Warning! Updating certificates will break your current SSO.\n Please update your Service Provider with the latest metadata XML/certificates to resume the SSO.";
        var r=confirm(txt);
        if(r == true)
        {
            document.getElementById(\'mo_idp_new_cert_form\').submit();
        }
    }

</script>';

