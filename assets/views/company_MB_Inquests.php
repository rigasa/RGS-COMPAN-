<?php
global $post;
//
$refPrefix = self::getOptionNameMB_fn();
$refDatas = RGS_Company::getRefsDatas_fn( $post->ID );
//-----------------------
$TD = self::getTD_fn();
//
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$args = array(
	'post_type' 	=>  RGS_CompanyInquest::getCptName_fn(),
	'numberposts' 	=> -1,
	'orderby'     	=> 'menu_order',
	'order'     	=> 'DESC',
	'post_status'   => 'draft',
    'paged' 		=> $paged,
    'meta_key'      => 'COMPANY_ID',
    'meta_value'    => $post->ID
);
//--------------------------------
$arrItems = get_posts( $args );
$total = count( $arrItems );
//--------------------------------
$dateFormat = get_option( 'date_format' );
//
$theHeadFooter = '<tr>';
    $theHeadFooter .= '<th style="width: 24px;><input type="checkbox" class="removeAllInquests" value="1"></th>';
    $theHeadFooter .= '<th scope="col" style="width: 40px;">' . __('ID', RGS_Shortcodes::getTD_fn()) . '</th>';
    //$theHeadFooter .= '<th scope="col">' . __('Logo', RGS_Shortcodes::getTD_fn()) . '</th>';
    $theHeadFooter .= '<th scope="col">' . __('Name', RGS_Shortcodes::getTD_fn()) . '</th>'; 
    $theHeadFooter .= '<th scope="col">' . __('Form', RGS_Shortcodes::getTD_fn()) . '</th>';
    $theHeadFooter .= '<th scope="col">' . __('Points', RGS_Shortcodes::getTD_fn()) . '</th>';
    $theHeadFooter .= '<th scope="col">' . __('Campaign', RGS_Shortcodes::getTD_fn()) . '</th>';
$theHeadFooter .= '</tr>';

$_output = '';
$_output .= '<p>';
$_output .= '<a class="inquestDelete" href=""><span class="dashicons dashicons-trash"></span></a>';
$_output .= '</p><hr>';

$_output .= '<table id="inquest-table" width="100%" border="0" cellpadding="3" class="wp-list-table widefat fixed striped pages">';
$_output .= '<thead>';
$_output .= $theHeadFooter;
$_output .= '</thead>';
$_output .= '<tbody>';
if( $total === 0 ) :
    $_output = '<tr id="empty-table">
    <td align="center" colspan="6">' . addslashes(__('No inquest was retrieved', RGS_Shortcodes::getTD_fn() ) ) . '</td>
    </tr>';
else:
    foreach( $arrItems as $ITEM ) :

        setup_postdata( $ITEM );

        $theContent = wp_trim_words( $ITEM->post_content, 100 );
        //$postThumbnail = get_the_post_thumbnail( $ITEM->ID, array(60, 60) );

        $theTitle = get_post_meta($ITEM->ID, 'POST_TITLE', TRUE);
        $theFormId = get_post_meta($ITEM->ID, 'FORM_ID', TRUE);
        if( ! empty( $theFormId ) ):
            $theFormId = get_the_title( $theFormId );
        endif;
        $thePointsTotal = get_post_meta($ITEM->ID, 'pointsTotal', TRUE);
        $theCampaignId = get_post_meta($ITEM->ID, 'campaignid', TRUE);
        if( ! empty( $theCampaignId ) ):
            $theCampaignId = get_the_title( $theCampaignId );
        endif;
        $_output .= '<tr class="inquest-line">';
        $_output .= '<td>';
        $_output .= '<input id="inquest-select-' .  $ITEM->ID . '" style="padding-left: 3px;" type="checkbox" class="inquestItem" value="' . $ITEM->ID .'" />';
        $_output .= '</td>';

        $_output .= '<td>';
        $_output .= $ITEM->ID;
        $_output .= '</td>';
        
        //$_output .= '<td>';
        //$_output .= $postThumbnail;
        //$_output .= '</td>';
        
        $_output .= '<td>';
        $_output .= $theTitle;
        $_output .= '</td>';

        $_output .= '<td>';
        $_output .= $theFormId;
        $_output .= '</td>';

        $_output .= '<td>';
        $_output .= $thePointsTotal;
        $_output .= '</td>';

        $_output .= '<td>';
        $_output .= $theCampaignId;
        $_output .= '</td>';

        $_output .= '</tr>';
    endforeach;
endif;
$_output .= '</tbody>';
$_output .= '<tfoot>';
$_output .= $theHeadFooter;
$_output .= '</tfoot>';
$_output .= '</table>';

wp_reset_postdata();

/*$_output .= '<div class="company-inquest-container">';
if( ! empty( $settings[ 'title' ] ) ):
    $_output .= '<h1 class="center">'.$settings[ 'title' ].'</h1>';
endif;
$_output .= '<div class="company-inquest-list-container">';
$_output .= '<ul id="company-inquest-list">'.$HTML.'</ul>';
$_output .= '<div id="news-navigation">';
$_output .= '<a id="prev2" class="prev" href="#"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>';
$_output .= '<a id="next2" class="next" href="#"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
$_output .= '</div>';
$_output .= '</div>';
$_output .= '</div>';*/

echo $_output;            



//$shortcode = '[companiesInquestsList company_id="' . $post->ID . '"]';
//do_shortcode($shortcode);
?>  
