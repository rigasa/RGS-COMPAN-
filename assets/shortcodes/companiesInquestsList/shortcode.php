
<?php
if( RGS_Company::isCustomPostType_fn( RGS_CompanyInquest::getCptName_fn() ) ):
	//
	if ( ! shortcode_exists( basename( dirname( __FILE__ ) ) ) ) :
		$_shc_attr = array(
			'name' => __( 'Inquests', RGS_Shortcodes::getTD_fn() ),
			'fields' => array( 
				'title' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-title',
					'title'          => esc_html__( 'Title', RGS_Shortcodes::getTD_fn() ),
					'desc'           => '',
					'value'          => __( 'Stay tuned. Read our news', RGS_Shortcodes::getTD_fn() ),
					'placeholder'    => '',
					'type'           => 'text'
				),
				'companyID' => array(
					'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-companyID',
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

			function rgsInquestsShortcode_fn( $_atts, $_content = NULL, $_tag )
			{
				//--------------------------------
				$defaults = rgs_Shortcodes::$_sc_items[ basename( dirname( __FILE__ ) ) ][ 'fields' ];
				//--------------------------------
				$settings = array();
				foreach( $defaults as $_key => $_field_arr ):
					$settings[ $_key ] = ( isset( $_atts[ $_key ] ) ) ? $_atts[ $_key ] : $_field_arr['value'];
				endforeach;
				//--------------------------------
				if( empty( $settings[ 'limit' ] ) ):
					$settings[ 'limit' ] = '-1';
				endif;
				//--------------------------------
				$args = array(
					'post_type' 	=>  RGS_CompanyInquest::getCptName_fn(),
					'numberposts' 	=> $settings[ 'limit' ],
					'orderby'     	=> $settings[ 'orderby' ],
					'order'     	=> $settings[ 'order' ],
					'post_status'   => $settings[ 'post_status' ]
				);
				if($companyID > 0 ):
					$args['meta_key'] = 'COMPANY_ID';
					$args['meta_value'] = $companyId;
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
				if( $total === 0 ) return;
				//--------------------------------
				$dateFormat = get_option( 'date_format' );
				$HTML = '';
				//
				foreach( $arrItems as $ITEM ) :

					setup_postdata( $ITEM );

					$theContent = wp_trim_words( $ITEM->post_content, 100 );
					$postThumbnail = get_the_post_thumbnail( $ITEM->ID, 'post-thumbnail' );
					$HTML .= '<li>';
					$HTML .= '<div class="company-inquest">';
					$HTML .= '';
					if(!empty( $postThumbnail ) ):
						$HTML .= '<div class="image">' . $postThumbnail .' </div>';
					endif;
					$HTML .= '';
					$HTML .= '<div class="mask">';
					$HTML .= '';
					$HTML .= '<a class="company-inquest-link" href="'.get_permalink( $ITEM->ID ).'" title="'.$ITEM->post_title.'"> </a>';
					$HTML .= '';
					$HTML .= '</div>';
					$HTML .= '';
					$HTML .= '</div>';
					$HTML .= '';
					$HTML .= '<div class="company-inquest-intro">';
					$HTML .= '';
					$HTML .= '<a class="company-inquest-link2" href="'.get_permalink( $ITEM->ID ).'"><h5>'.$ITEM->post_title.'</h5></a>';
					$HTML .= '';

					$HTML .= '<p class="news-date">'. get_the_date( $dateFormat, $ITEM->ID ).'</p>';
					$HTML .= '';
					if( ! empty( $theContent ) ):
						$HTML .= '<div>'. $theContent .'...</div>';
					endif;
					$HTML .= '';
					$HTML .= '<a class="btn-small open3" href="'.get_permalink( $ITEM->ID ).'" title="'.$ITEM->post_title.'"><i class="fa fa-arrow-right"></i>&nbsp;'.__( 'View Details', RGS_Shortcodes::getTD_fn() ).'</a>';
					$HTML .= '';
					$HTML .= '</div>';
					$HTML .= '';
					$HTML .= '</li>';

				endforeach;
				wp_reset_postdata();


				$_output = '';
				$_output .= '<div class="company-inquest-container">';
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
				$_output .= '</div>';

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