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
 * @filesource assets/plugins/Entreprise/assets/scripts/companySingle.js
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
if (typeof(oCompanySingle) === undefined) { var oCompanySingle = {}; }
// ---------------------------------
(function() {
	"use strict";
	//
	var oCF = oCompanySingle;
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
	if (typeof(oCF.getInquestCallback_fn) !== 'function') {
		oCF.getInquestCallback_fn = function(response) {
			console.log( "response", response );
			//
			if(typeof(response.success) !== 'undefined'){
				const theDatas = response.data;
				// graphSeries
				// points points.total points.points
				// table
				const HTML = '';
				jQuery('article.company .entry-content .wpcf7').html('<div id="company-single-response">'+theDatas.table+'</div>');
				jQuery('article.company .entry-content').append('<div id="oneGraph" style="height: 400px; width: 600px; overflow: hidden;"></div><br><button id="oneGraphSave">' + oCF.lang.saveImage + '</button><div id="output_image" style=""></div><br><br>');
				//
				const series = theDatas.graphSeries.split(',');
				oCompany.setGraph_fn('oneGraph', series);
				// Set NbInquests
				oCF.incrementInquests_fn();
			}
		};
	}
	// ------------------------------------------
	// AJAX CALLBACKS
	// ------------------------------------------
	if (typeof(oCF.getInquest_fn) !== 'function') {
		oCF.getInquest_fn = function() {
			jQuery.ajax({
				url: oCF.ajaxUrl,
				type: "POST",
				data: {
					'action': 'getInquest',
					'uniqid': oCF.Form.uniqId 
				},
				dataType: 'JSON',
				success:function(response){
					oCF.getInquestCallback_fn(response);
				},
				error: function(errorThrown){
					//alert('error');
					console.log(errorThrown);
				}
			}).done(function(response) {
				//oCF.getInquestCallback_fn(response);
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
			oCompanySingle.getInquest_fn();
		} );
		// ------------------------------------------
		// SET CAMPAIGN ID
		// ------------------------------------------
		if( oCF.exists('#campaignItem') ){
			var campaignID = jQuery('#campaignItem').data('campaign');
			if(typeof(campaignID) === 'undefined'){
				campaignID = 0;
			}
			if( oCF.exists('#CAMPAIGN_ID') ){
				jQuery('#CAMPAIGN_ID').val( campaignID );
			}
		}
		//-------------
		if (typeof(oCF.incrementInquests_fn) !== 'function') {
			oCF.incrementInquests_fn = function() {
				// Set NbInquests
				var nbInquests = parseInt( jQuery('#nbOfInquests').text() );
				var maxInquests = parseInt( jQuery('#nbMaxInquests').text() );

				nbInquests++;

				if(nbInquests >= maxInquests){
					var warning = '<p class="warning">'+ oCF.lang.maxInquests+' : ' + maxInquests + '</p>';
					jQuery('#campaigns-list').hide();
					jQuery('.wpcf7-form').hide();
					jQuery('article.type-company').append( warning );
					//
					nbInquests = maxInquests;
				}
				jQuery('#nbOfInquests').text( nbInquests );
			};
		}
		// ------------------------------------------
	} else {
		//$( '.wpcf7-submit', $form ).after( '<span class="ajax-loader"></span>' );
	}
	// -----------------------------
})(jQuery);