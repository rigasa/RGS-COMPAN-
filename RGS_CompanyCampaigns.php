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
 * @class RGS_CompanyCampaigns
 * @fullname RiGaSa Companion
 * @package RGS_CompanyCampaigns
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_CompanyCampaigns.php
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
if( ! class_exists( 'RGS_CompanyCampaigns' ) ):
	//----------------------------------
	class RGS_CompanyCampaigns
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
		const K_SLUG = 'rgsCompanyCampaign';
		const K_PREFIX = 'rgsCompanyCampaign-';
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
			self::$gSlug 			= sanitize_title( self::K_SLUG ) . 'Campaign';
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
			//----------------------------------------------------
			// ADMIN OPTIONS 
			//
			if(is_admin()):
				// 
				#add_filter( 'add_menu_classes',  array( __CLASS__, 'menuCampaignBubble_fn' ) );
				//
				add_action( 'load-post.php',     array( __CLASS__, 'initMetabox_fn' ) );
				add_action( 'load-post-new.php', array( __CLASS__, 'initMetabox_fn' ) );

				add_action( 'admin_print_scripts-post-new.php', array( __CLASS__, 'loadDatepicker_fn'), 101 );
				add_action( 'admin_print_scripts-post.php', array( __CLASS__, 'loadDatepicker_fn'), 101 );
				// Add columns in list
				add_filter( 'manage_' . self::getCptName_fn() . '_posts_columns', array( __CLASS__, 'manageAdminColumns_fn') );
				add_action( 'manage_' . self::getCptName_fn() . '_posts_custom_column', array( __CLASS__, 'setAdminColumns_fn'), 10, 2 );
				add_filter( 'manage_edit-' . self::getCptName_fn() . '_sortable_columns', array( __CLASS__, 'sortableColumns_fn') );
				//
			else:
				//add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
			endif;
			
			//------
			// GET MESSAGE BY AJAX
			//------
			#add_action( 'wp_ajax_getCampaign', array(__CLASS__, 'getCampaignByUniqId_fn') );
			#add_action( 'wp_ajax_nopriv_getCampaign', array(__CLASS__, 'getCampaignByUniqId_fn') );
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
				'name'                  => _x( 'Campaigns', 'Post Type General Name', $TD ),
				'singular_name'         => _x( 'Campaign', 'Post Type Singular Name', $TD ),
				'menu_name'             => __( 'Campaigns', $TD ),
				'name_admin_bar'        => __( 'Campaign', $TD ),
				'archives'              => __( 'Campaign Archives', $TD ),
				'attributes'            => __( 'Campaign Attributes', $TD ),
				'parent_item_colon'     => __( 'Parent Campaign:', $TD ),
				'all_items'             => __( 'All Campaigns', $TD ),
				'add_new_item'          => __( 'Add New Campaign', $TD ),
				'add_new'               => __( 'Add Campaign', $TD ),
				'new_item'              => __( 'New Campaign', $TD ),
				'edit_item'             => __( 'Edit Campaign', $TD ),
				'update_item'           => __( 'Update Campaign', $TD ),
				'view_item'             => __( 'View Campaign', $TD ),
				'view_items'            => __( 'View Campaigns', $TD ),
				'search_items'          => __( 'Search Campaign', $TD ),
				'not_found'             => __( 'Not found', $TD ),
				'not_found_in_trash'    => __( 'Not found in Trash', $TD ),
				'featured_image'        => __( 'Featured Image', $TD ),
				'set_featured_image'    => __( 'Set featured image', $TD ),
				'remove_featured_image' => __( 'Remove featured image', $TD ),
				'use_featured_image'    => __( 'Use as featured image', $TD ),
				'insert_into_item'      => __( 'Insert into campaign', $TD ),
				'uploaded_to_this_item' => __( 'Uploaded to this campaign', $TD ),
				'items_list'            => __( 'Campaigns list', $TD ),
				'items_list_navigation' => __( 'Campaigns list navigation', $TD ),
				'filter_items_list'     => __( 'Filter campaigns list', $TD ),
			);
			$rewrite = array(
				'slug'                  => __( 'campaign', $TD ),
				'with_front'            => true,
				'pages'                 => true,
				'feeds'                 => false,
			);
			$args = array(
				'label'                 => __( 'Campaigns', $TD ),
				'description'           => __( 'Campaign Description', $TD ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'thumbnail' ),
				//'taxonomies'            => array( self::getSlug_fn() . '-type', self::getSlug_fn() . '-post_tag' ),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 17,
				'menu_icon'             => 'dashicons-calendar-alt',
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
			return RGS_Company::getSlug_fn().'campaign';
		}
		//--------------------------------------------------
		// Columns
		//--------------------------------------------------
		static function manageAdminColumns_fn( $columns )
		{
			/*
			
[
  [cb]          => <input type="checkbox" />
  [title]       => Title
  [author]      => Author
  [categories]  => Categories
  [tags]        => Tags
  [comments]    => [..] Comments [..]
  [date]        => Date
]

			*/
			/*$columns['featured_image'] = __('Image', self::getTD_fn());
			$columns['post_id'] = __('ID', self::getTD_fn());
			$columns['startDate'] = __('Start Date', self::getTD_fn());
			$columns['endDate'] = __('End Date', self::getTD_fn());*/

			$columns = array(
				'cb' => $columns['cb'],
      			'image' => __( 'Image' ),
				'title' => __( 'Title' ),
				'startDate' => __('Start Date', self::getTD_fn()),
				'endDate' => __('End Date', self::getTD_fn()),
				'post_id' => __('ID', self::getTD_fn()),
				'active' => __('Active', self::getTD_fn())
			);

			return $columns;
		}
		//--------------------------------------------------
		static function setAdminColumns_fn( $column, $post_id )
		{
			//
			switch ( $column ) :
				case 'post_id':
					echo $post_id;
					break;
				// display featured image
				case 'image':
					$thumb = get_the_post_thumbnail( $post_id, array(60, 60) );
					echo isset($thumb) ? $thumb : '';
					break;
				case 'startDate':
					$startDate = get_post_meta($post_id, 'startDate', TRUE);
					echo !empty($startDate) ? $startDate  : '';
					break;
				case 'endDate':
					$endDate = get_post_meta($post_id, 'endDate', TRUE);
					echo !empty($endDate) ? $endDate : '';
					break;
					case 'active':
						$startDate = get_post_meta($post_id, 'startDate', TRUE);
						$endDate = get_post_meta($post_id, 'endDate', TRUE);
						$inCampaign = RGS_CompanyCampaigns::inCampaign_fn($startDate, $endDate );
						$inSyle = '';
            			if($inCampaign):
                			$inSyle =  '<span class="dashicons dashicons-yes" style="color:green;"></span> ';
            			endif;
						echo !empty($inSyle) ? $inSyle : '';
						break;
			endswitch;
		}
		//--------------------------------------------------
		static function sortableColumns_fn( $columns ) {
  			$columns['post_id'] = 'ID';
  			$columns['startDate'] = 'startDate';
  			$columns['endDate'] = 'endDate';
  			return $columns;
		}
		//--------------------------------------------------
		/*static function menuCampaignBubble_fn( $menu )
		{
			$pending_count = wp_count_posts( self::getCptName_fn() )->draft;

			foreach($menu as $menu_key => $menu_data) {
				if ('post_type=' . RGS_Company::getSlug_fn() . '&page=' . self::getCampaignMenuId_fn()  != $menu_data[2])
					continue;

				$menu[$menu_key][0] .= ' <span class="update-plugins count-$pending_count"><span class="plugin-count">' . number_format_i18n($pending_count) . '</span></span>';
			}

			return $menu;
		}*/
		//---------------------------------------------------------------
		// Meta box initialization.
		//---------------------------------------------------------------
		static function campaignMetabox_fn($post)
		{
			// check user capabilities
    		if ( ! current_user_can( 'manage_options' ) ) :
        		return;
    		endif;
			
			if( is_file( RGS_Company::$gViewsDir . 'companyCampaign_MB.php' ) ):
				include_once(RGS_Company::$gViewsDir . 'companyCampaign_MB.php');
			else:
				echo '<h1>';
				esc_html_e( 'No Campaign Metabox Loaded!', self::getTD_fn() ); 
				echo '</h1>';
			endif;
			
		}
		
		//--------------------------------------------------
		static function getDatasMB_fn( $post_id )
		{
			#$refDatas = get_post_meta($post_id, self::getOptionNameMB_fn(), TRUE);
			$startDate = get_post_meta($post_id, 'startDate', TRUE);
			$endDate = get_post_meta($post_id, 'endDate', TRUE);
			//
			$refDatas = array();
			$refDatas['startDate']= !empty($startDate) ? $startDate : date('d/m/Y');
			$refDatas['endDate'] = !empty($endDate) ? $endDate : date('d/m/Y', strtotime('+2 month'));
			//
			return $refDatas;
			//
		}
		//---------------------------------------------------------------
		// Renders the meta boxes.
		//---------------------------------------------------------------
		static function getOptionNameMB_fn()
		{
			return self::K_SLUG . '_MB';
		}
		//---------------------------------------------------------------
		static function getNonceName_fn()
		{
			return self::K_SLUG . '_nonce_name';
		}
		//---------------------------------------------------------------
		static function getNonceAction_fn()
		{
			return self::K_SLUG . '_nonce_action';
		}
		//---------------------------------------------------------------
		static function addMetaboxes_fn()
		{
			add_meta_box( 
				self::getSlug_fn() .'-campaigns', 
				'<i class="wp-menu-image dashicons-before dashicons-calendar-alt"></i> ' . __('Dates', self::getTD_fn()),  
				array(__CLASS__, 'campaignMetabox_fn'), 
				self::getCptName_fn(), 
				'normal', 
				'low'
			);
		}
		/**
		 * Handles saving the meta box.
		 *
		 * @param int     $post_id Post ID.
		 * @param WP_Post $post    Post object.
		 * @return null
		 */
		//---------------------------------------------------------------
		static function saveMetaboxes_fn($post_id, $post)
		{
			// Add nonce for security and authentication.
        	$nonce_name   = isset( $_POST[self::getNonceName_fn()] ) ? $_POST[self::getNonceName_fn()] : '';
			$nonce_action = self::getNonceAction_fn(); // 'custom_nonce_action';
			
			// Check if nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) :
				return;
			endif;
			
			// Check if user has permissions to save data.
			if ( ! current_user_can( 'edit_post', $post_id ) ) :
				return;
			endif;

			// Check if not an autosave.
			if ( wp_is_post_autosave( $post_id ) ) :
				return;
			endif;

			// Check if not a revision.
			if ( wp_is_post_revision( $post_id ) ) :
				return;
			endif;
			//
			$OPTION = self::getOptionNameMB_fn();
			if( isset( $_POST[$OPTION] ) ):
				#update_post_meta($post_id, self::getOptionNameMB_fn(), $_POST[$OPTION]);
				foreach( $_POST[$OPTION] as $key => $val ):
					update_post_meta($post_id, $key, $val );
				endforeach;
			endif;
			//
			return $post_id;
			//
		}
		//---------------------------------------------------------------
		public function initMetabox_fn() 
		{
			add_action( 'add_meta_boxes', array( __CLASS__, 'addMetaboxes_fn' ) );
			add_action( 'save_post',      array( __CLASS__, 'saveMetaboxes_fn' ), 10, 2 );
		}
		//--------------------------------------------------
		// MANAGE POST
		//--------------------------------------------------
		static function insertCampaign_fn( $formFields )
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
				//'post_status' => 'draft',
				'post_content' => $postContent,
				'meta_input' => $formFields
			);
			
			#echo '<pre>ADD POST::: '.print_r( $new_post, TRUE ).'</pre><br>';
			
			$post_id = wp_insert_post( $new_post );
			
		}
		//---------------------------------------------------------------
		static function loadDatepicker_fn()
		{
			global $post_type;
			if( self::getCptName_fn() == $post_type ) :
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
				//
				if( file_exists( RGS_Company::$gJsDir . 'adminCampaigns.js' ) ):
					wp_enqueue_script( 
						self::getSlug_fn() . '_adminadminCampaigns', 
						RGS_Company::$gJsUrl . 'adminCampaigns.js', 
						array( 'jquery-ui-datepicker', RGS_Company::getEnqueueName_fn() ), 
						'0.0.1', 
						true 
					);
				endif;
				//
			endif;
		}
		//---------------------------------------------------------------
		static function getTableInCampaign_fn( $content )
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
		static function getDisplayCampaign_fn( $post )
		{
			
			if(! $post ):
				return null;
			endif;
			//
			$arrDisplay = array();
			// MANAGE TABLE RESPONSES
			$table =  self::getTableInCampaign_fn( $post->post_content );
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
		static function getActivesCampaigns_fn($campaigns = array() )
		{
			$return = false;
			$arrLines = array();
			$arrIdx = 0;
			foreach( $campaigns as $campaignID ) :

				$arrLines[$arrIdx] = array();
				$mStartDate = get_post_meta( $campaignID, 'startDate', TRUE);
				$mEndDate = get_post_meta( $campaignID, 'endDate', TRUE);

				if( ! empty($mStartDate) and ! empty($mEndDate)  ):
					$inCampaign = RGS_CompanyCampaigns::inCampaign_fn($mStartDate, $mEndDate );
					
					if($inCampaign):
						$arrLines[$arrIdx]['id'] = $campaignID;
						$arrLines[$arrIdx]['title'] = get_the_title( $campaignID );
						$arrLines[$arrIdx]['thumb'] = get_the_post_thumbnail( $campaignID, array(60, 60) );
						$arrLines[$arrIdx]['start'] = $mStartDate;
						$arrLines[$arrIdx]['end'] = $mEndDate;
						$arrLines[$arrIdx]['icon'] = '<span class="dashicons dashicons-yes" style="color:green;"></span> ';

						$arrIdx++;

					endif;
				endif;

			endforeach;
			
			return $arrLines;
		}
		//---------------------------------------------------------------
		static function getActivesCampaign_fn($campaigns = array() )
		{
			$return = false;
			$arrLines = array();
			$arrIdx = 0;
			foreach( $campaigns as $campaignID ) :

				$arrLines[$arrIdx] = array();
				$mStartDate = get_post_meta( $campaignID, 'startDate', TRUE);
				$mEndDate = get_post_meta( $campaignID, 'endDate', TRUE);

				if( ! empty($mStartDate) and ! empty($mEndDate)  ):
					$inCampaign = RGS_CompanyCampaigns::inCampaign_fn($mStartDate, $mEndDate );
					
					if($inCampaign):
						$arrLines[$arrIdx]['id'] = $campaignID;
						$arrLines[$arrIdx]['title'] = get_the_title( $campaignID );
						$arrLines[$arrIdx]['thumb'] = get_the_post_thumbnail( $campaignID, array(60, 60) );
						$arrLines[$arrIdx]['start'] = $mStartDate;
						$arrLines[$arrIdx]['end'] = $mEndDate;
						$arrLines[$arrIdx]['icon'] = '<span class="dashicons dashicons-yes" style="color:green;"></span> ';

						return $arrLines;
						
						$arrIdx++;

					endif;
				endif;

			endforeach;
			
			return $arrLines;
		}
		//--------------------------------------------------
		// DELETE INQUEST
		//--------------------------------------------------
		static function deleteCampaign_fn( $id, $notInTrash = true )
		{
			wp_delete_post( $id, $notInTrash);
		}
		//--------------------------------------------------
		static function deleteAllCampaigns_fn()
		{
			$args = array(
				'posts_per_page' 	=> -1,
				'post_type'   	=> RGS_CompanyCampaigns::getCptName_fn(),
				'post_status' 	=> 'draft'
			);
			$post_list = get_posts( $args );
	
			foreach ( $post_list as $post ) :
				self::deleteCampaign_fn( $post->ID, true);
			endforeach;
		}
		//--------------------------------------------------
		static function deleteCampaignsByPost_fn($postID, $metaKEY = 'COMPANY_ID' )
		{
			$args = array(
				'post_type' 		=> RGS_CompanyCampaigns::getCptName_fn(), 
				'posts_per_page' 	=> -1,
				'post_status' 		=> 'draft',
				'meta_key' 			=> $metaKEY,
				'meta_value' 		=> $postID
			);
			//
			$theList = get_posts( $args );
			//
			foreach ( $theList as $post ) :
				self::deleteCampaign_fn( $post->ID, true);
			endforeach;
		}
		//--------------------------------------------------
		static function convertDate_fn( $date )
		{
			#$date = date('d/m/Y', strtotime( $date) );
			//
			$djour = explode("/", $date);
			return $djour[2].$djour[1].$djour[0];
		}
		//--------------------------------------------------
		static function inCampaign_fn($startDate, $endDate )
		{
			//
			#$startDate = date('d/m/Y', strtotime( $startDate) );
			#$endDate = date('d/m/Y', strtotime( $endDate) );
			//
			$today = self::convertDate_fn( date('d/m/Y') );
			$start = self::convertDate_fn( $startDate );
			$end = self::convertDate_fn( $endDate );

			if( $start <= $today and $end >= $today ):
				return true;
			endif;
			//
			return false;
		}
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		
	};
	//------------------------------------------------------
	if( ! function_exists( 'rgsCompanyCampaigns_fn' ) ):
		function rgsCompanyCampaigns_fn() 
		{
			return RGS_CompanyCampaigns::getInstance_fn();
		};
	endif;
	//------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_CompanyCampaigns' ] ) ):
		$GLOBALS[ 'RGS_CompanyCampaigns' ] = rgsCompanyCampaigns_fn();
	endif;
	//------------------------------------------------------
endif;
//----------------------------------------------------------
