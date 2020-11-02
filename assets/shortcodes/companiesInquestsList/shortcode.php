
<?php
if( RGS_Company::isCustomPostType_fn( RGS_CompanyInquest::getCptName_fn() ) ):
	//
	if ( ! shortcode_exists( basename( dirname( __FILE__ ) ) ) ) :
		$shortcodeAttr = array(
			'name' => __( 'Inquests', RGS_Shortcodes::getTD_fn() ),
			'fields' => array( 
				'title' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-title',
					'title'          => esc_html__( 'Title', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => __( 'List of Inquests', RGS_Shortcodes::getTD_fn() ),
					'placeholder'    => '',
					'type'           => 'text'
				),
				'company_id' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-company_id',
					'title'          => esc_html__( 'Company ID', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => 0,
					'placeholder'    => '',
					'type'           => 'number'
				),
				'limit' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-limit',
					'title'          => esc_html__( 'Limit', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => -1,
					'placeholder'    => '',
					'type'           => 'number',
					'default'        => array(
										'min'   => -1,
										'max'   => 100,
										'step'  => 1
									)
				),
				'orderby' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-orderby',
					'title'          => esc_html__( 'Order by', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => 'menu_order',
					'placeholder'    => '',
					'type'           => 'select',
					'default'        => array(
										'ID'  => esc_html__( 'Id', RGS_Shortcodes::getTD_fn() ),
										'menu_order'  => esc_html__( 'Menu order', RGS_Shortcodes::getTD_fn() ),
										'title'  => esc_html__( 'Title', RGS_Shortcodes::getTD_fn() ),
										'date'  => esc_html__( 'Date', RGS_Shortcodes::getTD_fn() ),
										'rand'  => esc_html__( 'Rand', RGS_Shortcodes::getTD_fn() )
									)
				),
				'order' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-order',
					'title'          => esc_html__( 'Order', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => 'DESC',
					'placeholder'    => '',
					'type'           => 'select',
					'default'        => array(
										'ASC'  => esc_html__( 'Ascending', RGS_Shortcodes::getTD_fn() ),
										'DESC'  => esc_html__( 'Descending', RGS_Shortcodes::getTD_fn() ),
										'external'  => esc_html__( 'External', RGS_Shortcodes::getTD_fn() ),
									)
				),
				'items' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-items',
					'title'          => esc_html__( 'Exclude', RGS_Shortcodes::getTD_fn() ),
					'desc'           => __( 'Separate elements with commas.', RGS_Shortcodes::getTD_fn() ),
					'value'          => '',
					'placeholder'    => '',
					'type'           => 'text'
				),
				'category' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-category',
					'title'          => esc_html__( 'Category', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => '',
					'placeholder'    => '',
					'type'           => 'text'
				),
				'post_status' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-post_status',
					'title'          => esc_html__( 'Status', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => 'draft',
					'placeholder'    => '',
					'type'           => 'text'
				)
			),
			'content' => false,
			'dir' => dirname( __FILE__ ),
			'url' => trailingslashit( get_site_url() ) . str_replace( ABSPATH, '', dirname( __FILE__ ) ),
			'basename' => basename( dirname( __FILE__ ) )
		);
		//
		if( ! function_exists( 'rgsInquestsShortcode_fn' ) ):

			function rgsInquestsShortcode_fn( $args, $content = NULL, $tag )
			{
				//--------------------------------
				$defaults = RGS_Shortcodes::$gScItems[ basename( dirname( __FILE__ ) ) ][ 'fields' ];
				//--------------------------------
				$settings = array();
				foreach( $defaults as $key => $fieldArr ):
					$settings[ $key ] = ( isset( $args[ $key ] ) ) ? $args[ $key ] : $fieldArr['value'];
				endforeach;
				//--------------------------------
				if( empty( $settings[ 'limit' ] ) ):
					$settings[ 'limit' ] = '-1';
				endif;
				//--------------------------------
				$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
				$args = array(
					'post_type' 	=>  RGS_CompanyInquest::getCptName_fn(),
					'numberposts' 	=> $settings[ 'limit' ],
					'orderby'     	=> $settings[ 'orderby' ],
					'order'     	=> $settings[ 'order' ],
					'post_status'   => $settings[ 'post_status' ],
					'paged' 		=> $paged,
				);
				//--------------------------------
				if($settings[ 'company_id' ] > 0 ):
					$args['meta_key'] = 'COMPANY_ID';
					$args['meta_value'] = $settings[ 'company_id' ];
				endif;
				//--------------------------------
				if( $settings[ 'orderby' ] === 'rand' ) :
					unset( $args[ 'order' ] );
				endif;

				if( $settings[ 'order' ] === 'external' ) :
					unset( $args[ 'order' ] );
					unset( $args[ 'orderby' ] );
				endif;
				//--------------------------------
				if( $settings[ 'items' ] !== '' ) :
					$args['include'] = $settings[ 'items' ];
				endif;

				if( $settings[ 'category' ] !== '' ) :
					$args['category'] = $settings[ 'category' ];
				endif;
				//--------------------------------
				$arrItems = get_posts( $args );
				$total = count( $arrItems );
				//--------------------------------
				$dateFormat = get_option( 'date_format' );
				//
				$theHeadFooter = '<tr>
					<th align="center" width="30" scope="col"><input type="checkbox" class="removeAllInquests" value="1"></th>
					<th scope="col">' . __('ID', RGS_Shortcodes::getTD_fn()) . '</th>
					<th scope="col">' . __('Logo', RGS_Shortcodes::getTD_fn()) . '</th>
					<th scope="col">' . __('Name', RGS_Shortcodes::getTD_fn()) . '</th>
					<th scope="col">' . __('Form', RGS_Shortcodes::getTD_fn()) . '</th>
					<th scope="col">' . __('Points', RGS_Shortcodes::getTD_fn()) . '</th>
					<th scope="col">' . __('Campaign', RGS_Shortcodes::getTD_fn()) . '</th>
				</tr>';
				$_output = '';
				$_output .= '<p>';
				$_output .= '<a class="inquestDelete" href=""><span class="dashicons dashicons-trash"></span></a>';
				$_output .= '</p><hr>';

				$_output .= '<table width="100%" border="0" cellpadding="3">';
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
						$postThumbnail = get_the_post_thumbnail( $ITEM->ID, array(60, 60) );

						$theTitle = get_post_meta($ITEM->ID, 'POST_TITLE', TRUE);
						$theFormId = get_post_meta($ITEM->ID, 'FORM_ID', TRUE);
						$thePointsTotal = get_post_meta($ITEM->ID, 'pointsTotal', TRUE);
						$theCampaignId = get_post_meta($ITEM->ID, 'campaignid', TRUE);

						$_output .= '<tr align="center">';
						$_output .= '<td>';
						$_output .= '<input id="inquest-select-' .  $ITEM->ID . '>" style="padding-left: 3px;" type="checkbox" class="inquestItem" value="' . $ITEM->ID .'" />';
						$_output .= '</td>';

						$_output .= '<td>';
						$_output .= $ITEM->ID;
						$_output .= '</td>';
						
						$_output .= '<td>';
						$_output .= $postThumbnail;
						$_output .= '</td>';
						
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
				return $_output;
				//--------------------------------
			};
			//---------------------------------------------------------------------------
		endif;
		//
		add_shortcode( basename( dirname( __FILE__ ) ), 'rgsInquestsShortcode_fn' );
		//
	endif;
endif;