<?php
global $post;
//
$refPrefix = self::getOptionNameMB_fn();
$refDatas = RGS_Company::getRefsDatas_fn( $post->ID );
//unset($refDatas['REFS']);
//update_post_meta($post->ID, $refPrefix, $refDatas);
//echo '<pre>IN MB::: '.print_r($refDatas, TRUE).'</pre>';

/*if(! isset( $refDatas['REF_Shortcode'] ) or empty( $refDatas['REF_Shortcode'] ) ):
	$theId = (int) $refDatas['REF_FormID'];
	$formName = RGS_CompanySettings::getFormNameById_fn( $theId );
	if( ! empty( $formName ) ):
		$refDatas['REF_Shortcode'] = addslashes( '[contact-form-7 id="' . $theId . '" title="' . $formName. '"]' ) ;
	endif;
endif;*/
//
$TD = self::getTD_fn();
?>   
		<input type="hidden" name="<?php echo self::getNonceName_fn(); ?>" id="<?php echo self::getNonceName_fn(); ?>" value="<?php echo wp_create_nonce( self::getNonceAction_fn() );?>" />

		<div class="row">
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label"><?php _e('Mail address', $TD); ?></label>
		</p>
			<div class="fields">
				<input type="email" class="regular-text" name="<?php echo $refPrefix; ?>[REF_MailAddress]" value="<?php echo $refDatas['REF_MailAddress']; ?>" />
				<p class="description"><?php _e("Respondent's email address", $TD); ?></p>
			</div>
		</div>

		<div class="row">
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"><?php _e('Number of employees', $TD); ?></label>
			</p>
			<div class="fields">
				<input type="number" name="<?php echo $refPrefix; ?>[REF_NbEmployees]" value="<?php echo $refDatas['REF_NbEmployees']; ?>" />
				<p class="description"><?php _e('Number of company employees', $TD); ?></p>
			</div>
		</div>
		
		<div class="row">
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"><?php _e('Form', $TD); ?></label>
			</p>
			<div class="fields">
				<?php
				RGS_CompanySettings::getSelectForm_fn( $refPrefix . '[REF_FormID]', $refDatas['REF_FormID'] );
				?>
				<p class="description"><?php _e('Select the form for company employees', $TD); ?></p>
			</div>
		</div>

		<div class="row">
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"><?php _e('Shortcode', $TD); ?></label>
			</p>
			<div class="fields">
				<input id="toCopy" type="text" class="large-text" name="<?php echo $refPrefix; ?>[REF_Shortcode]" value='<?php echo stripslashes( $refDatas['REF_Shortcode'] ); ?>' />
				
				<button id="copy" type="button"><?php _e("Write Shortcode", $TD); ?></button><br>
				<p class="description"><?php _e("Add this shortcode in editor.", $TD); ?></p>
			</div>
		</div>

		<?php
		//Template
		$theTemplate = $refDatas['REF_Template'];//RGS_CompanyMBoxes::getCurTemplate_fn($post->ID);
		$arrTemplates = wp_get_theme()->get_page_templates();
		?>
		<div class="row">
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"><?php _e('Template', $TD); ?></label>
			</p>
			<input id="templateField" type="hidden" name="<?php echo $refPrefix; ?>[REF_Template]" value='<?php echo $refDatas['REF_Template']; ?>' />
			<div class="fields">
				<select id="templateSelect">
					<option value=""<?php echo ( selected( '', $theTemplate, false ) ); ?>><?php echo __('Select template', $TD); ?></option>
					
					<?php
					foreach($arrTemplates as $key => $temp ):
					?>
					<option value="<?php echo $key; ?>"<?php echo ( selected( $key, $theTemplate, false ) ); ?>><?php echo __($temp, $TD); ?></option>
					<?php
					endforeach;
					?>
				</select>
				<p class="description"><?php _e("Select a template to view the company", $TD); ?></p>
			</div>
		</div>


<?php
// REF_Campaigns
?>