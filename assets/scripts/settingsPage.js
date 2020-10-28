/*
 ____   _   ____         ____          
|  _ \ (_) / ___|  __ _ / ___|   __ _  
| |_) || || |  _  / _` |\___ \  / _` | 
|  _ < | || |_| || (_| | ___) || (_| | 
|_| \_\|_| \____| \__,_||____/  \__,_|                                          
*/
//----------------------------------------------------------
/**
 * @class RGS_Company
 * @fullname RiGaSa Companion
 * @package RGS_Company
 * @category Core
 * @filesource assets/plugins/Entreprise/assets/scripts/settingsPage.js
 * @version 0.0.1
 * @created 2020-10-04
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-04
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
*/  
//----------------------------------------------------------
//	OBJECT
//----------------------------------------------------------
if (typeof(oCompany) === undefined) { var oCompany = {}; }

(function() {
	"use strict";
	// -----------------------------
	// TABS
	// -----------------------------
	if( oCompany.exists('.nav-tab') ) {
		
		// Tabs
		var navTab = jQuery( '.nav-tab' );
		var tabs = jQuery( '.tabs' );
		//
		var tabToogle = function( hash ) {
			location.hash = hash || '';
			hash = hash || jQuery( 'a', navTab ).context[0].hash;
			navTab.removeClass( 'nav-tab-active' );
			var tabLink = hash ? jQuery( 'a[href="' + hash + '"]' ) : jQuery( 'a:first-child', navTab );
			tabLink.addClass( 'nav-tab-active' );
			tabs.hide();
			jQuery( hash ).show();
		};
		//
		tabToogle( window.location.hash );
		//
		navTab.on( 'click', function( e ) {
			e.preventDefault();
			var hash = e.target.hash;
			tabToogle( hash );
			history.replaceState( {page: hash}, 'title ' + hash, hash );
		});
	}
	//-------------------------
	// RADAR
	//-------------------------
	if( oCompany.exists('#oneGraph') ) {
		// -------------------------
		if( oCompany.exists('#themeChoice') ) {
			//
			oCompany.setGraph_fn('oneGraph', oCompany.getValues_fn());
			//
			jQuery( '#themeChoice' ).on('change', function( event ){
				event.preventDefault();
				//
				oCompany.setGraph_fn('oneGraph', oCompany.getValues_fn());
			});
		}
		// -------------------------
		jQuery( '#oneGraphSave' ).on('click', function( event ){
			event.preventDefault();
			//
			if(oCompany.zingChartExists){
				zingchart.exec('oneGraph', 'getimagedata', {
					filetype : 'png',
					callback : function(imagedata) {
						//console.log(imagedata);
						jQuery('#output_image').append('<img src="' + imagedata + '" alt="" />');
					}
				});
			//
			}
		});
		
	} else {
		//console.log( oCompany.lang.noLogoId );
	}
	//-------------------------
	// COLORS
	//-------------------------
	if( oCompany.exists('.color-field') ) {
		
		jQuery('.color-field').wpColorPicker();
		
	}
	// -----------------------------
})(jQuery);