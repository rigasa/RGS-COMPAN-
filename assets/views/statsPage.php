<?php


/*$optName = EC_CompanySettings::getOptionName_fn();
$settings = EC_CompanySettings::getOption_fn($optName);
$def = (array) EC_CompanySettings::getDefaultOptions_fn();


echo '<pre>option in::: '.print_r($settings, TRUE).'</pre>';
echo '<pre>defaults::: '.print_r( $def, TRUE).'</pre>';

delete_option( $optName );

$settings = EC_CompanySettings::getOption_fn($optName);

echo '<pre>option out::: '.print_r($settings, TRUE).'</pre>';

die('DEBUG');*/
global $title;
$slug = EC_Company::getSlug_fn();
$TD = EC_Company::getTD_fn();
$pageID = EC_Company::getAdminMenuId_fn();
$pageURL = '?page=' . self::getStatsMenuId_fn() . '&tab=';

$pTab = ( ! empty($pTab) ) ? $pTab : 'companies';
$activeTab = ( isset( $_GET[ 'tab' ] ) ) ? $activeTab = $_GET[ 'tab' ] : $activeTab = $pTab;
// GET OPTIONS
$rgsOptions = EC_CompanySettings::getOption_fn();
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><i class="wp-menu-image dashicons-before dashicons-chart-line" style="vertical-align: middle; position: relative; top: 2px;"></i> <?php echo esc_html($title); ?>
	</h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $pageURL; ?>companies" class="nav-tab<?php echo $activeTab == 'companies' ? ' nav-tab-active' : ''; ?>"><?php _e( 'Companies', $TD ); ?></a>
		<a href="<?php echo $pageURL; ?>public" class="nav-tab<?php echo $activeTab == 'public' ? ' nav-tab-active' : ''; ?>"><?php _e( 'Public', $TD ); ?></a>
		<a href="<?php echo $pageURL; ?>info" class="nav-tab<?php echo $activeTab == 'info' ? ' nav-tab-active' : ''; ?>"><?php _e( 'Informations', $TD ); ?></a>
	</h2>

	<hr class="wp-header-end">


	<?php if( $activeTab == 'companies' ): ?>
		<div id="companies" class="tabs">
			<?php
			?>
			<?php
			//----------------------------
			$allMsg = EC_FormStats::getAllMsg_fn();
			//----------------------------
			$companiesList = EC_FormStats::getCompaniesList_fn();
			$nbCompanies = count($companiesList);
			$nbForms = count($allMsg);
			//
			if($companiesList):
				$selected = 0;
				// ?>
				<br>
			<label for="companiesList"><?php _e( 'Companies', $TD ); ?>: </label>
				<select id="companiesList">
					
					<option value="0" <?php echo ( selected( $selected, "0", false ) ); ?>>
						<?php esc_html_e( 'All Companies', $TD ); ?>
					</option>
					
					<?php
					foreach ( $companiesList as $theId => $theName ): ?>

						<option value="<?php echo $theId; ?>" <?php echo ( selected( $selected, "$theId", false ) ); ?>>
						<?php esc_html_e( $theName, $TD ); ?>
					</option>

					<?php endforeach; ?>

				</select><br><span style="margin-left: 20px;"><?php echo __( 'Number Companies', $TD ) . ': <span style="font-weight: bold;">' . $nbCompanies .'</span>'; ?></span>
				
				</select><br><span style="margin-left: 20px;"><?php echo __( 'Number Forms', $TD ) . ': <span style="font-weight: bold;">' . $nbForms .'</span>'; ?></span>
			
			<br>
			<hr>
			<br>
			<?php endif; ?>
			
			<?php
			
			
			
			
			
			$formChoice = (int) $rgsOptions['formChoice'];
			$themeChoice = $rgsOptions['themeChoice'];
			//----------------------------
			//$allMsg = EC_FormStats::getAllMsg_fn();
			//
			if( empty( $allMsg ) ) : ?>
					<p class="noDataFound"><?php _e('No data found', $TD); ?></p>
			<?php else: ?>
				<div id="architectContainer">
				<?php
				//---------------------------------------------------------
				echo EC_FormStats::getArchitectHtml_fn( $allMsg , $formChoice );
				//---------------------------------------------------------
				?>
				</div>
			<?php endif; ?>
			<br>
			<hr>
			<div id="chartByPoints">
				<div id="pointsChart" style="width:100%; height: 500px;"></div>
			</div>
			<hr>
			<div id="chartBySections">
				<div id="sectionsGraph"></div>
				<br>
				<button id="sectionsGraph-save" class="button button-secondary">
					<?php _e( 'Save Image', $TD ); ?>
				</button>
				<button id="sectionsGraph-clear" class="button button-secondary">
					<?php _e( 'Clear Image', $TD ); ?>
				</button>
				<div id="sectionsGraph-photo" class="output-img"></div>
			</div>
			<hr>
			<br>
		</div>
	<?php elseif( $activeTab == 'public' ): ?>
		<div id="public" class="tabs">
			<?php
			//
			//
			?>
		</div>
	<?php else: ?>
		<div id="info" class="tabs">
			<p>
			<a title="JavaScript Charts by ZingChart" style="text-decoration: none; color: rgb(109, 110, 113) !important; font-size: 11px !important; display: block !important; opacity: 1 !important; font-family: &quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, &quot;Lucida Sans&quot;, Helvetica, Arial, sans-serif;" href="http://www.zingchart.com/?origin=http://www.genevedurable.ch&amp;pathname=/~projets/WP-ASDD/wp-admin/admin.php"><?php _e('Powered by', $TD); ?> <span style="color:#00384A; font-weight:bold;">ZingChart</span></a></p>
		</div>
	<?php endif; ?>

</div>