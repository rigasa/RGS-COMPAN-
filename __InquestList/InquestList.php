<?php
/**
 * Plugin name: RGS Inquest List;
 * Description: SImple plugin using WP_List_Table
 */
if( !defined(  'ABSPATH' ) ) exit;

if( !class_exists( 'RGS_InquestList' ) )
{
	class RGS_InquestList
	{
		//------------------
		public $list;
		//------------------
		public function __construct()
		{
			add_action( 'admin_menu', [ $this, 'add_pages' ] );
			add_action( "wp_ajax_rgsAction", [ $this, 'ajaxhandler' ] );
		}
		//------------------
		public function add_pages()
		{
			$hook = add_menu_page(
				'List Table Stuff',
				'Simple List Table',
				'manage_options',
				basename( __FILE__ ),
				[ $this, 'list_page' ],
				'dashicons-groups'
			);
			
			add_action( 'admin_print_scripts-'.$hook, [ $this, 'ajax_scripts' ] );
		}
		//------------------
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
		//------------------
		public function ajax_scripts()
		{
			wp_enqueue_script( 
				'rgsAjax', plugin_dir_url( __FILE__ ).'/js/rgsAjax.js', 
				array( 'jquery' ), 
				true
			);
			
			$proto = isset( $_SERVER[ 'HTTPS' ] ) ? 'https' : 'http';
			$URL = admin_url( 'admin-ajax.php', $proto );

			$params = [
				'action' 	=>  'rgsAction', // Mais pourquois 'RAOUL' ? … parce que !
				'str'		=> 'RAOUL !',
				'URL'		=> $URL
			];
			wp_localize_script( 'rgsAjax', 'rgsAjaxParams', $params );

		}
		//------------------
		public function ajaxhandler()
		{
			$this->list = RGS_InquestTable::get_instance();
			echo json_encode( $this->list->ajax_response() );
			die();
		}
		//------------------
		
	}
	add_action( 'plugins_loaded', 'load_simple_list_table' );
	function load_simple_list_table()
	{
		new RGS_InquestList;
	}
}
