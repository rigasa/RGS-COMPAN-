(function(jQuery){
	var process = {
		__query	: function( queryStr, param ){
			var pairs = queryStr.split( "&" );
			for( var i=0; i< pairs.length; i++ ){
				var pair = pairs[i].split( "=" );
				console.log( pair );
				var k = pair[0], v = pair[1];
				if( param == k ) return v;
			}
			return false;
		},
		tempProcessData : {},
		queryString : function( link ){
			return link.attr( 'href' ).split( '?' )[1];
		},
		update  : function( data ){

			data.action = rgsAjaxParams.action;
			data.nce = jQuery( '#rgsAjaxNonce').val();

			jQuery.ajax({

				URL 		: rgsAjaxParams.url,
				data 		: data,
				success 	: function( resp ){

					resp = JSON.parse( resp );
					console.log( resp );

					if( resp.pagination && resp.pagination.top.length ){

						jQuery(".tablenav.top .tablenav-pages").replaceWith( jQuery(resp.pagination.top) );

					}
					if( resp.headers && resp.headers.length ){

						jQuery("thead tr").empty().html( jQuery(resp.headers) );
						jQuery("tfoot tr").empty().html( jQuery(resp.headers) );
					}
					if( resp.rows && resp.rows.length ){
						jQuery('#the-list').empty().html( jQuery(resp.rows) );
					}
					if( resp.pagination && resp.pagination.bottom.length ){

						jQuery("tablenav.bottom .tablenav-pages").replaceWith( jQuery(resp.pagination.bottom) );
					}
				}
		});
		},
		init : function(){
			
			jQuery( document ).on('click', '.manage-column.sortable a, .manage-column.sorted a', 		function(e){
				e.preventDefault();
				var data = {
					'paged'		: process.__query( process.queryString( jQuery(this) ), 'paged' ),
					'orderby'	: process.__query( process.queryString( jQuery(this) ), 'orderby' ),
					'order'		: process.__query( process.queryString( jQuery(this) ), 'order' ),

				};
				process.update( data );
			});
			
			
			jQuery(document).on( 'click', '.pagination-links a', function(e){
				e.preventDefault();
				var orderby = ( orderby = process.__query( process.queryString( jQuery(this) ), 'orderby' ) ) ? 
				orderby : 
				process.__query( process.queryString( jQuery('.column-primary a') ), 'orderby' );

				var order = ( order = process.__query( process.queryString( jQuery(this), 'order' ) ) ) ?
				order : 
				process.__query( process.queryString( jQuery('.column-primary a') ), 'order' );				
				var data = {
					'paged'		: process.__query( process.queryString( jQuery( this ) ), 'paged' ),
					'orderby'	: orderby,
					'order'		: order
				};
				process.update( data );
			});
			
			jQuery(document).on( 'keyup','input[name=paged]', function(e){
				var orderby = 
				( orderby = process.__query( process.queryString( jQuery('.tablenav.top .pagination-links a:eq(0)'), 'order' ) ) ) ? 
					orderby : 
					process.__query( process.queryString( jQuery('.column-primary a') ), 'orderby' );
				var order = 
				( order = process.__query( process.queryString( jQuery('.tablenav.top .pagination-links a:eq(0)'), 'order' ) ) ) ? 
					order : 
					process.__query( process.queryString( jQuery('.column-primary a') ), 'order' );
				process.tempProcessData = {
					paged 	: parseInt( jQuery(this).val() ) || 1,
					order 	: order,
					orderby : orderby
				};			
			});
			
			jQuery(document).on( 'keypress', 'input[name=paged]', function(e){
				if( e.which === 13 ){
					e.preventDefault();
					console.log( process.tempProcessData.paged );
					if( process.tempProcessData.paged ){
						console.log( process.tempProcessData );
						process.update( process.tempProcessData );
					}
					return false;
				}
			});
			
			jQuery(document).on( 'click', '.row-actions a', function(e){
				if( !process.__query( process.queryString( jQuery(this) ), 'preview' ) ){
					e.preventDefault();
					var deletenonce = process.__query( process.queryString( jQuery(this) ), 'jstdelnce' );
					var publishnonce = process.__query( process.queryString( jQuery(this) ), 'jstpubnce' );
					var post = process.__query( process.queryString( jQuery(this) ), 'post' );
					var orderby = ( !jQuery('.sorted a').length ) ? 
						process.__query( process.queryString( jQuery('.column-primary a') ), 'orderby' ) :
						process.__query( process.queryString( jQuery('.sorted a:eq(0)') ), 'orderby' );
					var order = ( !jQuery('.sorted a').length ) ? 
						process.__query( process.queryString( jQuery('.column-primary a') ), 'order' ) :
						process.__query( process.queryString( jQuery('.sorted a:eq(0)') ), 'order' );
					var data = {
						orderby	: orderby,
						order	: order,
						post 	: post
					};
					if( deletenonce ){
						data.jstdelnce = deletenonce;
						data.action2 = 'delete';
					}
					else if( publishnonce ){
						data.jstpubnce = publishnonce;
						data.action2 = 'publish';
					}
					process.update( data );
				}
			}); 
			
			
			
			
		}
	};
	window.onload = function(){
		process.init();
	};
}( jQuery );