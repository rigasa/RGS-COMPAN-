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
	// -----------------------------
	// ONLY POST COMPANY
	// -----------------------------
	if(oCompany.isAdmin){
		const isCustomPost = oCompany.getQueryStringParams_fn( 'post=', window.location.href );

		if(isCustomPost !== '' ){
			
			// -----------------------------
			if (typeof(oCompany.copyShortcode_fn) !== 'function') {
				oCompany.copyShortcode_fn = function()
				{
					if( oCompany.exists('#copy') ) {
						jQuery( '#copy' ).on('click', function( event ){
							event.preventDefault();

							const copyText = document.querySelector("#toCopy");

							copyText.select();
							copyText.setSelectionRange(0, 99999); // used for mobile phone
							document.execCommand("copy");
							console.log( `${copyText.value} is copied` );
						});
					}
				};
			}
			// -----------------------------
			oCompany.copyShortcode_fn();
			// -----------------------------
			if( oCompany.exists('#formChoice') ) {
				jQuery( '#formChoice' ).on('change', function( event ){
					event.preventDefault();

					const newForm = jQuery(this).val();
					const newName = jQuery(this).find('option:selected').text();
					const newShortcode = '[contact-form-7 id="' + newForm + '" title="' + newName.trim() + '"]';
					
					if( oCompany.exists('#toCopy') ) {
						jQuery('#toCopy').val(newShortcode);
						oCompany.copyShortcode_fn();
						console.log('newShortcode', jQuery('#toCopy').val() );
					}
				});
			}
			// -----------------------------
			
		}
	}
	// -----------------------------
})(jQuery);