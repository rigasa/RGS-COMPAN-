<?php
global $title;
$slug = RGS_Company::getSlug_fn();
$TD = RGS_Company::getTD_fn();
$pageID = RGS_Company::getAdminMenuId_fn();
// GET OPTIONS
//$rgsOptionID = RGS_CompanySettings::getOptionName_fn();
//$rgsOptions = get_option( $rgsOptionID );
$rgsOptions = RGS_CompanySettings::getOption_fn();
// check if the user have submitted the settings
// WordPress will add the "settings-updated" $_GET parameter to the url
if ( isset( $_GET[ 'settings-updated' ] ) ) {
	// add settings saved message with the class of "updated"
	add_settings_error( $slug . '_messages', $slug . '_message', __( 'Settings Saved', $TD ), 'updated' );
}

// show error/update messages
settings_errors( $slug . 'messages' );
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><i class="wp-menu-image dashicons-before dashicons-admin-settings" style="vertical-align: middle; position: relative; top: 2px;"></i> <?php echo esc_html($title); ?>
	</h1>

	<h2 class="nav-tab-wrapper">
<a href="#charts-options" class="nav-tab"><?php _e( 'Charts', $TD ); ?></a>
<a href="#form-options" class="nav-tab"><?php _e( 'Forms', $TD ); ?></a>
<a href="#informations" class="nav-tab"><?php _e( 'Informations', $TD ); ?></a>
</h2>

	<hr class="wp-header-end">

	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wporg"
		settings_fields( $pageID . '_chart' );
		?>
		<div id="charts-options" class="tabs">
			<?php do_settings_sections( $pageID . '_chart' ); ?>

			<div id="oneGraph" style="height: 400px; width: 600px;"></div>
			<br>
			<button id="oneGraphSave">
				<?php _e( 'Save Image', $TD ); ?>
			</button>
			<div id="output_image" style=""></div>
			<?php
			/*if(class_exists('ZC')): ?>

			<div id="phpGraph" style="height: 400px; width: 400px;"></div>

			<?php

			$zc = new ZC( "phpGraph" );
			$zc->setChartType( 'area' );
			$zc->setSeriesData( 0, array( 1, 4, 2, 6, 4 ) );
			$zc->render();
			?>
			<?php endif; */ ?>

		</div>
		<div id="form-options" class="tabs">
			
			<?php do_settings_sections( $pageID . '_form' ); ?>
			
			<?php do_settings_sections( $pageID . '_colors' ); ?>
			
			<?php
			/*$arrColors = RGS_CompanySettings::getDefaultColors_fn();
			$colors = RGS_CompanySettings::getColorsByName_fn();
			//
			$formChoice = (int) $rgsOptions['formChoice'];
			$themeChoice = $rgsOptions['themeChoice'];
			//
			$formArchitect = RGS_FormStats::getFormArchitect_fn($formChoice);
			//
			$qPos = 0;
			
			foreach($formArchitect as $formThemeId => $formThemeArr):
				$theFormThemeName = $formThemeArr['theme'];
				$theFormThemeQ = $formThemeArr['questions'];
				echo '<p>' . $theFormThemeName . '</p>';
				foreach($theFormThemeQ as $formQId => $formQ):
					echo '<p style="border: 1px solid #ccc; border-left: 10px solid ' .$arrColors[$qPos]. '; padding-left:6px;">' . $formQ . '</p>';
					$qPos ++;
				endforeach;
			endforeach;*/
			//echo '<pre>formArchitect::: '.print_r($formArchitect, TRUE).'</pre>';
			//----------------------------
			
			
			//$data = RGS_CompanyForm::getFormElem_fn( 329 );
			
			//echo '<p>SATAS::: '.print_r($data, TRUE).'</p>';
			
			
			/*$short = RGS_CompanyForm::getCampaignForm_fn();
			
			echo do_shortcode( $short );*/
			
			
			?>
			
		</div>
		<div id="informations" class="tabs">
			
		</div>

		<?php submit_button( __( 'Save Settings', $TD ) ); ?>
	</form>




</div>