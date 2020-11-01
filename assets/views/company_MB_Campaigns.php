<?php
global $post;
//-----------------------
$TD = RGS_Company::getTD_fn();
//
$refPrefix = RGS_CompanyMBoxes::getOptionNameMB_fn();
$refDatas = RGS_Company::getRefsDatas_fn( $post->ID );
// // REF_Campaigns
$campaigns = !empty($refDatas['REF_Campaigns']) ? $refDatas['REF_Campaigns'] : array();
//
$args = array(
    'post_type' 	=>  RGS_CompanyCampaigns::getCptName_fn(),
    'numberposts' 	=> -1,
    'orderby'     	=> 'menu_order',
    'order'     	=> 'DESC',
    'post_status'   => 'publish'
);
//--------------------------------
$arrItems = get_posts( $args );
$total = count( $arrItems );
//-----
//echo '<pre>$campaigns::: '.print_r($campaigns, true).'</pre>';
?>  

<table width="100%" border="0" cellpadding="3">
    <thead>
        <tr>
			<th align="center" width="30" scope="col"><input type="checkbox" class="removeAll" value="1"></th>
            <th scope="col"><?php _e('ID', $TD); ?></th>
            <th scope="col"><?php _e('Logo', $TD); ?></th>
            <th scope="col"><?php _e('Name', $TD); ?></th>
            <th scope="col"><?php _e('Start', $TD); ?></th>
            <th scope="col"><?php _e('End', $TD); ?></th>
            <th scope="col"><?php _e('Active', $TD); ?></th>
        </tr>
	</thead>
<tbody>
    <?php
    if( $total === 0 ) : ?>
        <tr id="empty-table">
			<td align="center" colspan="6"><?php _e('No campaigns was retrieved', $TD ) ; ?></td>
        </tr>
    <?php else:
        //
		foreach( $arrItems as $ITEM ) :

            $mStartDate = get_post_meta( $ITEM->ID, 'startDate', TRUE);
            $mEndDate = get_post_meta( $ITEM->ID, 'endDate', TRUE);

            $inCampaign = RGS_CompanyCampaigns::inCampaign_fn($mStartDate, $mEndDate );
            $inSyle = '';
            if($inCampaign):
                $inSyle =  '<span class="dashicons dashicons-yes" style="color:green;"></span> ';
            endif;
            //
            $postThumbnail = get_the_post_thumbnail( $ITEM->ID, array(60, 60) );
            $metaStartDate = ! empty ($mStartDate) ? $mStartDate : '';
            $metaEndDate = ! empty($mEndDate) ? $mEndDate : '';
            if(!empty( $postThumbnail ) ):
                //$HTML .= '<div class="image">' . $postThumbnail .' </div>';
            endif;
    ?>
        <tr align="center">
			<td>
            <?php
            $checked = in_array($ITEM->ID, $campaigns) ? ' checked="checked"' : '';            ?>
            <input name="<?php echo $refPrefix; ?>[REF_Campaigns][]" id="b-select-<?php echo $ITEM->ID; ?>" style="padding-left: 3px;" type="checkbox" class="tableItem" value="<?php echo $ITEM->ID; ?>"<?php echo  $checked ?> /></td>
            <td><?php echo $ITEM->ID; ?></td>
            <td><?php echo $postThumbnail; ?></td>
            <td><?php echo $ITEM->post_title; ?></td>
            <td><?php echo $metaStartDate; ?></td>
            <td><?php echo $metaEndDate; ?></td>
            <td><?php echo $inSyle; ?></td>
        </tr>
   <?php 
        endforeach;
        wp_reset_postdata();
    endif; ?>
    </tbody>
	<tfoot>
        <tr>
			<th align="center" scope="col"><input type="checkbox" class="removeAll" value="1"></th>
            <th scope="col"><?php _e('ID', $TD); ?></th>
            <th scope="col"><?php _e('Logo', $TD); ?></th>
            <th scope="col"><?php _e('Name', $TD); ?></th>
            <th scope="col"><?php _e('Start', $TD); ?></th>
            <th scope="col"><?php _e('End', $TD); ?></th>
            <th scope="col"><?php _e('Active', $TD); ?></th>
        </tr>
	</tfoot>
</table>
