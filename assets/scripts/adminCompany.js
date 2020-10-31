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
							const newShortcode = jQuery('#toCopy').val();
							
							//const oldContent = jQuery('#content').html();
							jQuery('#content').html('');
							jQuery('#content').html(newShortcode);
							//wp.media.editor.insert(newShortcode);

						});
					}
				};
			}
			// -----------------------------
			if( oCompany.exists('#formChoice') ) {
				jQuery( '#formChoice' ).on('change', function( event ){
					event.preventDefault();

					const newForm = jQuery(this).val();
					const newName = jQuery(this).find('option:selected').text();
					const newShortcode = '[contact-form-7 id="' + newForm + '" title="' + newName.trim() + '"]';
					//
					if( oCompany.exists('#toCopy') ) {
						jQuery('#toCopy').val(newShortcode);
						oCompany.copyShortcode_fn();
					}
				});
			}
			// -----------------------------
			if( oCompany.exists('#templateSelect') ) {
				jQuery( '#templateSelect' ).on('change', function( event ){
					event.preventDefault();
					// 
					const newTemplate = jQuery(this).val();
					//
					if( oCompany.exists('#templateField') ) {
						jQuery('#templateField').val(newTemplate);
						console.log('newTemplate', jQuery('#newTemplate').val() );
					}
				});
			}
			// -----------------------------
			
		}
	}
	
	// -----------------------------
})(jQuery);