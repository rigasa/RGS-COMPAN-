<?php
/**
 * Plugin name: RGS Inquest List;
 * Description: SImple plugin using WP_List_Table
 */
if( !defined(  'ABSPATH' ) ) exit;

if( !class_exists( 'RGS_InquestList' ) ):
	class RGS_InquestList
	{
		//--------------------------------------------------
		public $list;
		//---
		static public $gFile;
		static public $gDir;
		static public $gUrl;
		
		static public $gJsDir;
		static public $gJsUrl;
		//---
		//--------------------------------------------------
		public function __construct()
		{
			self::setupGlobals_fn();
			self::loadDependencies_fn();
			self::setupHooks_fn();
		}
		//--------------------------------------------------
		private function setupGlobals_fn() 
		{
			//----------------------------------------------
			self::$gFile          	= __FILE__;
			self::$gDir     		= trailingslashit( dirname( self::$gFile ) );
			self::$gUrl				= trailingslashit( get_site_url() ) . str_replace( ABSPATH, '', self::$gDir );
			//---
			self::$gJsDir      		= self::$gDir . trailingslashit( 'scripts' );
			self::$gJsUrl      		= self::$gUrl . trailingslashit( 'scripts' );
			//----------------------------------------------
		}
		//--------------------------------------------------
		private function loadDependencies_fn() 
		{
			//----------------------------------------------
			$fileRequired = self::$gDir . 'TableInquestList.php';
			if( file_exists( $fileRequired ) ):
				require_once( $fileRequired );
			endif;
			//----------------------------------------------
		}
		//--------------------------------------------------
		private function setupHooks_fn() 
		{
			//----------------------------------------------
			// SETUPS
			add_action( 'admin_menu', array(__CLASS__, 'add_pages' ) );
			add_action( "wp_ajax_rgsAction", array(__CLASS__, 'ajaxhandler' ) );
			//----------------------------------------------
		}
		//--------------------------------------------------
		public function add_pages()
		{
			$hook = add_menu_page(
				'List Table Stuff',
				'Simple List Table',
				'manage_options',
				basename( __FILE__ ),
				array(__CLASS__, 'list_page' ),
				'dashicons-groups'
			);
			
			add_action( 'admin_print_scripts-'.$hook, array(__CLASS__, 'ajax_scripts' ) );
		}
		//--------------------------------------------------
		public function list_page()
		{
			$this->list = RGS_InquestTable::get_instance();
			$this->list->prepare_items();
			?>
			<div class="wrap">
				<h1>Simple List Table</h1>
				<form action="" method="POST">
					
					
					<?php  $this->list->display(); ?>

				</form>
				

			</div>
			<?php
		}
		//--------------------------------------------------
		public function ajax_scripts()
		{
			if( is_file( self::$gJsDir . 'rgsAjax.js' ) ):
				wp_enqueue_script( 
					'rgsAjax', 
					self::$gJsUrl . 'rgsAjax.js', 
					array( 'jquery' ), 
					true
				);

				$proto = isset( $_SERVER[ 'HTTPS' ] ) ? 'https' : 'http';
				$URL = admin_url( 'admin-ajax.php', $proto );

				$args = array(
					'action' 	=> 'rgsAction', //
					'str'		=> 'RAOUL !',
					'URL'		=> $URL
				);
				wp_localize_script( 'rgsAjax', 'rgsAjaxParams', $args );
			endif;
		}
		//--------------------------------------------------
		public function ajaxhandler()
		{
			$this->list = RGS_InquestTable::get_instance();
			echo json_encode( $this->list->ajax_response() );
			die();
		}
		//--------------------------------------------------
		//--------------------------------------------------
		//--------------------------------------------------
		
	};
	//--------------------------------------------------
	if( ! function_exists( 'rgsInquestList_fn' ) ):

		function rgsInquestList_fn() 
		{
			new RGS_InquestList;
		};
	endif;
	//--------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_InquestList' ] ) ):

		$GLOBALS[ 'RGS_InquestList' ] = rgsInquestList_fn();

	endif;
	//--------------------------------------------------
	/*function load_simple_list_table()
	{
		new RGS_InquestList;
	}
	
	add_action( 'plugins_loaded', 'load_simple_list_table' );*/
endif;
