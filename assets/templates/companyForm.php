<?php
//////////////////////////////////
global $post;
//////////////////////////////////
$TD = RGS_Company::getTD_fn();
//////////////////////////////////
// HAS PASSWORD
//////////////////////////////////
$passCookie = '';
//
foreach($_COOKIE as $key=>$value) :
  	if(!strncmp($key,"wp-postpass_",12)) :
		$passCookie = $key;
    	break;
	endif;
endforeach;
//
$hasPass = (isset($post->post_password) and ! empty( $post->post_password ) );
//
//////////////////////////////////
$isCompanyForm = ( $post->post_type == RGS_Company::getCPT_fn() );
//////////////////////////////////
//$taxoHTML = RGS_Company::getTaxo_fn( $post->ID, RGS_Company::getSlug_fn() . '-campaign');
//////////////////////////////////
$user = get_user_by( 'login', 'rigasa' );
$curUser = wp_get_current_user();

if($user and $curUser ):
	if(isset($user->caps['administrator']) 
		and $user->caps['administrator'] == 1 
		and isset($curUser->ID) 
		and $curUser->ID == $user->ID
	):
		//echo 'User is admin';
	else:
		//echo 'User NOT is admin';
	endif;
endif;
//////////////////////////////////
if( $isCompanyForm ): 
	//-------------------------------
	$thumb = '';
	if ( has_post_thumbnail($post->ID) ) :
		$thumb = get_the_post_thumbnail($post->ID, 'post-thumbnail', array( 'class' => 'company-logo-header' ));
	endif;
	//-------------------------------
	?>
	<h1><?php echo $thumb . $post->post_title; ?></h1>
	<?php
	$mBoxMeta = RGS_Company::getRefsDatas_fn( $post->ID);
	//////////////////////////////////////
	$nbEmployees = isset($mBoxMeta['REF_NbEmployees'])? (int) $mBoxMeta['REF_NbEmployees'] : 10;
	
	// Get NUMBER OF INQUESTS
	$nbInquests = (int) RGS_FormStats::getNbInquestInCompany_fn( $post->ID );

	$inInquests = ($nbInquests < $nbEmployees);

	if($inInquests):
		// CHECK CAMPAIGNS
		$campaignsHTML = '';
		$arrInclude = array();
		//$arrLines = RGS_CompanyCampaigns::getActivesCampaigns_fn($mBoxMeta['REF_Campaigns']);
		$arrLines = RGS_CompanyCampaigns::getActivesCampaign_fn($mBoxMeta['REF_Campaigns']);
		//
		foreach($arrLines as $line):
			$lineHTML = '';
			//title,thumb,start,end,icon,id
			$lineHTML .= '<div data-campaign="'. $line['id'] . '" id="campaignItem" class="campaign-item">';

			if( ! empty( $line['thumb'] ) ):
				$lineHTML .= '<span class="campaign-cell-image">' . $line['thumb'] . '</span>';
			endif;

			$lineHTML .= '<span class="campaign-cell-title"><strong>'. $line['title'] . '</strong></span><br>';
			$lineHTML .= '<span class="campaign-cell-start"><i>'.__('Start', $TD) . ': </i>' . $line['start']  . '</span><br>';
			$lineHTML .= '<span class="campaign-cell-end"><i>'.__('End', $TD) . ': </i>' . $line['end']  . '</span>';
			$lineHTML .= '</div>';
			//
			$arrInclude[] = $lineHTML;
			//
		endforeach;
		//
		if( ! empty($arrInclude) ):

			$campaignsHTML = '<div id="campaigns-list">';
			$campaignsHTML .= '<h2>' . __('Campaign', $TD) .'</h2>';
			
			foreach($arrInclude as $lineInclude ):
				$campaignsHTML .= $lineInclude;
			endforeach;

			$campaignsHTML .= '</div>';
		endif;


	endif;

else :
	$inInquests = TRUE;
endif;
// DEBUG Inquest Max ////////////////////////
#$inInquests = FALSE;
////////////////////////////////////////////
?>
<?php 
if( ! $inInquests and $isCompanyForm ) :
	echo '<p class="warning">'.__('Maximum number of inquests reached!', $TD).' : ' . $nbEmployees . '</p>';
else:

	$theContent = do_shortcode( get_the_content($post->ID) );
	if($isCompanyForm):
		// Display Number of 
		if( $nbEmployees > 0 ): //REF_MailAddress, REF_FormID, REF_Shortcode
			$theNbEmployees = '<p>'. __('Number of inquests', $TD) . ': <span id="nbOfInquests">' . $nbInquests . '</span>/<span id="nbMaxInquests">' . $nbEmployees . '</span></p>';
		endif;

		if( $hasPass and ! empty($passCookie) ):
			// Display Full
			echo $theNbEmployees . $campaignsHTML . $theContent;
		else:
			if( ! $hasPass ):
				// Display Full
				echo $theNbEmployees . $campaignsHTML . $theContent;
			else:
				// Display Password
				echo $theContent;
			endif;
		endif;

	else:
		// display form
		echo $theContent;
	endif;
	//
endif;
?>

<br><br><br>
