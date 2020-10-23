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
 * @filesource assets/plugins/Entreprise/assets/scripts/companyForm.js
 * @version 0.0.1
 * @created 2020-10-10
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-10
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
*/  
//----------------------------------------------------------
//	OBJECT
//----------------------------------------------------------
if (typeof(oCompanyForm) === undefined) { var oCompanyForm = {}; }
// ---------------------------------
(function() {
	"use strict";
	//
	var oCF = oCompanyForm;
	oCF.Form = {};
	// -----------------------------
    // TOOLS
    // ---------------------------------
    if (typeof(oCF.exists) === 'undefined') {
        oCF.exists = function(pSelector) {
            return (jQuery(pSelector).length > 0);
    	};
    }
	// -----------------------------
	// METHODS
	// -----------------------------
	if (typeof(oCF.uniqid) !== 'function') {
		oCF.uniqid = function(a = "", b = false) {
			const c = Date.now()/1000;
			let d = c.toString(16).split(".").join("");
			while(d.length < 14) { d += "0";}
				let e = "";
			if(b){
				e = ".";
				e += Math.round(Math.random()*100000000);
			}
			return a + d + e;
		};
	}
	// -----------------------------
	if (typeof(oCF.getMessageCallback_fn) !== 'function') {
		oCF.getMessageCallback_fn = function(response) {
			console.log( "response", response );
			//
			if(typeof(response.success) !== 'undefined'){
				const theDatas = response.data;
				// graphSeries
				// points points.total points.points
				// table
				jQuery('article.company .entry-content .wpcf7').html(theDatas.table);
				jQuery('article.company .entry-content').append('<div id="oneGraph" style="height: 400px; width: 600px; overflow: hidden;"></div><br><button id="oneGraphSave">' + oCF.lang.saveImage + '</button><div id="output_image" style=""></div>');
				//
				const series = theDatas.graphSeries.split(',');
				oCompany.setGraph_fn('oneGraph', series);
			}
		};
	}
	// ------------------------------------------
	// AJAX CALLBACKS
	// ------------------------------------------
	if (typeof(oCF.getMessage_fn) !== 'function') {
		oCF.getMessage_fn = function() {
			jQuery.ajax({
				url: oCF.ajaxUrl,
				type: "POST",
				data: {
					'action': 'getMessage',
					'uniqid': oCF.Form.uniqId 
				},
				dataType: 'JSON',
				success:function(response){
					oCF.getMessageCallback_fn(response);
				},
				error: function(errorThrown){
					//alert('error');
					console.log(errorThrown);
				}
			}).done(function(response) {
				//oCF.getMessageCallback_fn(response);
			});
		};
	}
	// -----------------------------
	// ONLY FORM PAGE
	//
	if( ! oCF.isAdmin){
		//
		oCF.Form.obj = jQuery( '.wpcf7' );
		//
		oCF.Form.target = document.querySelector( '.wpcf7' );
	
		//
		oCF.Form.unitTag = (typeof(oCF.Form.obj).prop('id') !== 'undefined') ? oCF.Form.obj.prop('id'): '';
		// 
		oCF.Form.uniqId = oCF.uniqid();
		//
		//console.log('oCF.Form', oCF.Form);
		// ------------------------------------------
		// UNIQUE_ID
		if( oCF.exists('#UNIQUE_ID') ){
			jQuery('#UNIQUE_ID').val( oCF.Form.uniqId );
		}
		// ------------------------------------------
		// AJAX SEND
		// ------------------------------------------
		jQuery( oCF.Form.obj ).on( 'wpcf7mailsent', function( event ) {
			//const formID = event.detail.contactFormId;
			//const postID = event.detail.containerPostId;
			//const inputs = event.detail.inputs;
			//console.log('mail sent OK', event.detail );
			oCompanyForm.getMessage_fn();
		} );
		// ------------------------------------------
	} else {
		//$( '.wpcf7-submit', $form ).after( '<span class="ajax-loader"></span>' );
	}
	// -----------------------------
})(jQuery);