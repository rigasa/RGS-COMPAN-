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
 * @class RGS_CompanyTemplates
 * @fullname Eco Citoyen Management
 * @package RGS_Company
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_CompanyTemplates.php
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
if( ! class_exists( 'RGS_CompanyTemplates' ) ):
	//-------------------------------------------------------------------------------
	class RGS_CompanyTemplates 
	{
		//-----------------------------------------------
		// Declare Vars
        //-----------------------------------------------
        //A reference to an instance of this class.
		private static $pInstance;
		//A Unique Identifier
		private static $gSlug;
		//The array of templates that this plugin tracks.
		static public $gTemplates;
		static public $gCacheKey;
		static public $gTemplatesDir;
		static public $gTemplatesUrl;
		//
		static public $TD;
        //-----------------------------------------------
		// Constructor
		//-----------------------------------------------
		/**
		 * Initialize
		 */
		public function __construct() 
		{
			self::setupGlobals_fn();
			self::loadDependencies_fn();
			self::setupHooks_fn();
		}
		//-----------------------------------------------
		// Getter and Setters Methods
		//-----------------------------------------------
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
			if ( NULL == self::$pInstance ) :
				self::$pInstance = new self;
			endif;

			return self::$pInstance;
		}
		//-----------------------------------------------
		// Constructor Methods
		//-----------------------------------------------
		/**
		 * Sets some globals for the class
		 *
		 * @access private
		 */
		private static function setupGlobals_fn() 
		{
			$cacheKey = self::getCacheKey_fn();
			
			wp_cache_delete( $cacheKey , 'themes' );
			
			self::$gSlug = RGS_Company::$gSlug . '_templates';
			self::$gTemplates = array();
			
			self::$gCacheKey = 'page_templates-' . md5( trailingslashit( get_theme_root() ) . get_stylesheet() );

			self::$gTemplatesDir = RGS_Company::$gTemplatesDir;
			self::$gTemplatesUrl = RGS_Company::$gTemplatesUrl;
			
			self::$TD = RGS_Company::getTD_fn();
		}
		//-----------------------------------------------
        /**
		 * Load the required dependencies for this theme.
		 *
		 * Include the following files that make up the theme:
		 *
		 * @since    0.1.0
		 * @access   private
		 */
		private static function loadDependencies_fn() 
		{
			//----------
			// Add your templates to this array.
			self::$gTemplates = array(
				
				'companySingle.php' => __( 'Company Single', RGSO_TD )
			);
			//----------
			
		}
		//-----------------------------------------------
		private function setupHooks_fn() 
		{
            // Add a filter to the attributes metabox to inject template into the cache.
			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) :
		
				// 4.6 and older
				// add_filter( 'page_attributes_dropdown_pages_args', array( __CLASS__, 'registerTemplates_fn' ) );
		
			else :
		
				// Add a filter to the wp 4.7 version attributes metabox
				add_filter( 'theme_page_templates', array( __CLASS__, 'addNewTemplate_fn' ) );
		
			endif;
			// Add a filter to the save post to inject out template into the page cache
			add_filter( 'wp_insert_post_data', array( __CLASS__, 'registerTemplates_fn' ) );
	
			// Add a filter to the template include to determine if the page has our 
			// template assigned and return it's path
			add_filter( 'template_include', array( __CLASS__, 'viewTemplate_fn' ), 99 );
	
			// Register scripts and styles
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'registerScripts_fn' ), 101 );
		}

		//-----------------------------------------------
        // Hooks Methods
		//-----------------------------------------------
		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doens't really exist.
		 *
		 */
		static public function registerTemplates_fn( $atts ) 
		{
			// Create the key used for the themes cache
			$cacheKey = self::getCacheKey_fn(); 
	
			// Retrieve the cache list. 
			// If it doesn't exist, or it's empty prepare an array
			$gTemplates = wp_get_theme()->get_page_templates();
			
			if ( empty( $gTemplates ) ) :
				$gTemplates = array();
			endif;
			
			// New cache, therefore remove the old one
			wp_cache_delete( $cacheKey , 'themes' );
			
			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$gTemplates = array_merge( $gTemplates, self::$gTemplates );
			
			// Add the modified cache to allow WordPress to pick it up for listing available templates
			wp_cache_add( $cacheKey, $gTemplates, 'themes', 1800 );
	
			return $atts;
	
		} 
		//-----------------------------------------------
		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 */
		static public function addNewTemplate_fn( $postsTemplates ) 
		{
			$postsTemplates = array_merge( $postsTemplates, self::$gTemplates );

			return $postsTemplates;
		}
		//-----------------------------------------------
		/**
		 * Checks if the template is assigned to the page
		 */
		static public function viewTemplate_fn( $template ) 
		{
	
			global $post;
			
			if( $post ):
			
				$lTemplates = self::$gTemplates;
				$lMetaTemplate = get_post_meta( $post->ID );
			
				if ( ! isset( $lTemplates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) :

					return $template;

				endif;

				$file = self::$gTemplatesDir . get_post_meta( $post->ID, '_wp_page_template', true );

				// Just to be safe, we check if the file exist first
				if ( file_exists( $file ) ) :
					return $file;
				else :
					echo $file;
				endif;
			endif;

			return $template;
	
		}
		//-----------------------------------------------
		/**
		 * Registers our template css and js
		 */
	
		static public function registerScripts_fn() 
		{
			global $post;

			$lTemplates = self::$gTemplates;
			$lSlug = self::$gSlug;

			if( $post ):
				//
				$curTemplate = get_post_meta( $post->ID, '_wp_page_template', true );
				//
				if (! empty( $curTemplate ) and array_key_exists( $curTemplate, $lTemplates)) :

					$filename = pathinfo( $curTemplate, PATHINFO_FILENAME );
					$dirName = dirname( $curTemplate );

					if ( ! empty( $dirName ) ) :

						$masterEnqueue = RGS_Company::getEnqueueName_fn();
						$thisSlug = $lSlug . '-' . str_replace( "-", "_", $dirName );

						$cssDir = self::$gTemplatesDir . trailingslashit( $dirName ) . trailingslashit( 'styles' );
						$cssUrl = self::$gTemplatesUrl . trailingslashit( $dirName ) . trailingslashit( 'styles' );
						
						// LOAD CUSTOM STYLES
						if ( file_exists( $cssDir  . 'style.css' ) ) :
								
							wp_enqueue_style( 
								$thisSlug, 
								$cssUrl  . 'style.css',
								array( $masterEnqueue )
							);

						endif;

						// LOAD CUSTOM MEDIA QUERIES
						if ( file_exists( $cssDir  . 'media-queries.css' ) ) :

							wp_enqueue_style( 
								$thisSlug . '-media-queries', 
								$cssUrl  . 'media-queries.css',
								array( $thisSlug )
							);
							
						endif;

						// LOAD CUSTOM SCRIPTS
						$jsDir = self::$gTemplatesDir . trailingslashit( $dirName ) . trailingslashit( 'scripts' );
						$jsUrl = self::$gTemplatesUrl . trailingslashit( $dirName ) . trailingslashit( 'scripts' );
			
						if ( file_exists( $jsDir . 'script.js' ) ) :
					
							wp_enqueue_script( 
								$thisSlug, 
								$jsUrl . 'script.js', 
								array(), 
								FALSE, 
								TRUE 
							);
				
							wp_localize_script( 
								$thisSlug, 
								strtoupper( str_replace( "-", "_", $dirName ) ), 
								array( 
									'post_id'=>$post->ID,
									'ajaxurl' => admin_url( 'admin-ajax.php' )  
								) 
							);
				
						endif;
					endif;
				endif;
			endif;

		}
		//-----------------------------------------------
        // Utilities
        //-----------------------------------------------
        static public function getCacheKey_fn() 
		{
			return self::$gCacheKey;
		}
		//-----------------------------------------------
        // Tools
		//-----------------------------------------------
        
		//-----------------------------------------------
		//-----------------------------------------------
	};
	//-------------------------------------------------------------------------------
	if( ! function_exists( 'rgsCompanyTemplates_fn' ) ):
		function rgsCompanyTemplates_fn() 
		{
			return RGS_CompanyTemplates::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_CompanyTemplates' ] ) ):
		$GLOBALS[ 'RGS_CompanyTemplates' ] = rgsCompanyTemplates_fn();
	endif;
	//-------------------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------------------