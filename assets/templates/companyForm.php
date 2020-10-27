<?php
//////////////////////////////////
$slug = RGS_Company::getSlug_fn();
$TD = RGS_Company::getTD_fn();
//////////////////////////////////
global $post;
//////////////////////////////////
// HAS PASSWORD
//////////////////////////////////
$passCookie = '';
foreach($_COOKIE as $key=>$value) :
  	if(!strncmp($key,"wp-postpass_",12)) :
		$passCookie = $key;
    	break;
	endif;
endforeach;
#echo '<pre>$hasPassCookie::: '.print_r($passCookie, TRUE).'</pre>';
$hasPass = (isset($post->post_password) and ! empty( $post->post_password ) );
#echo '<pre>$hasPass::: '.print_r($hasPass, TRUE).'</pre>';
//////////////////////////////////






$output = ''; 
$TAXO = RGS_Company::getSlug_fn() . '-campaign';
$taxoHTML = '';
$isInRange = FALSE;
$isPublicForm = TRUE;
//
$terms = get_the_terms( $post->ID, $TAXO );
if ( ! empty( $terms ) ) :
	//
	$isPublicForm = FALSE;
	$countInRange = array();
	//
	foreach($terms as $term):
		//
		$termID = $term->term_id;
		$termName = $term->name;
		// 
		$termMETAS = RGS_Company::campaignTaxoGetCustomFields_fn( $term->term_id );
		$startDate = $termMETAS['startDate']; 
		$endDate = $termMETAS['endDate']; 
		$cLogo = $termMETAS['cLogo']; 
		//
		/*$dFORMAT = 'Y-m-d'; //'d/m/Y';
		//
		$moreDAYS = 14; // 2 weeks
		$DATE = date($dFORMAT);
		$DATE_END = RGS_Company::getMoreDays_fn( $DATE, $moreDAYS, $dFORMAT  );
		//
		$DATE = date($dFORMAT, strtotime($DATE . ' +' . 15 . $moreDAYS . ' days'));
		//
		#echo '<pre>term:: '.print_r($term, TRUE).'</pre>';
		echo '<pre>termMID:: '.print_r($termID, TRUE).'</pre>';
		echo '<pre>termMETAS:: '.print_r($termMETAS, TRUE).'</pre>';
		echo '<pre>DATE:: '.print_r($startDate, TRUE).'</pre>';
		echo '<pre>DATE_END:: '.print_r($endDate, TRUE).'</pre>';*/
		$termInRange = RGS_Company::isDateRange_fn($startDate, $endDate);
		//
		if($termInRange) :
			//
			$termHTML = '<div id="term-' . $term->term_id . '" class="campaign-row" style="border:1px solid #000000; padding:6px;">';
			$image = wp_get_attachment_image_src ( $cLogo, 'thumbnail', FALSE );
			$imgSrc = '';
			//
			if( is_array( $image ) and isset( $image[0] ) ):
				$imgSrc = $image[0] ;
			endif;
			//
			if( ! empty( $imgSrc ) ):
				$termHTML .= '<span class="campaign-cell-image" style="float:left; margin-right:6px;"><img width="auto" height="66" src="' . $imgSrc . '" class="cLogoImage" alt="" style="height: 66px; width: auto; margin:0px;"></span>';
			endif;

			$termHTML .= '<span class="campaign-cell-title"><strong>'. $termName . '</strong></span><br>';
			$termHTML .= '<span class="campaign-cell-start"><i>'.__('Start', $TD) . ': </i>' . $startDate . '</span><br>';
			$termHTML .= '<span class="campaign-cell-end"><i>'.__('End', $TD) . ': </i>' . $endDate . '</span>';
			$termHTML .= '</div>';
			//
			$countInRange[] = $termHTML;
			//
		endif;
		//
	endforeach;
	//---------------------
	if( ! empty($countInRange) ):

		$taxoHTML = '<div id="campaigns-list" style="width:calc( 100% - 0px); padding-bottom:10px; ">';
		$taxoHTML .= '<h2 style="margin-bottom: 8px;">' . __('Campaigns', $TD) .'</h2>';
		foreach($countInRange as $termDisplay ):
			$taxoHTML .= $termDisplay;
		endforeach;
		$isInRange = TRUE;

		$taxoHTML .= '</div>';
	endif;
	//---------------------
	//
else:
	$isInRange = TRUE;
endif;


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

if( ! $isPublicForm ): 
//-------------------------------
$thumb = '';
if ( has_post_thumbnail($post->ID) ) :
	$thumb = get_the_post_thumbnail($post->ID, 'post-thumbnail', array( 'class' => 'company-logo-header' ));
endif;
//-------------------------------
?>
	<h1><?php echo $thumb . $post->post_title; ?></h1>
	<?php

//$pm = get_post_meta( $post->ID);

//echo '<pre>'.print_r( $pm, TRUE ).'</pre>';
	$mBoxMeta = get_post_meta( $post->ID, 'company_MB');
	$nbEmployees = isset($mBoxMeta[0]['REFS']['REF_NbEmployees'])? (int) $mBoxMeta[0]['REFS']['REF_NbEmployees'] : 10;
	// Get NUMBER OF INQUESTS
	$nbInquiries = (int) RGS_FormStats::getNbInquestInCompany_fn( $post->ID );

	$display = ($nbInquiries < $nbEmployees);
else :
	$display = TRUE;
endif;
//
?>
<?php 
if(! $display ) :
	echo '<p class="warning">'.__('Maximum number of inquiries reached!', $TD).' : ' . $nbEmployees . '</p>';
else:

	$theContent = do_shortcode( get_the_content($post->ID) );

	// Display Number of 
	if( $nbEmployees > 0 ): //REF_MailAddress, REF_FormID, REF_Shortcode
		$theNbEmployees = '<p id="nbOfInquiries">'. __('Number of inquiries', $TD) . ': ' . $nbInquiries . '/' . $nbEmployees . '</p>';
	endif;

	if( $hasPass and ! empty($passCookie) ):
		// Display Full
		echo $theNbEmployees . $taxoHTML . $theContent;
	else:
		if( ! $hasPass ):
			// Display Full
			echo $theNbEmployees . $taxoHTML . $theContent;
		else:
			echo $theContent;
		endif;
	endif;

	

	//the_content();
	wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', $TD ), 'after' => '</div>' ) ); 
endif;
?>

<br><br><br>
