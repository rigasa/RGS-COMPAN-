<?php
if ( ! shortcode_exists( basename( dirname( __FILE__ ) ) ) ) :
	$shortcodeAttr = array(
		'name' => __( 'Insert page', RGS_Shortcodes::getTD_fn() ),
		'fields' => array( 
			'id' => array(
				'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-id',
				'title'          => esc_html__( 'ID', RGS_Shortcodes::getTD_fn() ),
				'desc'           => '',
				'value'          => '',
				'placeholder'    => '',
				'type'           => 'text'
			),
			'slug' => array(
				'id'             => 'rgs-sc-' . basename( dirname( __FILE__ ) ) . '-slug',
				'title'          => esc_html__( 'Slug', RGS_Shortcodes::getTD_fn() ),
				'desc'           => '',
				'value'          => 'sample-page',
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
	if( ! function_exists( 'rgsShortcodePage_fn' ) ):

		function rgsShortcodePage_fn( $atts, $content = NULL, $tag )
		{
			//--------------------------------
			$defaults = RGS_Shortcodes::$gScItems[ basename( dirname( __FILE__ ) ) ][ 'fields' ];
			//--------------------------------
			$settings = array();
			foreach( $defaults as $key => $fieldArr ):
				$settings[ $key ] = ( isset( $atts[ $key ] ) ) ? $atts[ $key ] : $fieldArr['value'];
			endforeach;
			//--------------------------------
			if( $settings[ 'id' ] != 0 ) :

				$page = get_page( $settings[ 'id' ], OBJECT, 'display' );

			elseif( ! empty( $settings[ 'slug' ] ) ) :

				$page = get_page_by_path( $settings[ 'slug' ] );

			endif;
			//--------------------------------
			if( ! isset( $page->postcontent ) ) return;
			//--------------------------------
			return do_shortcode( $page->postcontent );
			//--------------------------------
		};
		//---------------------------------------------------------------------------
	endif;
	//
	add_shortcode( basename( dirname( __FILE__ ) ), 'rgsShortcodePage_fn' );
	//
endif;