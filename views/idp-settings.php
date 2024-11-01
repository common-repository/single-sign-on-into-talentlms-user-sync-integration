<?php
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'upload_idp_metadata' ) 
	{
		echo '
		<div class="mo_idp_table_layout mo-idp-center">
		<div border="0" style="background-color:#FFFFFF;padding:0px 0px 0px 10px;">
		<table style="width:100%;">
			<tr>
				<td colspan="3">
					<h3>UPLOAD SP METADATA
						<span style="float:right;margin-right:5px;">
							<a href="'.esc_url(admin_url()).'admin.php?page=idp_configure_idp' . '"><input type="button" class="button" value="Cancel"/></a>
						</span>
					</h3>
				</td>
			</tr>
			<tr><td colspan="4"><hr></td></tr>
			<tr>

			<form name="saml_form" method="post" action="'.esc_url(admin_url()).'admin.php?page=idp_configure_idp'. '" enctype="multipart/form-data">
			<input type="hidden" name="mo_idp_protocol_type" value="'.esc_attr(sanitize_text_field($_GET['var'])).'">

			<tr>
				<td width="30%"><strong>Service Provider Name<span style="color:red;">*</span>:</strong></td>
				<td><input type="text" name="idp_sp_name" placeholder="Service Provider name " pattern="\w+" title="Only alphabets, numbers and underscore is allowed" style="width: 100%;" value="" required /></td>
				</tr>
	
			<tr>

				<input type="hidden" name="option" value="saml_idp_upload_metadata" />
				<input type="hidden" name="action" value="upload_idp_metadata" />
	
					<td>Upload Metadata  :</td>
					<td colspan="2"><input type="file" name="metadata_file" />
					<input type="submit" class="button button-primary button-large" value="Upload"/></td>
			</tr>
					
			<tr>
				<td colspan="2"><p style="font-size:13pt;text-align:center;"><b>OR</b></p></td>
			</tr>
	
			<tr>
				<input type="hidden" name="option" value="saml_idp_upload_metadata" />
				<input type="hidden" name="action" value="fetch_metadata" />
				<td width="20%">Enter metadata URL:</td>
				<td><input type="url" name="metadata_url" placeholder="Enter metadata URL of your SP." style="width:100%" /></td>
				<td width="20%">&nbsp;&nbsp;<input type="submit" class="button button-primary button-large" value="Fetch Metadata"/></td>
			</tr>
		</form>
		</table><br />
		</div>
		</div>';
	}
	else
	{
		echo'<div class="mo_idp_divided_layout mo-idp-full">
				<div class="mo_idp_table_layout mo-idp-center">
				<fieldset>
					<legend><h2>'.esc_html($header).'</h2></legend>';


		if(isset($sp) && !empty($sp) || $sp_exists)
		{

			echo	'<form name="f" method="post" action="'.esc_url($post_url).'">';


			echo	$sp_exists  ? '
					<input type="hidden" name="option" value="mo_add_idp" />'
					: '<input type="hidden" name="option" value="mo_edit_idp" />';

			echo	'<input type="hidden" name="service_provider" value="'.(isset($sp) && !empty($sp) ? esc_attr($sp->id) : "").'" />
					<input type="hidden" name="mo_idp_protocol_type" value="'.esc_attr($protocol_inuse).'">

					<span id="upload-metadata" >
					<b style="line-height:15px;display: inline-block;">You can upload your Service Provider Metadata XML File or the SP Metadata URL to configure the Service Provider.</b>
					<a href="' .esc_url($post_url). '&action=upload_idp_metadata&var='.esc_attr($protocol_inuse).'" style="float:right;">
					<input 
						type="button" class="button button-primary button-medium"
						value="Upload SP Metadata" style="font-size:small;"/>
					</a> 
					</span>

					<p align="center"><b>OR</b></p>					
									
					<table class="mo_idp_settings_table">
						<tr>
							<td colspan="2">
								<b>
									Please note down the following information from your Service Provider admin screen
									and keep it handy to configure your Identity provider.
								</b>
								<ol>
									<li><b>SP Entity ID / Issuer</b></li>
									<li><b>ACS URL</b></li>
									<li><b>X.509 Certificate for Signing if you are using HTTP-POST Binding. (Optional)</b></li>
									<li><b>X.509 Certificate for Encryption. (Optional)</b></li>
									<li><b>NameID Format</b></li>
								</ol>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="background-color:#CBCBCB;padding:1%;border-radius:2px;">
									<i>
									New to SAML? &nbsp;Looking for a documentation? &nbsp;
									<a href="'.esc_url($saml_doc).'" target="_blank">Click Here to view the guides.</a>
									</i>
								</div>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td style="width:200px;">
								<strong>Service Provider Name <span style="color:red;">*</span>:</strong>
							</td>
							<td>
								<input  type="text"
										name="idp_sp_name"
										id="idpName"
										placeholder="Service Provider Name"
										style="width: 95%;"
										value="'.(!empty($sp) ? esc_attr($sp->mo_idp_sp_name) : '').'"
										required
										pattern="^\w*$"
										title="Only alphabets, numbers and underscore is allowed"/>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
								<strong>SP Entity ID or Issuer <span style="color:red;">*</span>:</strong>
							</td>
							<td>
								<input  type="text"
										name="idp_sp_issuer"
										placeholder="Service Provider Entity ID or Issuer"
										style="width: 95%;"
										value="'.(!empty($sp) ? esc_url($sp->mo_idp_sp_issuer) : '').'"
										required />
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
								<strong>ACS URL <span style="color:red;">*</span>:</strong>
							</td>
							<td>
								<input  type="text"
										name="idp_acs_url"
										placeholder="AssertionConsumerService URL"
										style="width: 95%;"
										value="'.(!empty($sp) ? esc_url($sp->mo_idp_acs_url) : '').'"
										required />
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>NameID format <span style="color:red;">*</span>:</strong></td>
							<td>
								<!-- todo: Optimize this and get values from controller -->
								<select style="width:95%;" name="idp_nameid_format" required >
									<option value="">Select a NameID Format</option>
									<option value="1.1:nameid-format:emailAddress"
											'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'emailAddress') ? 'selected' : '').'>
											urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
									</option>
									<option value="1.1:nameid-format:unspecified"
											'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'unspecified') ? 'selected' : '').'>
											urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
									</option>
									<option value="2.0:nameid-format:transient"
											'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'transient') ? 'selected' : '').'>
											urn:oasis:names:tc:SAML:2.0:nameid-format:transient
									</option>
									<option value="2.0:nameid-format:persistent"
											'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'persistent') ? 'selected' : '').'>
											urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
									</option>
								</select>
								<br>
								<i>
									(<span style="color:red">NOTE: </span> Select urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress by default)
								</i>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
								<strong>Default Relay State (optional)</span>:</strong>
							</td>
							<td>
								<input  type="text"
										name="idp_default_relayState"
										placeholder="Default Relay State"
										style="width: 95%;"
										value="'.(!empty($sp) ? esc_attr($sp->mo_idp_default_relayState) : '').'"
										/>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<td><strong>Assertion Signed:</strong></td>
						<td>
							<input  type="checkbox"
									name="idp_assertion_signed"
									value="1"
									'.(!empty($sp) && esc_attr($sp->mo_idp_assertion_signed) ? 'checked' : '').'
									/>
							Check if you want to sign the SAML Assertion.
						</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
					</table>
					</fieldset>
					<fieldset>
						<legend><h2>TALENTLMS INTEGRATION</h2></legend>
						<table class="mo_idp_settings_table">
							<tr>
								<td><strong>TalentLMS URL :</strong></td>
								<td>
									<input type="url"
									   name="mo_idp_talentlms_url"
									   style="width: 95%;"
									   value="'.esc_attr($TalentLMS_URL).'"
									   placeholder="https://YOURDOMAIN.talentlms.com" />
									<br>
									<span>You can add your TalentLMS Domain here from your TalentLMS Accounts and Settings. </span>
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td><strong>TalentLMS API:</strong></td>
								<td>
									<input type="text"
									   name="mo_idp_talentlms_api"
									   style="width: 95%;"
									   value="'.esc_attr($TalentLMS_API).'"
									   placeholder="TalentLMS API"/>
									<br>
									<span>You can get your TalentLMS API from Accounts and Settings of your TalentLMS Admin settings.</span>
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
						</table>
					</fieldset>
					<table class="mo_idp_settings_table">						   
					<tr class="hide_button_top">
					<td>&nbsp;</td>
					<td>
						<br/>
						<input  type="submit"
								name="submit"
								id="Save"
								style="width:100px;margin-bottom:2%;"
								value="Save"
								class="button button-primary button-large"
								/> &nbsp;
						<input  type="button"
								name="test"
								id="testConfig"
								title="You can only test your Configuration after saving your Service Provider Settings."
								onclick="showTestWindow(\''.esc_url($test_window).'\');"
								'.(empty($sp) ? 'disabled' : '').'
								value="Test configuration"
								class="button button-primary button-large"
								style="margin-right: 2%;"/>
						<input  type="button"
								name="delete"
								class="button button-primary button-large"
								'.(empty($sp) ? "disabled" : "").'
								value="Delete SP Configuration"
								onclick = "window.location=\''.esc_url($delete_url).( !empty($sp) ? esc_attr($sp->id) : '' ).'\'"/>
					</td>
				</tr>
				</table>
				</form>
				<form id="add_sp" method="get" action="'.esc_url($post_url).'">
					<input type="hidden" name="page" value="'.esc_attr($spSettingsTabDetails->_menuSlug).'"/>
					<input type="hidden" name="action" value="add_wsfed_sp"/>
				</form>';
		}
		else
		{
			echo	'<p>Invalid SP. No Such Service Provider Found.</p>';
		}
		echo'</div>
			</div>';
	}