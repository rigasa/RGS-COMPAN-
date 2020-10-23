<?php
/*
 ____   _   ____         ____          
|  _ \ (_) / ___|  __ _ / ___|   __ _  
| |_) || || |  _  / _` |\___ \  / _` | 
|  _ < | || |_| || (_| | ___) || (_| | 
|_| \_\|_| \____| \__,_||____/  \__,_|                                          
*/
//-----------------------------------------------------------------------
/**
 * @class EC_CompanyMBoxes
 * @fullname Eco Citoyen Management
 * @package EC_CompanyMBoxes
 * @category Core
 * @filesource assets/plugins/Entreprise/EC_CompanyMBoxes.php
 * @version 0.0.1
 * @created 2020-10-02
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-02
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
 * pChart http://pchart.sourceforge.net/screenshots.php
*/                                                                                 

//--------------------------------------
if ( ! defined( 'ABSPATH' ) ) exit; // SECURITY : Exit if accessed directly
//--------------------------------------
if( ! class_exists( 'EC_CompanyMBoxes' ) ):
	//----------------------------------
	class EC_CompanyMBoxes
	{
		//------------------------------
		//---------------------------------------------------------------
		private static $instance; // THE only instance of the class
		//---------------------------------------------------------------
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
		const K_SLUG = 'ecCompanyMBoxCampaign';
		const K_PREFIX = 'ecCompanyMBoxCampaign-';
		const K_VERS = '1.0.0';
		
		//------------------------------
		public function __construct()
		{
			self::setupGlobals_fn();
			self::loadDependencies_fn();
			self::setupHooks_fn();
		}
		//---------------------------------------------------------------
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
		//---------------------------------------------------------------
		// Constructor Methods
		//---------------------------------------------------------------
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
			self::$gSlug 			= sanitize_title( self::K_SLUG );
			//---
		}
		//---------------------------------------------------------------
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
			//
		}
		//---------------------------------------------------------------
		private function setupHooks_fn() 
		{
			// SETUPS
			if ( is_admin() ) :
            	add_action( 'load-post.php',     array( __CLASS__, 'initMetabox_fn' ) );
            	add_action( 'load-post-new.php', array( __CLASS__, 'initMetabox_fn' ) );
				
				// Add columns in list
				//add_filter('manage_' . self::getRefMetabox_fn() .'_columns', array( __CLASS__, 'manageAdminColumns_fn'), 99, 1 );
				//add_action( 'manage_' . self::getRefMetabox_fn() .'_custom_column', array( __CLASS__, 'setAdminColumns_fn'), 99, 2 );
				
				$TAXO = EC_Company::getTaxoCampaignName_fn();
				// MODIFY COLUMNS (add our meta to the list)
				add_filter( 
					'manage_edit-' . $TAXO . '_columns', 
					array( __CLASS__, 'addRefTermColumns_fn' ) 
				);

				// RENDER COLUMNS (render the meta data on a column)
				add_filter( 
					'manage_' . $TAXO . '_custom_column', 
					array( __CLASS__, 'manageRefTermColumns_fn' ), 
					10, 
					3 
				);
			
				// Make the column sortable
				add_filter( 
					'manage_edit-' . $TAXO . '_sortable_columns', 
					array( __CLASS__, 'sortRefTermColumns_fn' )
				);


        	endif;
			//----------------------------------------------------
		}
		//---------------------------------------------------------------
		// CPT
		//---------------------------------------------------------------
		static function getSlug_fn()
		{
			return EC_Company::getSlug_fn();
		}
		//---------------------------------------------------------------
		static function getTD_fn()
		{
			return EC_Company::getTD_fn();
		}
		//---------------------------------------------------------------
		static function getNonceName_fn()
		{
			return self::getSlug_fn() . '_nonce_name';
		}
		//---------------------------------------------------------------
		static function getNonceAction_fn()
		{
			return self::getSlug_fn() . '_nonce_action';
		}
		//---------------------------------------------------------------
		// Meta box initialization.
		//---------------------------------------------------------------
		public function initMetabox_fn() 
		{
			add_action( 'add_meta_boxes', array( __CLASS__, 'addMetaboxes_fn' ) );
			add_action( 'save_post',      array( __CLASS__, 'saveMetaboxes_fn' ), 10, 2 );
		}
		//---------------------------------------------------------------
		// Add Meta box.
		static function getRefMetabox_fn()
		{
			return self::getSlug_fn() .'-ref';
		}
		//---------------------------------------------------------------
		static function addMetaboxes_fn()
		{
			add_meta_box( 
				self::getRefMetabox_fn(), 
				__('References', self::getTD_fn()),  
				array(__CLASS__, 'refMetabox_fn'), 
				self::getSlug_fn(), 
				'normal', 
				'low'
			);
			
			/*add_meta_box( 
				self::getSlug_fn() .'-campaign', 
				__('Campaign', self::getTD_fn()),  
				array(__CLASS__, 'campaignMetabox_fn'), 
				self::getSlug_fn(), 
				'normal', 
				'low'
			);*/
		}
		//---------------------------------------------------------------
		// Renders the meta boxes.
		//---------------------------------------------------------------
		static function getOptionNameMB_fn()
		{
			return self::getSlug_fn() . '_MB';
		}
		//---------------------------------------------------------------
		static function refMetabox_fn($post)
		{
			// check user capabilities
    		if ( ! current_user_can( 'manage_options' ) ) :
        		return;
    		endif;
			
			if( is_file( EC_Company::$gViewsDir . 'mbRef.php' ) ):
				include_once(EC_Company::$gViewsDir . 'mbRef.php');
			else:
				echo '<h1>';
				esc_html_e( 'No Reference Metabox Page Loaded!', self::getTD_fn() ); 
				echo '</h1>';
			endif;
		}
		//---------------------------------------------------------------
		static function campaignMetabox_fn($post)
		{
			// check user capabilities
    		if ( ! current_user_can( 'manage_options' ) ) :
        		return;
    		endif;
			
			if( is_file( EC_Company::$gViewsDir . 'mbCampaign.php' ) ):
				include_once(EC_Company::$gViewsDir . 'mbCampaign.php');
			else:
				echo '<h1>';
				esc_html_e( 'No Campaign Metabox Page Loaded!', self::getTD_fn() ); 
				echo '</h1>';
			endif;
			
		}
		//---------------------------------------------------------------
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
			
			
			
			if( isset( $_POST[ self::getOptionNameMB_fn()] ) ):
				update_post_meta($post_id, self::getOptionNameMB_fn(), $_POST[self::getOptionNameMB_fn()]);
			endif;
			//
			return $post_id;
			//
		}
		//--------------------------------------------------
		// Columns
		//--------------------------------------------------
		static function addRefTermColumns_fn( $columns )
		{
			$columns = array();
			$columns['id'] = __( 'ID', self::getTD_fn() );
			$columns['slug'] = __( 'Slug', self::getTD_fn() );
			$columns['name'] = __( 'Name', self::getTD_fn() );
			$columns['startDate'] = __( 'Start', self::getTD_fn() );
			$columns['endDate'] = __( 'End', self::getTD_fn() );
			$columns['description'] = __( 'Description', self::getTD_fn() );
			$columns['cLogo'] = __( 'Logo', self::getTD_fn() );
			$columns['posts'] = __( 'Count', self::getTD_fn() );
			
			return $columns;
		}
		//--------------------------------------------------
		static function manageRefTermColumns_fn( $content, $columnName, $termID )
		{
			$term = get_term($termID, EC_Company::getTaxoCampaignName_fn() );
			/*
			["term_id"]=>  //int
			["name"]=>   //string 
			["slug"]=>  //string 
			["term_group"]=>  //int
			["term_taxonomy_id"]=> //int
			["taxonomy"]=>   //string
			["description"]=>    //string
			["parent"]=> //int
			["count"]=>  // int
			["filter"]= //string
			["meta"]= array(0) {} //an array of meta fields.
			
			$columns['id'] = __( 'ID', self::getTD_fn() );
			$columns['name'] = __( 'Name', self::getTD_fn() );
			$columns['slug'] = __( 'Slug', self::getTD_fn() );
			$columns['startDate'] = __( 'Start', self::getTD_fn() );
			$columns['endDate'] = __( 'End', self::getTD_fn() );
			$columns['description'] = __( 'Description', self::getTD_fn() );
			$columns['cLogo'] = __( 'Logo', self::getTD_fn() );
			$columns['total'] = __( 'Total', self::getTD_fn() );
			
			*/
			switch ( $columnName ) :
				//
				case 'id':
					$content = $term->term_id;
					break;
				case 'name':
					$content = $term->name;
					break;
				case 'slug':
					$content = $term->slug;
					break;
				case 'description':
					$content = $term->description;
					break;
				case 'posts':
					$content = $term->count;
					break;
				case 'cLogo':
					$cLogo = get_term_meta( $termID, 'cLogo', true );
					$image = wp_get_attachment_image_src ( $cLogo, 'thumbnail', FALSE );
					$imgSrc = '';
					//
					if( is_array( $image ) and isset( $image[0] ) ):
						$imgSrc = $image[0] ;
						$content = '<img width="auto" height="66" src="' . $imgSrc . '" class="cLogoImage" alt="" style="height: 66px; width: auto; margin:0px;">';
					endif;
					break;
				case 'startDate':
					$content = get_term_meta( $termID, 'startDate', true );
					break;
				case 'endDate':
					$content = get_term_meta( $termID, 'endDate', true );
					break;
			endswitch;
			
			return $content;
		}
		//---------------------------------------------------------------
		static function sortRefTermColumns_fn( $columns )
		{
			//$sortable = array();
			$columns['id'] = 'id';
			$columns['slug'] = 'slug';
			$columns['name'] = 'name';
			$columns['startDate'] = 'startDate';
			$columns['endDate'] = 'endDate';
			$columns['description'] = 'description';
			#$sortable['cLogo'] = 'cLogo';
			$columns['posts'] = 'posts';
			
			return $columns;
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'ecCompanyMBoxes_fn' ) ):
		function ecCompanyMBoxes_fn() 
		{
			return EC_CompanyMBoxes::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'EC_CompanyMBoxes' ] ) ):
		$GLOBALS[ 'EC_CompanyMBoxes' ] = ecCompanyMBoxes_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------