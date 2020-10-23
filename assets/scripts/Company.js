/*
 ____   _   ____         ____          
|  _ \ (_) / ___|  __ _ / ___|   __ _  
| |_) || || |  _  / _` |\___ \  / _` | 
|  _ < | || |_| || (_| | ___) || (_| | 
|_| \_\|_| \____| \__,_||____/  \__,_|                                          
*/
//----------------------------------------------------------
/**
 * @class EC_Company
 * @fullname Eco Citoyen Management
 * @package EC_Company
 * @category Core
 * @filesource assets/plugins/Entreprise/assets/scripts/Company.js
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
	// ---------------------------------
    // TOOLS
    // ---------------------------------
    if (typeof(oCompany.exists) === 'undefined') {
        oCompany.exists = function(pSelector) {
            return (jQuery(pSelector).length > 0);
    	};
    }
    // -----------------------------
    if (typeof(oCompany.urlExists_fn) !== 'function') {
		oCompany.urlExists_fn = function(url)
		{
			var http = new XMLHttpRequest();
			http.open('HEAD', url, false);
			http.send();
			return http.status !== 404;
		};
	}
    // -----------------------------
    if (typeof(oCompany.getFileExtension_fn) !== 'function') {
        oCompany.getFileExtension_fn = function( url ) 
		{
			if (url === null) {
        		return "";
    		}
			var index = url.lastIndexOf("/");
    		if (index !== -1) {
        		url = url.substring(index + 1); // Keep path without its segments
    		}
    		index = url.indexOf("?");
    		if (index !== -1) {
        		url = url.substring(0, index); // Remove query
    		}
    		index = url.indexOf("#");
    		if (index !== -1) {
        		url = url.substring(0, index); // Remove fragment
    		}
    		index = url.lastIndexOf(".");
    		return ( index !== -1 ) ? url.substring(index + 1) : "";
        };
    }
	//
	let file_frame;
	// -----------------------------
	// UPLOAD
	// -----------------------------
	if (typeof(oCompany.mediaUpload_fn) !== 'function') {
		oCompany.mediaUpload_fn = function(jQuerythis){
			//
			if (file_frame) { file_frame.close(); }
			//
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery(jQuerythis).data('uploader-title'),
				button: {
					text: jQuery(jQuerythis).data('uploader-button-text'),
				},
				multiple: false
			});
			
			file_frame.on('select', function() {
				
				const mediaAttachment = file_frame.state().get('selection').first().toJSON();
				//
				let mediaContent = '';
				let mediaUrl = mediaAttachment.url;
				
				if( mediaAttachment.type === 'video' ){
					//
					let ext = oCompany.getFileExtension_fn( mediaUrl );
					let videoUrl = mediaUrl.replace( '.' + ext, '' );

					mediaContent = '<video class="gallery-video-content" width="100%" height="auto" autoplay="1" loop="1" muted="" playsinline="" webkit-playsinline="" ';

					mediaContent += 'data-title="' + mediaAttachment.title + '" data-description="' + mediaAttachment.description + '" data-caption="' + mediaAttachment.caption + '">';

					mediaContent += '';

					if( oCompany.urlExists_fn( videoUrl + '.mp4' ) ){

						mediaContent += '<source src="' + videoUrl + '.mp4' + '" type="video/mp4">';

					}

					mediaContent += '';

					if( oCompany.urlExists_fn( videoUrl + '.webm' ) ){
						mediaContent += '<source src="' + videoUrl + '.webm' + '" type="video/webm">';
					}

					mediaContent += '';
					mediaContent += '</video>';
					//
				} else if( mediaAttachment.type === 'image' ){
					//
					mediaUrl = mediaAttachment.sizes.full.url;
					//if( oCompany.urlExists_fn( mediaUrl ) ){
						mediaContent = '<img class="cLogoImage" src="' + mediaAttachment.sizes.thumbnail.url + '" alt="" style="margin:0;padding:0;max-height:100px;float:none;" />';
					//}
					//
				}
				//
				jQuery('#cLogoID').val(mediaAttachment.id);
				//
				jQuery( '#cLogoWrapper' ).html( mediaContent ).css( 'display','block' );
				//
			});
			//
			file_frame.open();
			//
		};
	}
	// -----------------------------
	if (typeof(oCompany.hasMedia_fn) !== 'function') {
        oCompany.hasMedia_fn = function() {
            return oCompany.exists('#cLogoWrapper .cLogoImage');
    	};
    }
	// -----------------------------
	if (typeof(oCompany.addMedia_fn) !== 'function') {
        oCompany.addMedia_fn = function() {
            
			jQuery( '#cAddMediaButton' ).on('click', function( event ){
				event.preventDefault();
				oCompany.mediaUpload_fn(this);
			});
			
    	};
    }
	// -----------------------------
	if (typeof(oCompany.delMedia_fn) !== 'function') {
        oCompany.delMedia_fn = function() {
            
			jQuery( '.cRemoveMediaButton' ).on('click', function( event ){
				event.preventDefault();

				if( oCompany.hasMedia_fn() ){
					if( confirm( oCompany.lang.delMedia ) ){
						console.log('REMOVE MEDIA !');
						
						jQuery('#cLogoID').val('');
   						jQuery('#cLogoWrapper').html('<img class="cLogoImage" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
						
					}
				} else {
					alert( oCompany.lang.noMediaToDel );
				}
			});
			
    	};
    }
	// -----------------------------
	if (typeof(oCompany.getQueryStringParams_fn) !== 'function') {
        oCompany.getQueryStringParams_fn = function(params, url) {
            
			// first decode URL to get readable data
			var href = decodeURIComponent(url || window.location.href);
			// regular expression to get value
			var regEx = new RegExp('[?&]' + params + '=([^&#]*)', 'i');
			var value = regEx.exec(href);
			// return the value if exist
			return value ? value[1] : null;
			
    	};
    }
	// -----------------------------
	// ONLY TAXONOMY CAMPAIGN
	// -----------------------------
	const isCustomPTaxo = oCompany.getQueryStringParams_fn( 'taxonomy', window.location.href );
	// -----------------------------
	if(isCustomPTaxo === oCompany.taxoCampaign ){
		// -----------------------------
		if( oCompany.exists('#cLogoID') ) {
			// -------------------------
			oCompany.addMedia_fn();
			oCompany.delMedia_fn();
			// -------------------------

		} else {
			console.log( oCompany.lang.noLogoId );
		}
	}
	// -----------------------------
	// GRAPHS
	// -----------------------------
	oCompany.zingChartExists = (typeof(zingchart) === 'object');
	// -------------------------
	// THEME
	// -------------------------
	if (typeof(oCompany.getValues_fn) !== 'function') {
		oCompany.getValues_fn = function(pTheme) {
			let values = [3, 5, 8, 4, 2, 6];
				
			return values;
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleItem_fn) !== 'function') {
		oCompany.getStyleItem_fn = function(pTheme) {
			
			//To style your scale labels.
			let colorItem = 'black';

			if( pTheme === 'dark' ){
				colorItem = 'white';
			}
			return { 
				'font-color': colorItem,
				'font-family': "Georgia",
				'font-size': 12,
				'font-weight': "bold",
				'font-style': "italic"
			};
		
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleTick_fn) !== 'function') {
		oCompany.getStyleTick_fn = function(pTheme) {

			//Tick Marks
			let colorItem = 'orange';

			if( pTheme === 'dark' ){
				colorItem = 'orange';
			}
			return { 
				'line-color': colorItem,
				'line-width': 3,
				size: 15,
				placement: "outer"
			};
		
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleGuide_fn) !== 'function') {
		oCompany.getStyleGuide_fn = function(pTheme) {

			//Tick Marks
			let colorItem = 'red';

			if( pTheme === 'dark' ){
				colorItem = 'red';
			}
			return { 
				'line-color': colorItem,
				'line-width': 1,
				'line-style': "solid",
				'background-color': "#f0f0f5 #e0e0eb"
			};
		};
	}
	// -------------------------
	if (typeof(oCompany.getConfig_fn) !== 'function') {
		oCompany.getConfig_fn = function(pValues) {
			//
			//ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "b55b025e438fa8a98e32482b5f768ff5"];
			//
			oCompany.theme = 'light';
			if( typeof(oCompany.options.themeChoice) !== 'undefined' ) {
				oCompany.theme = oCompany.options.themeChoice;
			}
			let theme = oCompany.theme;
			if( oCompany.exists('#themeChoice') ) {
				theme = jQuery( '#themeChoice' ).val();
			}
			//
			return {
				"theme": theme,
				"type": "radar",
				"series": [{
					"values": pValues
				}],
				"scale-k": {
					"labels": [ 'Consomation éléctrique', 'Chauffage', 'Matières premières', 'Mobilité', 'Alimentation', 'Déchets' ],
					item: oCompany.getStyleItem_fn(theme),
					tick: oCompany.getStyleTick_fn(theme),
					guide: oCompany.getStyleGuide_fn(theme)
				},
				"scale-v": {
					"values": "0:10:2", //To set your min/max/step.
					"aspect": "circle",
    				//"format": "%v%"
				}
			};
		};
	}
	// -------------------------
	if (typeof(oCompany.setGraph_fn) !== 'function') {
		oCompany.setGraph_fn = function(pId, pValues) {
			//
			if(typeof(pId) === 'undefined') { var pId = 'oneGraph'; }
			if(typeof(pValues) === 'undefined') { var pValues = oCompany.getValues_fn(); }
			//
			var myConfig = oCompany.getConfig_fn(pValues);
			//
			if(oCompany.zingChartExists){
				zingchart.render({
					id: pId,
					height: '100%',
					width: '100%',
					output: "svg", // "svg"
					title: {
					  text: 'A Simple Bar Chart',
					  fontSize: '24px'
					},
					legend: {},
					data: myConfig
				});
			}
			// -------------------------
		};
	}
	// -------------------------
		
	
	
	// -----------------------------
	// ONLY SETTINGS PAGE GRAPHS
	// -----------------------------
	oCompany.zingChartExists = (typeof(zingchart) === 'object');
	// -------------------------
	// THEME
	// -------------------------
	if (typeof(oCompany.getValues_fn) !== 'function') {
		oCompany.getValues_fn = function(pTheme) {
			let values = [3, 5, 8, 4, 2, 6];
				
			return values;
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleItem_fn) !== 'function') {
		oCompany.getStyleItem_fn = function(pTheme) {
			
			//To style your scale labels.
			let colorItem = 'black';

			if( pTheme === 'dark' ){
				colorItem = 'white';
			}
			return { 
				'font-color': colorItem,
				'font-family': "Georgia",
				'font-size': 12,
				'font-weight': "bold",
				'font-style': "italic"
			};
		
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleTick_fn) !== 'function') {
		oCompany.getStyleTick_fn = function(pTheme) {

			//Tick Marks
			let colorItem = 'orange';

			if( pTheme === 'dark' ){
				colorItem = 'orange';
			}
			return { 
				'line-color': colorItem,
				'line-width': 3,
				size: 15,
				placement: "outer"
			};
		
		};
	}
	// -------------------------
	if (typeof(oCompany.getStyleGuide_fn) !== 'function') {
		oCompany.getStyleGuide_fn = function(pTheme) {

			//Tick Marks
			let colorItem = 'red';

			if( pTheme === 'dark' ){
				colorItem = 'red';
			}
			return { 
				'line-color': colorItem,
				'line-width': 1,
				'line-style': "solid",
				'background-color': "#f0f0f5 #e0e0eb"
			};
		};
	}
	// -------------------------
	if (typeof(oCompany.getConfig_fn) !== 'function') {
		oCompany.getConfig_fn = function() {
			//
			//ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "b55b025e438fa8a98e32482b5f768ff5"];
			//
			let theme = 'light';
			if( oCompany.exists('#themeChoice') ) {
				theme = jQuery( '#themeChoice' ).val();
			}
			//
			return {
				"theme": theme,
				"type": "radar",
				"series": [{
					"values": oCompany.getValues_fn()
				}],
				"scale-k": {
					"labels": [ 'Consomation éléctrique', 'Chauffage', 'Matières premières', 'Mobilité', 'Alimentation', 'Déchets' ],
					item: oCompany.getStyleItem_fn(theme),
					tick: oCompany.getStyleTick_fn(theme),
					guide: oCompany.getStyleGuide_fn(theme)
				},
				"scale-v": {
					"values": "0:10:2", //To set your min/max/step.
					"aspect": "circle",
    				//"format": "%v%"
				}
			};
		};
	}
	// -------------------------
	if (typeof(oCompany.setGraph_fn) !== 'function') {
		oCompany.setGraph_fn = function() {

			var myConfig = oCompany.getConfig_fn();
			//
			if(oCompany.zingChartExists){
				zingchart.render({
					id: 'oneGraph',
					height: '100%',
					width: '100%',
					output: "svg",
					title: {
					  text: 'A Simple Bar Chart',
					  fontSize: '24px'
					},
					legend: {},
					data: myConfig
				});
			}
			// -------------------------
		};
	}
	// -------------------------
		
	// -----------------------------
})(jQuery);