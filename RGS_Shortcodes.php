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
 * @class RGS_Shortcodes
 * @fullname RiGaSa Companion
 * @package RGS_Shortcodes
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_Shortcodes.php
 * @version 0.0.1
 * @created 2020-10-07
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-07
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
 * Class FPDF http://www.fpdf.org/fr/doc/index.php
*/                                                                                 

//--------------------------------------
if ( ! defined( 'ABSPATH' ) ) exit; // SECURITY : Exit if accessed directly
//--------------------------------------
if( ! class_exists( 'RGS_Shortcodes' ) ):
	//----------------------------------
	class RGS_Shortcodes
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
		//------------------------------------------------------------
		static public $gBasename;
		// Plugin Hierarchy
		//------------------------------------------------------------
		private static $folderDir;
		//-------------------------------------------------------------
		public static $gScItems;
		//--------------------------------------------------------------
		const K_SLUG = 'rgsShortcodes';
		const K_PREFIX = 'rgsShortcodes-';
		const K_VERS = '0.0.1';
		
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
			self::$folderDir      	= RGS_Company::$gAssetsDir . trailingslashit( 'shortcodes' );
			//---
			self::$gSlug 			= sanitize_title( self::K_SLUG );
			//---
			self::$gScItems = array();
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
			//------------------------------------------------------
			
			//------------------------------------------------------
		}
		//---------------------------------------------------------------
		private function setupHooks_fn() 
		{
			// SETUPS
			//---------------------------------------------------
			add_action( 'wp_init', array(__CLASS__, 'initClass_fn') );
			//---------------------------------------------------
			add_filter ( 'widget_text', 'do_shortcode' );
			//---------------------------------------------------
			add_action( 'init', array( __CLASS__, 'addShortcodes_fn' ), 10 );
			//---------------------------------------------------
			add_action( 'after_setup_theme', array( __CLASS__, 'override_fn' ), 10 );
			//---------------------------------------------------
			//---------------------------------------------------
			//---------------------------------------------------
		}
		//---------------------------------------------------------------
		// Getters
		//---------------------------------------------------------------
		static function getSlug_fn()
		{
			return RGS_Company::getSlug_fn();
		}
		//---------------------------------------------------------------
		static function getTD_fn()
		{
			return RGS_Company::getTD_fn();
		}
		//---------------------------------------------------------------
		// HOOKS
		//---------------------------------------------------------------
		static function initClass_fn()
		{
			
		}
		//---------------------------------------------------------------
		static function override_fn()
		{
			
		}
		//---------------------------------------------------------------
		static function addShortcodes_fn()
		{
			//-----------------------------
			self::$gScItems = self::scandirShortcodes_fn( self::$folderDir );
			#echo '<pre>ADD SHORTCODES: '.print_r( self::$gScItems, TRUE).'</pre>';
			#die( '---' );
			//-----------------------------
		}
		//---------------------------------------------------------------
		// TOOLS
		//---------------------------------------------------------------
		static public function checkHexaColor_fn( $value ) 
		{ 
			// Function that will check if value is a valid HEX color.
			if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) : // if user insert a HEX color with #     
				return true;
			endif;

			return false;
		}
		//---------------------------------------------------------------
		static function scandirShortcodes_fn( $_dir = '' )
		{
			$_arr = array();
			
			if( is_dir( $_dir ) ):
				
				if ( $_dh = opendir( $_dir ) ) :
			
					while ( ( $_entry = readdir( $_dh ) ) !== false ) :
						
						if ( $_entry !== "." and $_entry !== ".." ) :
			
							$_shortcode_dir = $_dir . trailingslashit( $_entry );
			
							if( is_dir( $_shortcode_dir ) ):
			
								if( file_exists( $_shortcode_dir . 'shortcode.php' ) ):
			
									include_once( $_shortcode_dir . 'shortcode.php' );
									
									if( isset( $_shc_attr ) ):
										
										$_arr[ $_entry ] = array();
										$_arr[ $_entry ] = array_replace_recursive( $_arr[ $_entry ], $_shc_attr );
										
										unset( $_shc_attr );
										
									endif;
									
								endif; // IS SHORTCODE
							endif;
							
						endif;
			
					endwhile;
			
					closedir( $_dh );
			
				endif;
			
			endif;
			
			ksort( $_arr );
			
			return $_arr;
		}
		//---------------------------------------------------------------
		static function removeWpautop_fn( $_content ) 
		{
			$_content = do_shortcode( shortcode_unautop( $_content ) );
			$_content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $_content );
			//$_content = str_replace( array( "\r","\n","\t" ), array( '','','' ), $_content );
			return $_content;
		}
		//---------------------------------------------------------------
		static function getShortcodes_fn()
		{
			//self::$gScItems = self::scandirShortcodes_fn( self::$folderDir );
			$_items = self::$gScItems;
			$_names = array();
			foreach ( $_items as $_key => $_row ) :
				$_names[$_key]  = $_row['name'];
			endforeach;
			
			array_multisort( $_names, SORT_ASC, $_items );
			
			return $_items;
		}
		//---------------------------------------------------------------
		// Shortcode Generator
		//---------------------------------------------------------------
		static function buildShortcode_fn( $name )
		{
			$_sc = self::$gScItems; //self::getShortcodes_fn();
			
			if( ! isset( $_sc[ $name ] ) ) return;
			
			$Sc = $_sc[ $name ];
			
			$shortcode = "[$name";
			
			foreach( $Sc[ 'fields' ] as $_key => $_type_arr ) :
				
				$_val = ( isset( $_type_arr[ 'value' ] ) ) ? $_type_arr[ 'value' ]: '';
				if( $Sc[ 'fields' ][ $_key ][ 'type' ] == 'checkbox' ):
					$_val = ( $_val == 'true' ) ? 'true': 'false';
				endif;
				$shortcode .= ' ' . $_key .'="' . $_val. '"';
				
			endforeach;
			
			$shortcode .= ']';
			
			if( $Sc[ 'content' ] != false ) :
				
				$shortcode .= $Sc[ 'content' ];
				$shortcode .= "[/$name]";
				
			endif;
			
			return $shortcode;
		}	
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsShortcodes_fn' ) ):
		function rgsShortcodes_fn() 
		{
			return RGS_Shortcodes::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_Shortcodes' ] ) ):
		$GLOBALS[ 'RGS_Shortcodes' ] = rgsShortcodes_fn();
	endif;
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsClearAutop_fn' ) ):
		function uds_clear_autop($content)
		{
    		$content = str_ireplace('<p>', '', $content);
    		$content = str_ireplace('</p>', '', $content);
    		$content = str_ireplace('<br>', '', $content);
    		return $content;
}
	endif;
endif;
//-----------------------------------------------------------------------
