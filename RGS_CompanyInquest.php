<?php
/*
 ____   _   ____         ____          
|  _ \ (_) / ___|  __ _ / ___|   __ _  
| |_) || || |  _  / _` |\___ \  / _` | 
|  _ < | || |_| || (_| | ___) || (_| | 
|_| \_\|_| \____| \__,_||____/  \__,_|                                          
*/
//----------------------------------------------------------
/**
 * @class RGS_CompanyInquest
 * @fullname Eco Citoyen Management
 * @package RGS_CompanyInquest
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_CompanyInquest.php
 * @version 0.0.1
 * @created 2020-10-15
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-015
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
 * pChart http://pchart.sourceforge.net/screenshots.php
*/                                                                                    
//--------------------------------------
if ( ! defined( 'ABSPATH' ) ) exit; // SECURITY : Exit if accessed directly
//--------------------------------------
if( ! class_exists( 'RGS_CompanyInquest' ) ):
	//----------------------------------
	class RGS_CompanyInquest
	{
		//------------------------------
		//--------------------------------------------------
		private static $instance; // THE only instance of the class
		//--------------------------------------------------
		/**
		 * Slug
		 *
		 * @var string
		 */
		static public $gSlug;
		//---
		static public $gFile;
		static public $gDir;
		static public $gUrl;
		//---
		static public $gBasename;
		// Plugin Hierarchy
		//---
		const K_SLUG = 'rgsCompanyInquest';
		const K_PREFIX = 'rgsCompanyInquest-';
		const K_VERS = '1.0.0';
		
		//------------------------------
		public function __construct( )
		{
			self::setupGlobals_fn();
			self::loadDependencies_fn();
			self::setupHooks_fn();
		}
		//--------------------------------------------------
		/**
		 * Return an instance of this class.
		 *
		 * @access public
		 *
		 * @return object A single instance of this class.
		 * @static
		 */
		public static function getInstance_fn() 
		{
			// If the single instance hasn't been set, set it now.
			if ( NULL == self::$instance ) :
				self::$instance = new self;
			endif;

			return self::$instance;
		}
		//--------------------------------------------------
		// Constructor Methods
		//--------------------------------------------------
		/**
		 * Sets some globals for the class
		 *
		 * @access private
		 */
		private function setupGlobals_fn() 
		{
			//---
			$this->_version        	= self::K_VERS;
			self::$gFile          	= __FILE__;
			self::$gDir     		= trailingslashit( dirname( self::$gFile ) );
	
			self::$gUrl				= trailingslashit( get_site_url() ) . str_replace( ABSPATH, '', self::$gDir );
			//---
			$lName 					= basename( self::$gDir );
			$gBasename 				= explode( $lName, self::$gFile );
			self::$gBasename       	= $lName . $gBasename[ 1 ];
			//
			// Directories Hierarchy
			//
			//---
			self::$gSlug 			= sanitize_title( self::K_SLUG ) . 'Inquest';
			//---
		}
		//--------------------------------------------------
		/**
		 * Load the required dependencies for this theme.
		 *
		 * Include the following files that make up the theme:
		 *
		 * @since    0.1.0
		 * @access   private
		 */
		private function loadDependencies_fn() 
		{
			//------------------------------------------------------
			//------------------------------------------------------
		}
		//--------------------------------------------------
		private function setupHooks_fn() 
		{
			//
			// SETUPS
			add_action( 'after_setup_theme', array( __CLASS__, 'overrideParent_fn' ), 10 );
			add_action( 'after_setup_theme', array( __CLASS__, 'removeFeatures_fn' ), 11 );
			add_action( 'after_setup_theme', array( __CLASS__, 'setupTheme_fn' ), 12 );
			//
			//----------------------------------------------------
			// CREATE CPT
			add_action( 'init', array( __CLASS__, 'registerCPT_fn' ), 1 );
			//
			//----------------------------------------------------
			// ADMIN OPTIONS 
			//
			if(is_admin()):
				// Add columns in list
				add_filter('manage_' . self::getCptName_fn() . '_columns', array( __CLASS__, 'manageAdminColumns_fn') );
				add_action( 'manage_' . self::getCptName_fn() . '_custom_column', array( __CLASS__, 'setAdminColumns_fn'), 10, 2 );
				//
				add_filter( 'add_menu_classes',  array( __CLASS__, 'menuInquestBubble_fn' ) );
				//
			else:
				//add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
			endif;
			
			//------
			// GET MESSAGE BY AJAX
			//------
			add_action( 'wp_ajax_getInquest', array(__CLASS__, 'getInquestByUniqId_fn') );
			add_action( 'wp_ajax_nopriv_getInquest', array(__CLASS__, 'getInquestByUniqId_fn') );
			//
	}
		//------------------------------
	//|-----------------
	//| Hooks Methods
	//|------------------
		//--------------------------------------------------
		static function registerCPT_fn()
		{
			$TD = self::getTD_fn();
			
			$labels = array(
				'name'                  => _x( 'Inquests', 'Post Type General Name', $TD ),
				'singular_name'         => _x( 'Inquest', 'Post Type Singular Name', $TD ),
				'menu_name'             => __( 'Inquests', $TD ),
				'name_admin_bar'        => __( 'Inquest', $TD ),
				'archives'              => __( 'Inquest Archives', $TD ),
				'attributes'            => __( 'Inquest Attributes', $TD ),
				'parent_item_colon'     => __( 'Parent Inquest:', $TD ),
				'all_items'             => __( 'All Inquests', $TD ),
				'add_new_item'          => __( 'Add New Inquest', $TD ),
				'add_new'               => __( 'Add Inquest', $TD ),
				'new_item'              => __( 'New Inquest', $TD ),
				'edit_item'             => __( 'Edit Inquest', $TD ),
				'update_item'           => __( 'Update Inquest', $TD ),
				'view_item'             => __( 'View Inquest', $TD ),
				'view_items'            => __( 'View Inquests', $TD ),
				'search_items'          => __( 'Search Inquest', $TD ),
				'not_found'             => __( 'Not found', $TD ),
				'not_found_in_trash'    => __( 'Not found in Trash', $TD ),
				'featured_image'        => __( 'Featured Image', $TD ),
				'set_featured_image'    => __( 'Set featured image', $TD ),
				'remove_featured_image' => __( 'Remove featured image', $TD ),
				'use_featured_image'    => __( 'Use as featured image', $TD ),
				'insert_into_item'      => __( 'Insert into inquest', $TD ),
				'uploaded_to_this_item' => __( 'Uploaded to this inquest', $TD ),
				'items_list'            => __( 'Inquests list', $TD ),
				'items_list_navigation' => __( 'Inquests list navigation', $TD ),
				'filter_items_list'     => __( 'Filter inquest list', $TD ),
			);
			$rewrite = array(
				'slug'                  => __( 'Inquest', $TD ),
				'with_front'            => true,
				'pages'                 => true,
				'feeds'                 => false,
			);
			$args = array(
				'label'                 => __( 'Inquests', $TD ),
				'description'           => __( 'Inquest Description', $TD ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				//'taxonomies'            => array( self::getSlug_fn() . '-type', self::getSlug_fn() . '-post_tag' ),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => false,
				'show_in_menu'          => false,
				'menu_position'         => 17,
				'menu_icon'             => 'dashicons-email-alt',
				//'show_in_nav_menus'     => true,
				'capability_type'       => 'page',
				//'show_in_admin_bar'     => true,
				//'can_export'            => false,
				'has_archive'           => true,
				//'exclude_from_search'   => true,
				//'publicly_queryable'    => true,
				'query_var'             => true,
				'rewrite'               => $rewrite,
				//'show_in_rest'          => false,
			);
			register_post_type( self::getCptName_fn(), $args );
			//
			
		}
		//--------------------------------------------------
		static function overrideParent_fn()
		{
		
		}
		//--------------------------------------------------
		static function removeFeatures_fn()
		{
			
		}
		//--------------------------------------------------
		static function setupTheme_fn()
		{
			
		}
		//--------------------------------------------------
		// CPT
		//--------------------------------------------------
		static function getSlug_fn()
		{
			return RGS_Company::getSlug_fn();
		}
		//--------------------------------------------------
		static function getTD_fn()
		{
			return RGS_Company::getTD_fn();
		}
		//--------------------------------------------------
		static function getCptName_fn()
		{
			return RGS_Company::getSlug_fn().'Inquest';
		}
		//--------------------------------------------------
		// Columns
		//--------------------------------------------------
		static function manageAdminColumns_fn( $columns )
		{
			
			$columns['featured_image'] = __('Image', self::getTD_fn());
			$columns['post_id'] = __('ID', self::getTD_fn());
			return $columns;
		}
		//--------------------------------------------------
		static function setAdminColumns_fn( $column, $post_id )
		{
			switch ( $column ) :
				// display featured image
				case 'featured_image':
					echo the_post_thumbnail( 'thumbnail' );
					break;
				case 'post_id':
					echo $post_id;
					break;
			endswitch;
		}
		//--------------------------------------------------
		static function menuInquestBubble_fn( $menu )
		{
			$pending_count = wp_count_posts( self::getCptName_fn() )->draft;

			foreach($menu as $menu_key => $menu_data) {
				if ('post_type=' . RGS_Company::getSlug_fn() . '&page=' . RGS_Company::getInquestMenuId_fn()  != $menu_data[2])
					continue;

				$menu[$menu_key][0] .= ' <span class="update-plugins count-$pending_count"><span class="plugin-count">' . number_format_i18n($pending_count) . '</span></span>';
			}

			return $menu;
		}
		//--------------------------------------------------
		// MANAGE POST
		//--------------------------------------------------
		static function insertInquest_fn( $formFields )
		{
			
			$postTitle = '';
			if(isset( $formFields['POST_TITLE'] ) ) :
				$postTitle = sanitize_text_field( $formFields['POST_TITLE'] );
				unset( $dbFields['POST_TITLE'] );
			endif;
			//
			$metaType = '';
			if(isset( $formFields['POST_TYPE'] ) ) :
				$postTitle = $formFields['POST_TYPE'];
				$ID = $formFields['POST_ID'];
				if( $formFields['POST_TYPE'] == RGS_Company::getSlug_fn() ):
					unset( $formFields['POST_ID'] );
					$formFields['COMPANY_ID'] = $ID;
				endif;
				unset( $formFields['POST_TYPE'] );
			endif;
			//
			$postContent = '';
			if(isset( $formFields['mail']['body'] ) ) :
				$postContent = $formFields['mail']['body'];
				unset( $formFields['mail'] );
			endif;
			//
			$new_post = array(
				'post_type' => self::getCptName_fn(),
				'post_title' => $postTitle,
				'post_status' => 'draft',
				'post_content' => $postContent,
				'meta_input' => $formFields
			);
			
			#echo '<pre>ADD POST::: '.print_r( $new_post, TRUE ).'</pre><br>';
			
			$post_id = wp_insert_post( $new_post );
			// insert as post meta
        	#add_post_meta( $post_id, 'some_meta', $formFields['some_meta']);
			
			//echo '<pre>FIELDS AFTER::: '.print_r( $formFields, TRUE ).'</pre><br>';
			//die('insertInquest_fn');
			
		}
		//---------------------------------------------------------------
		static function getTableInInquest_fn( $content )
		{
			//Début et fermeture du tag
			$posd = '<table width="100%" border="2" cellspacing="0" cellpadding="6">';
			$pose = '</table>';
			$pos1 = strpos($content, $posd);
			$pos1 = strpos($content, '>', $pos1);
			$pos2 = strpos($content, $pose);
			//
			$table =  $posd . substr ( $content , $pos1+strlen($posd) , $pos2-$pos1-strlen($posd) ) .$pose;
			return $table;
		}
		//---------------------------------------------------------------
		static function getDisplayInquest_fn( $post )
		{
			
			if(! $post ):
				return null;
			endif;
			//
			$arrDisplay = array();
			// MANAGE TABLE RESPONSES
			$table =  self::getTableInInquest_fn( $post->post_content );
			//
			$pMetasResponses = get_post_meta($post->ID, 'responses', TRUE );
			foreach( $pMetasResponses as $id => $response ) :
				$number = $id+1;
				$table =  str_replace("[radio-$number]", $response, $table);
			endforeach;

			$arrDisplay['table'] = $table;
			$arrDisplay['points'] = get_post_meta($post->ID, 'points', TRUE );
			$arrDisplay['graphSeries'] = get_post_meta($post->ID, 'graphSeries', TRUE );
			
			return $arrDisplay;
		}
		//---------------------------------------------------------------
		// AJAX
		//---------------------------------------------------------------
		static function getInquestByUniqId_fn()
		{
			$uinqid = (isset( $_POST['uniqid'] ) ) ? $_POST['uniqid'] : '';
			//
			if( empty( $uinqid ) ) :
				wp_send_json_error( __('Uniq id is mandatory', self::getTD_fn() ) );
			else:
				$args = array(
					'post_type' 	=> self::getCptName_fn(),
					'post_status' 	=> 'draft',
					'meta_key' 		=> 'uniqId',
					'meta_value' 	=> $uinqid
				);
				$query = new WP_Query($args);
				if( $query->have_posts() ) : 
					while( $query->have_posts() ) : 
						$query->the_post();
						global $post;
						//
						$arrDisplay = self::getDisplayInquest_fn( $post );
						//
					endwhile;
				endif;
				wp_reset_postdata();
				//
				if( ! $arrDisplay ):
					wp_send_json_error( __('No inquest was retrieved', self::getTD_fn() ) );
				else:
					wp_send_json_success($arrDisplay);
				endif;
			endif;
			
			wp_die();
		}
		//--------------------------------------------------
		static function deleteInquest_fn( $id, $notInTrash = true )
		{
			wp_delete_post( $id, $notInTrash);
		}
		//--------------------------------------------------
		static function deleteAllInquests_fn()
		{
			$args = array(
				'posts_per_page' 	=> -1,
				'post_type'   	=> RGS_CompanyInquest::getCptName_fn(),
				'post_status' 	=> 'draft'
			);
			$post_list = get_posts( $args );
	
			foreach ( $post_list as $post ) :
				self::deleteInquest_fn( $post->ID, true);
			endforeach;
		}
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		
	};
	//------------------------------------------------------
	if( ! function_exists( 'rgsCompanyInquest_fn' ) ):
		function rgsCompanyInquest_fn() 
		{
			return RGS_CompanyInquest::getInstance_fn();
		};
	endif;
	//------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_CompanyInquest' ] ) ):
		$GLOBALS[ 'RGS_CompanyInquest' ] = rgsCompanyInquest_fn();
	endif;
	//------------------------------------------------------
endif;
//----------------------------------------------------------
