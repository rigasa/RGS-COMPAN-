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
 * @class RGS_HtmlToPDF
 * @fullname Eco Citoyen Management
 * @package RGS_HtmlToPDF
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_HtmlToPDF.php
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
if( ! class_exists( 'RGS_HtmlToPDF' ) ):
	//----------------------------------
	class RGS_HtmlToPDF
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
		static public $gAdminPageId;
		//---
		const K_SLUG = 'rgsHtmlToPdf';
		const K_PREFIX = 'rgsHtmlToPdf-';
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
			//---
			self::$gAdminPageId = null;
			//---
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
			//------------------------------------------------------
			$fileRequired = RGS_Company::$gLibsDir . trailingslashit( 'fpdf' ) . 'fpdf.php';
			if( file_exists( $fileRequired ) ):
				require_once( $fileRequired );
			endif;
			//------------------------------------------------------
			$dirPlugins = RGS_Company::$gLibsDir . trailingslashit( 'fpdf' ) . trailingslashit( 'plugins' );
			$fileRequired = $dirPlugins . 'loadPlugins.php';
			if( file_exists( $fileRequired ) ):
				require_once( $fileRequired );
			endif;
			//------------------------------------------------------
		}
		//---------------------------------------------------------------
		private function setupHooks_fn() 
		{
			// SETUPS
			//---------------------------------------------------
			add_action( 'wp_init', array(__CLASS__, 'initClass_fn') );
			//------
			// CREATE PDF BY AJAX
			//------
			add_action( 'wp_ajax_createPDF', array(__CLASS__, 'createPDF_fn') );
			add_action( 'wp_ajax_nopriv_createPDF', array(__CLASS__, 'createPDF_fn') );
			//---------------------------------------------------
		}
		//---------------------------------------------------------------
		// CPT
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
		static function initClass_fn()
		{
			
		}
		//---------------------------------------------------------------
		// TEMPLATES
		//---------------------------------------------------------------
		static function getHeader_fn()
		{
			$fileCustomHeader = null;
			//------------------------------------------------------
			$file = RGS_Company::$gTemplatesDir . 'customHeader.php';
			if( file_exists( $file ) ):
				$fileCustomHeader = file_get_contents($file);
			endif;
			//------------------------------------------------------
			return $fileCustomHeader;
		}
		//---------------------------------------------------------------
		static function getFooter_fn()
		{
			$fileCustomFooter = null;
			//------------------------------------------------------
			$file = RGS_Company::$gTemplatesDir . 'customFooter.php';
			if( file_exists( $file ) ):
				$fileCustomFooter = file_get_contents($file);
			endif;
			//------------------------------------------------------
			return $fileCustomFooter;
		}
		//---------------------------------------------------------------
		// PDF CONSTRUCTOR
		//---------------------------------------------------------------
		static function constructPDF_fn( $args )
		{
			if( ! class_exists('FPDF')):
				return array('error' => __('Page to PDF empty', self::getTD_fn() ) );
			else:
				//-----------
				//create a FPDF object
				$pdf=new FPDF();

				//set document properties
				$pdf->SetAuthor('Lana Kovacevic');
				$pdf->SetTitle('FPDF tutorial');

				//set font for the entire document
				$pdf->SetFont('Helvetica','B',20);
				$pdf->SetTextColor(50,60,100);

				//set up a page
				$pdf->AddPage('P');
				$pdf->SetDisplayMode(real,'default');

				//insert an image and make it a link
				//$pdf->Image('logo.png',10,20,33,0,' ','http://www.fpdf.org/');

				//display the title with a border around it
				$pdf->SetXY(50,20);
				$pdf->SetDrawColor(50,60,100);
				$pdf->Cell(100,10,'FPDF Tutorial',1,0,'C',0);

				//Set x and y position for the main text, reduce font size and write content
				$pdf->SetXY (10,50);
				$pdf->SetFontSize(10);
				$pdf->Write(5,'Congratulations! You have generated a PDF.');

				//Output the document
				$pdf->Output('example1.pdf','I');

				echo '<pre>createPDF::: '.print_r( $pdf, TRUE).'</pre>';
				die('DEBUG');


				return array('success' => array(
					'file' => 'example1.pdf'
				));
				//-----------
			endif;
		}
		//---------------------------------------------------------------
		// AJAX CALLBACK
		//---------------------------------------------------------------
		static function createPDF_fn()
		{

			$pageHTML = (isset( $_POST['pageHTML'] ) ) ? $_POST['pageHTML'] : '';
			//
			if( empty( $pageHTML ) ) :
				wp_send_json_error( __('Page to PDF empty', self::getTD_fn() ) );
			else:
				//-----------
				/*$isConstruct = self::constructPDF_fn( $pageHTML );
				//-----------
				if( !isset($isConstruct['error']) ):
					wp_send_json_error( $isConstruct['error'] );
				else:
					//-----------
				endif;
				*/
				echo '<pre>createPDF::: '.print_r( $pageHTML, TRUE).'</pre>';
					die('DEBUG');
				//
			endif;

			wp_die();
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsHTML2PDF_fn' ) ):
		function rgsHTML2PDF_fn() 
		{
			return RGS_HtmlToPDF::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_HtmlToPDF' ] ) ):
		$GLOBALS[ 'RGS_HtmlToPDF' ] = rgsHTML2PDF_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------
