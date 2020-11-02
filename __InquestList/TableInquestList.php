<?php
/**
 * Plugin name: RGS Inquest List;
 * Description: SImple plugin using WP_List_Table
 */
if( !defined(  'ABSPATH' ) ) exit;
if( !class_exists( 'WP_List_Table' ) )
{
	if( file_exists( ABSPATH.'/wp-admin/includes/class-wp-list-tabe.php' ) ){
		require_once( ABSPATH.'/wp-admin/includes/class-wp-list-tabe.php' );
	}else
	{
		require_once( ABSPATH.'/wp-admin/includes/class-jst-list-tabe.php' );
	}	


}	

if( !class_exists( 'RGS_InquestTable' ) ) :
	class RGS_InquestTable extends WP_List_Table
	{	
		private $posttablename 		= 'posts';
		private $userstablename		= 'users'; 
		public static $instance		= null;
		private $tablename		    = 'posts';
		private $perpage		    = 4;
		private $deletenonceaction	= '_jst_detele_post';
		private $deletenoncename 	= 'jstdelnce';
		private $editnonceaction 	= '_jst_edit_post';
		private $editnoncename 		= 'jstedtnce';
		private $publishnonceaction	= '_jst_publish_post';
		private $publishnoncename	= 'jstpubnce'; 

		public $foundrows;

		public function __construct()
		{
			parent::__construct( [
				'singular'	=> 'post',
				'plural'	=> 'posts',
				'ajax'		=> true
			] );
		}

		public function column_title( $item )
		{
			// Publish
			$publishnonceaction = $this->publishnonceaction;
			$publish_url = add_query_arg([
				'page'		=> $_REQUEST[ 'page' ],
				'post'		=> $item[ 'ID' ],
				'action'	=> 'publish'
			]);
			$nonced_publish_url = wp_nonce_url( $publish_url, $publishnonceaction, $this->publishnoncename );

			// Edit
			$proto = isset( $_SERVER[ "HTTPS" ] ) ? 'https' : 'http';
			$edit_url = admin_url( 'post.php', $proto );
			$edit_url = add_query_arg( [
				'post'		=> $item[ 'ID' ],
				'action'	=> 'edit'
			], $edit_url );

			// Delete 
			$deletenonceaction = $this->deletenonceaction;
			$delete_url = add_query_arg( [
				'page'		=> esc_url( $_REQUEST[ 'page' ] ),
				'action'	=> 'delete',
				'post'		=> $item[ 'ID' ]
			] );
			$nonced_delete_url = wp_nonce_url( $delete_url, $deletenonceaction, $this->deletenoncename );

			$actions = [
				'edit'		=> sprintf(
					'<a href="%1$s">%2$s</a>',
					$edit_url,
					__( 'Edit', 'jst' )
				),
				'delete'	=> sprintf(
					'<a href="%1$s">%2$s</a>',
					$nonced_delete_url,
					__( 'Delete', 'jst' )
				),
				'publish'	=> sprintf(
					'<a href="%1$s">%2$s</a>',
					$nonced_publish_url,
					__( 'Publish', 'jst' )
				)
			];
			return sprintf( "%s %s", $item[ 'title' ], $this->row_actions( $actions ) );
		}


		public function column_default( $item, $column )
		{
			switch( $column )
			{
				case "date":
				case 'user':
					return $item[ $column ];
					break;
				default:
					return print_r( $item, 1 );
			}
		} 

		public function column_cb( $item ){
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s"/>',
				$this->_args[ "singular" ],
				$item[ "ID" ]
			);
		}


		public function get_columns()
		{
			return [
				'cb'	=> '<input type="checkbox" />',
				'title' => __( 'Title', 'jst' ),
				'user'	=> __( 'User', 'jst' ),
				'date'	=> __( 'Date', 'jst' )
			];
		}

		public function get_sortable_columns()
		{
			return [
				'title'		=> array( 'post_title', true ),
				'user'		=> array( 'user_nicename', false ),
				'date'		=> array( 'post_date', false )
			];
		} 

		protected function get_bulk_actions(){
			return [
				'publish'	=> __( 'Publish', 'jst' ),
				'delete' 	=> __( 'Delete', 'jst' ),
			];
		}


		public function process_action()
		{
			if( !isset( $_REQUEST[ 'post' ] ) )
			{
				return false;
			}
			$current_action = ( FALSE !== $this->current_action() || 'rgsAction' != $this->current_action() ) ? $this->current_action() : 
					( isset( $_REQUEST[ 'action2' ] ) && is_array( $_REQUEST[ 'action2' ], [ 'delete', 'publish' ] ) ) ? $_REQUEST[ 'action2' ] : false;

				if( FALSE ===  $current_action )
				{
					return 'no action found';
				}
			
			
			if( 
				( !is_array( $_REQUEST[ 'post' ] ) && !filter_var( $_REQUEST[ 'post' ], FILTER_VALIDATE_INT ) ) ||
				( is_array( $_REQUEST[ 'post' ] ) && !wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'bulk-'.$this->_args[ 'plural' ] ) ) ||
				( !is_array( $_REQUEST[ 'post' ] ) && $current_action == 'delete' && !wp_verify_nonce( $_REQUEST[ $this->deletenoncename ], $this->deletenonceaction ) ) || 
				( filter_var( $_REQUEST[ 'post' ], FILTER_VALIDATE_INT ) && $current_action == 'publish' && !wp_verify_nonce( $_REQUEST[ $this->publishnoncename ], $this->publishnonceaction ) )
			)
			{
				wp_die( 'Vous n’avez pas les droits suffisants pour continuer sur cette page !' );
			}
			if( 'delete' == $current_action )
			{
				$this->action_delete( $_REQUEST[ 'post' ] );
			}
			if( 'publish' == $current_action )
			{
				$this->action_publish( $_REQUEST[ 'post' ] );
			}
		}

		private function format_ids( $ids )
		{
			if( !is_array( $ids ) ) $ids = array( $ids );
			foreach( $ids as $k=>$id )
			{
				if( !filter_var( $id, FILTER_VALIDATE_INT ) || $id < 0 ) return false;
			}
			return $ids;
		}

		private function action_delete( $ids  )
		{
			if( FALSE === ( $ids = $this->format_ids( $ids ) ) )
			{
				return false;
			}
			$ids = implode( ', ', $ids );
			global $wpdb;
			$tablename = $wpdb->prefix.$this->tablename;
			$sql = "
			DELETE FROM {$tablename} 
			WHERE ID in (".$ids.")";
			return $wpdb->query( $sql );
		}

		private function action_publish( $ids )
		{
			if( FALSE === ( $ids = $this->format_ids( $ids ) ) )
			{
				return false;
			}

			$ids = implode( ', ', $ids );
			global $wpdb;
			$tablename = $wpdb->prefix.$this->tablename;
			$sql = "
			UPDATE {$tablename} 
			SET post_status = 'publish'  
			WHERE ID in (".$ids.")";
			return $wpdb->query( $sql );
		}

		private function getDataInfos( $page = 1 )
		{
			global $wpdb;
			$page = $page <= 1 ? 1 : (int) $page;

			$posttablename = $wpdb->prefix.$this->posttablename;
			$userstablename = $wpdb->prefix.$this->userstablename;
			$perpage = $this->perpage;

			$sql = 
			"SELECT SQL_CALC_FOUND_ROWS p.ID, post_title title, post_date date, u.user_nicename user 
			FROM {$posttablename} p
			JOIN {$userstablename} u 
			ON p.post_author = u.ID 
			WHERE p.post_status = 'draft' 
			AND p.post_type = 'post'";
			$orderby	= isset( $_REQUEST[ 'orderby' ] ) ? esc_sql( $_REQUEST[ 'orderby' ] ) : 'post_title';
			$order 		= isset( $_REQUEST[ "order" ] )  ? esc_sql( $_REQUEST[ 'order' ] ) :  'ASC';
			$sql .= " ORDER BY ".$orderby." ".$order; 
			$sql .= " LIMIT %d,%d";

			$sql = $wpdb->prepare( $sql, ( $page -1 ) * $perpage, $perpage );
			$items = $wpdb->get_results( $sql, ARRAY_A );
			$this->foundrows = $wpdb->get_var( "SELECT FOUND_ROWS()" );

			return array(
				'items'		=> $items,
				'foundrows'	=> $this->foundrows
			);
		} 

		public function prepare_items()
		{
			$this->process_action();

			$columns 	= $this->get_columns();
			$sortable 	= $this->get_sortable_columns();
			$hidden 	= ['ID'];

			$this->_column_headers = [
				$columns, $hidden, $sortable
			];
			$paged = ( isset( $_REQUEST[ "paged" ] ) && $_REQUEST[ "paged" ] && $_REQUEST[ "paged" ] > 1) ? (int) $_REQUEST[ 'paged' ] : 1;
			$itemInfos = $this->getDataInfos( $paged );

			$this->items = $itemInfos[ 'items' ];

			$totalitems = $itemInfos[ 'foundrows' ];
			$totalPages = ceil( $totalitems / $this->perpage );

			$this->set_pagination_args(
				[
					'total_items'	=> $totalitems,
					'per_page'	=> $this->perpage,
					'total_pages'	=> $totalPages,
					'orderby'	=> isset( $_REQUEST[ 'orderby' ] ) ? esc_attr( $_REQUEST[ 'orderby' ] ) : 'title',
					'order'		=> isset( $_REQUEST[ 'order' ] ) ? esc_attr( $_REQUEST[ 'order' ] ) : 'ASC'
				]
			);
		}

		public function display()
		{
			$nce = wp_create_nonce( 'jst_ajx_nce' );
			echo '';
			parent::display();
		}
		
		public function ajax_response()
		{
			if( !wp_verify_nonce( $_REQUEST['nce'], 'jst_ajx_nce' ) )
			{
				return false;
			}
			$this->prepare_items();
			ob_start();
			$this->print_column_headers();
			$headers = ob_get_clean();
			ob_start();
			$this->display_rows_or_placeholder();
			$rows = ob_get_clean();
			ob_start();
			echo $this->pagination( 'top' );
			$tablenav_top = ob_get_clean();
			ob_start();
			echo $this->pagination( 'bottom' );
			$tablenav_bottom = ob_get_clean();

			$resp = [
				'rows'			=> $rows,
				'headers'		=> $headers,
				'pagination'	=> [
					'top'		=> $tablenav_top,
					'bottom'	=> $tablenav_bottom
				]
			];
			extract( $this->_pagination_args, EXTR_SKIP );
			if( isset( $total_items ) )
			{
				$resp[ 'total_items_i18n' ] = sprintf( 
						_n( '% item', '% items', $total_items ), 
						number_format_i18n( $total_items ) );
			}
			if( isset( $total_pages ) )
			{
				$resp[ 'total_pages' ] 		= $total_pages;
				$resp[ 'total_pages_i18n' ]	= number_format_i18n( $total_pages );
			}
			return $resp;
		}
		
		
		public static function get_instance()
		{
			self::$instance = is_null( self::$instance ) ? new self() : self::$instance;
			return self::$instance; 
		}



	};
endif;