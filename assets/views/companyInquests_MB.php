<?php

$refDatas = get_post_meta($post->ID, '_' . self::getOptionNameMB_fn(), TRUE);
if ( ! $refDatas) :
	$refDatas = array();
	if( ! isset( $refDatas['INQUESTS'] ) ):
		$refDatas['INQUESTS'] = array();
	else:

	endif;
endif;

//-----------------------
$TD = self::getTD_fn();
#echo '<pre>' .print_r($shortcode_tags, TRUE). '</pre>';
$shortcode = '[companiesInquestsList company_id="' . $post->ID . '"]';
do_shortcode($shortcode);
?>  

<!--<table width="100%" border="0" cellpadding="3">
    <thead>
        <tr>
			<th align="center" width="30" scope="col"><input type="checkbox" class="removeAll" value="1"></th>
            <th scope="col"><?php _e('ID', $TD); ?></th>
            <th scope="col"><?php _e('Logo', $TD); ?></th>
            <th scope="col"><?php _e('Start', $TD); ?></th>
            <th scope="col"><?php _e('End', $TD); ?></th>
        </tr>
	</thead>
	<tbody>
        <tr>
			<td align="center"><input style="padding-left: 3px;" type="checkbox" class="removeItem" value="1"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
	<tfoot>
        <tr>
			<th align="center" scope="col"><input type="checkbox" class="removeAll" value="1"></th>
            <th scope="col"><?php _e('ID', $TD); ?></th>
            <th scope="col"><?php _e('Logo', $TD); ?></th>
            <th scope="col"><?php _e('Start', $TD); ?></th>
            <th scope="col"><?php _e('End', $TD); ?></th>
        </tr>
	</tfoot>
</table>
-->