<?php
global $title;
$slug = RGS_Company::getSlug_fn();
$TD = RGS_Company::getTD_fn();
$pageID = RGS_Company::getAdminMenuId_fn();
//
$cptMessages = RGS_CompanyMsg::getCptName_fn();
//
//class Example_List_Table extends WP_List_Table {}
//$example_lt = new Example_List_Table();
// $refDatas = RGS_Company::getRefsDatas_fn( $post->ID );
//
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
	<h1 class="wp-heading-inline"><i class="wp-menu-image dashicons-before dashicons-email-alt" style="vertical-align: middle; position: relative; top: 2px;"></i> <?php echo esc_html($title); ?>
	</h1>
	<hr class="wp-header-end">
	
	<?php
	
	function deleteMessage_fn( $id, $notInTrash = true )
	{
		wp_delete_post( $id, $notInTrash);
	}
	
	function deleteAllMessages_fn()
	{
		$args = array(
			'numberposts' 	=> -1,
			'post_type'   	=> RGS_CompanyMsg::getCptName_fn(),
			'post_status' 	=> 'draft'
		);
		$post_list = get_posts( $args );
	
		foreach ( $post_list as $post ) :
			deleteMessage_fn( $post->ID, true);
		endforeach;
	}
	//
	//Protect against arbitrary paged values
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	$args = array(
		'numberposts' 	=> 10,
		'post_type'   	=> $cptMessages,
		'post_status' 	=> 'draft',
		'orderby'    	=> 'date',
		'paged' 		=> $paged,
    	'sort_order' 	=> 'ASC'
	);
	//
	#deleteAllMessages_fn();
	//
	?>
	<ul class="subsubsub">
		<li class="all">
			<a href="#" class="current">
				<?php _e('All'); ?> 
				<span class="count">(1)</span>
			</a> |
		</li>
		<li class="publish">
			<a href="#">
				<?php _e('Active'); ?> 
				<span class="count">(5)</span>
			</a>
		</li>
	</ul>
	<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th><?php _e('ID', $TD); ?></th>
			<th><?php _e('Uniq ID', $TD); ?></th>
			<th><?php _e('Title', $TD); ?></th>
			<th><?php _e('Created At', $TD); ?></th>
			<th><?php _e('Private', $TD); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('ID', $TD); ?></th>
			<th><?php _e('Uniq ID', $TD); ?></th>
			<th><?php _e('Title', $TD); ?></th>
			<th><?php _e('Created At', $TD); ?></th>
			<th><?php _e('Private', $TD); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		
	$post_list = get_posts( $args ); // this array holds all of your data
	if( !empty( $post_list ) ) :
 
		foreach( $post_list as $row ) : 
			$metas = get_post_meta($row->ID);
			// graphSeries
			// points _> total, points
			// createdAt
			// unserialize(tags)
			// unserialize(responses )
			?>
			<tr>
				<td><?php echo $row->ID; ?></td>
				<td><?php echo get_post_meta($row->ID, 'uniqId', true); ?></td>
				<td><?php echo get_post_meta($row->ID, 'POST_TITLE', true); ?></td>
				<td><?php echo get_post_meta($row->ID, 'createdAt', true); ?></td>
				<td><?php echo get_post_meta($row->ID, 'private', true); ?></td>
			</tr>
			<?php
		endforeach;
	else : ?>
		<tr>
			<td colspan="3"><?php _e('No data found', $TD); ?></td>
		</tr>
		<?php 
	endif; 
	wp_reset_postdata();
	?>	
	</tbody>
</table>
	<?php
	$big = 999999999; // need an unlikely integer

	echo paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $post_list->max_num_pages
	) );
	?>
</div>
