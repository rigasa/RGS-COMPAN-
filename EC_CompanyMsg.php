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
 * @class EC_CompanyMsg
 * @fullname Eco Citoyen Management
 * @package EC_CompanyMsg
 * @category Core
 * @filesource assets/plugins/Entreprise/EC_CompanyMsg.php
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
if( ! class_exists( 'EC_CompanyMsg' ) ):
	//----------------------------------
	class EC_CompanyMsg
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
		const K_SLUG = 'ecCompanyMsg';
		const K_PREFIX = 'ecCompanyMsg-';
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
			self::$gSlug 			= sanitize_title( self::K_SLUG ) . 'Msg';
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
				add_filter( 'add_menu_classes',  array( __CLASS__, 'menuMsgBubble_fn' ) );
				//
			else:
				//add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
			endif;
			
			//------
			// GET MESSAGE BY AJAX
			//------
			add_action( 'wp_ajax_getMessage', array(__CLASS__, 'getMessageByUniqId_fn') );
			add_action( 'wp_ajax_nopriv_getMessage', array(__CLASS__, 'getMessageByUniqId_fn') );
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
				'name'                  => _x( 'Messages', 'Post Type General Name', $TD ),
				'singular_name'         => _x( 'Message', 'Post Type Singular Name', $TD ),
				'menu_name'             => __( 'Messages', $TD ),
				'name_admin_bar'        => __( 'Message', $TD ),
				'archives'              => __( 'Message Archives', $TD ),
				'attributes'            => __( 'Message Attributes', $TD ),
				'parent_item_colon'     => __( 'Parent Message:', $TD ),
				'all_items'             => __( 'All Messages', $TD ),
				'add_new_item'          => __( 'Add New Message', $TD ),
				'add_new'               => __( 'Add Message', $TD ),
				'new_item'              => __( 'New Message', $TD ),
				'edit_item'             => __( 'Edit Message', $TD ),
				'update_item'           => __( 'Update Message', $TD ),
				'view_item'             => __( 'View Message', $TD ),
				'view_items'            => __( 'View Messages', $TD ),
				'search_items'          => __( 'Search Message', $TD ),
				'not_found'             => __( 'Not found', $TD ),
				'not_found_in_trash'    => __( 'Not found in Trash', $TD ),
				'featured_image'        => __( 'Featured Image', $TD ),
				'set_featured_image'    => __( 'Set featured image', $TD ),
				'remove_featured_image' => __( 'Remove featured image', $TD ),
				'use_featured_image'    => __( 'Use as featured image', $TD ),
				'insert_into_item'      => __( 'Insert into message', $TD ),
				'uploaded_to_this_item' => __( 'Uploaded to this message', $TD ),
				'items_list'            => __( 'Messages list', $TD ),
				'items_list_navigation' => __( 'Messages list navigation', $TD ),
				'filter_items_list'     => __( 'Filter message list', $TD ),
			);
			$rewrite = array(
				'slug'                  => __( 'Message', $TD ),
				'with_front'            => true,
				'pages'                 => true,
				'feeds'                 => false,
			);
			$args = array(
				'label'                 => __( 'Messages', $TD ),
				'description'           => __( 'Message Description', $TD ),
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
			return EC_Company::getSlug_fn();
		}
		//--------------------------------------------------
		static function getTD_fn()
		{
			return EC_Company::getTD_fn();
		}
		//--------------------------------------------------
		static function getCptName_fn()
		{
			return EC_Company::getSlug_fn().'Msg';
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
		static function menuMsgBubble_fn( $menu )
		{
			$pending_count = wp_count_posts( self::getCptName_fn() )->draft;

			foreach($menu as $menu_key => $menu_data) {
				if ('post_type=' . EC_Company::getSlug_fn() . '&page=' . EC_Company::getMsgMenuId_fn()  != $menu_data[2])
					continue;

				$menu[$menu_key][0] .= ' <span class="update-plugins count-$pending_count"><span class="plugin-count">' . number_format_i18n($pending_count) . '</span></span>';
			}

			return $menu;
		}
		//--------------------------------------------------
		// MANAGE POST
		//--------------------------------------------------
		static function insertMsg_fn( $formFields )
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
				if( $formFields['POST_TYPE'] == EC_Company::getSlug_fn() ):
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
			//die('insertMsg_fn');
			
		}
		//---------------------------------------------------------------
		static function getTableInMsg_fn( $content )
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
		static function getDisplayMsg_fn( $post )
		{
			
			if(! $post ):
				return null;
			endif;
			//
			$arrDisplay = array();
			// MANAGE TABLE RESPONSES
			$table =  self::getTableInMsg_fn( $post->post_content );
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
		static function getMessageByUniqId_fn()
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
						$arrDisplay = self::getDisplayMsg_fn( $post );
						//
					endwhile;
				endif;
				wp_reset_postdata();
				//
				if( ! $arrDisplay ):
					wp_send_json_error( __('No message was retrieved', self::getTD_fn() ) );
				else:
					wp_send_json_success($arrDisplay);
				endif;
			endif;
			
			wp_die();
		}
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		
	};
	//------------------------------------------------------
	if( ! function_exists( 'ecCompanyMsg_fn' ) ):
		function ecCompanyMsg_fn() 
		{
			return EC_CompanyMsg::getInstance_fn();
		};
	endif;
	//------------------------------------------------------
	if( ! isset( $GLOBALS[ 'EC_CompanyMsg' ] ) ):
		$GLOBALS[ 'EC_CompanyMsg' ] = ecCompanyMsg_fn();
	endif;
	//------------------------------------------------------
endif;
//----------------------------------------------------------
