<?php
global $post;
//
$refPrefix = self::getOptionNameMB_fn();
$refDatas = EC_Company::getRefsDatas_fn( $post->ID );
//
$TD = self::getTD_fn();
?>   
		<input type="hidden" name="<?php echo self::getNonceName_fn(); ?>" id="<?php echo self::getNonceName_fn(); ?>" value="<?php echo wp_create_nonce( self::getNonceAction_fn() );?>" />

		<div class="row">
			<div class="label"><?php _e('Mail address', $TD); ?></div>
			<div class="fields">
				<input type="email" class="regular-text" name="<?php echo $refPrefix; ?>[REFS][REF_MailAddress]" value="<?php echo $refDatas['REFS']['REF_MailAddress']; ?>" />
				<span class="description"><?php _e("Respondent's email address", $TD); ?></span>
			</div>
		</div>

		<div class="row">
			<div class="label"><?php _e('Number of employees', $TD); ?></div>
			<div class="fields">
				<input type="number" name="<?php echo $refPrefix; ?>[REFS][REF_NbEmployees]" value="<?php echo $refDatas['REFS']['REF_NbEmployees']; ?>" />
				<span class="description"><?php _e('Number of company employees', $TD); ?></span>
			</div>
		</div>
		
		<div class="row">
			<div class="label"><?php _e('Form', $TD); ?></div>
			<div class="fields">
				<?php
				EC_CompanySettings::getSelectForm_fn( $refPrefix . '[REFS][REF_FormID]', $refDatas['REFS']['REF_FormID'] );
				?>
				<span class="description"><?php _e('Select the form for company employees', $TD); 
					// $shortcode = addslashes( '[contact-form-7 id="' . $theId . '" title="' . $theName. '"]' ) ;
					?></span>
			</div>
		</div>

		<div class="row">
			<div class="label"><?php _e('Shortcode', $TD); ?></div>
			<div class="fields">
				<input id="toCopy" type="text" class="large-text" name="<?php echo $refPrefix; ?>[REFS][REF_Shortcode]" value='<?php echo $refDatas['REFS']['REF_Shortcode']; ?>' />
				
				<button id="copy" type="button"><?php _e("Copy in clipboard", $TD); ?></button><br>
				<span class="description"><?php _e("Add this shortcode in editor.", $TD); ?></span>
			</div
		