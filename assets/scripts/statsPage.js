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
 * @fullname Eco Citoyen Management
 * @package RGS_Company
 * @category Core
 * @filesource assets/plugins/Entreprise/assets/scripts/statsPage.js
 * @version 0.0.1
 * @created 2020-10-21
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-21
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
*/  
//----------------------------------------------------------
//	OBJECT
//----------------------------------------------------------
//if (typeof(oCompanyF) === undefined) { var oCompany = {}; }
//if (typeof(oCompanyForm) === undefined) { var oCompanyForm = {}; }
//if (typeof(zingchart) === undefined) { var zingchart = {}; }
// ---------------------------------
(function() {
	"use strict";
	//
	//if (typeof(oCompanyF) === undefined) { var oCompany = {}; }
	//if (typeof(oCompanyForm) === undefined) { var oCompanyForm = {}; }
	//if (typeof(zingchart) === undefined) { var zingchart = {}; }
	const theOptions = oCompanyForm.options;
	// -----------------------------
	// ONLY TAB COMPANIES STATS PAGE
	// -----------------------------
	oCompanyForm.theme = 'light';
	if( typeof(theOptions.themeChoice) !== 'undefined' ) {
		oCompanyForm.theme = theOptions.themeChoice;
	}
	// -----------------------------
	// GRAPH BY SECTIONS
	// -----------------------------
	if (typeof(oCompanyForm.checkMedia_fn) !== 'function') {
        oCompanyForm.checkMedia_fn = function(pSelector) {
			
			if(jQuery('#' + pSelector + '-photo').html() === '' ){
				// Hide Button
				jQuery( '#' + pSelector + '-clear' ).hide();
			} else {
				// Show Button
				jQuery( '#' + pSelector + '-clear' ).show();
				// Add Event Clear Image
				jQuery( '#' + pSelector + '-clear' ).on('click', function( event ){
					event.preventDefault();
					// Clear Image
					jQuery('#' + pSelector + '-photo').html('');
					// Hide Button
					oCompanyForm.checkMedia_fn(pSelector);
				});
				
			}
		};
	}
	// -----------------------------
	if (typeof(oCompanyForm.createMedia_fn) !== 'function') {
        oCompanyForm.createMedia_fn = function(pSelector, pTarget) {
            if(oCompany.zingChartExists){
				zingchart.exec( pSelector, 'getimagedata', {
					filetype : 'png',
					callback : function(imagedata) {
						//console.log(imagedata);
						if( oCompany.exists('#' + pTarget) ) {
							jQuery('#' + pTarget).html('<img src="' + imagedata + '" alt="" />');
							oCompanyForm.checkMedia_fn( pSelector );
						}
					}
				});
			//
			}
    	};
    }
	//------------------------------------
	// CHANGE COMPANY
	// -----------------------------
	if( oCompany.exists('#companiesList') ) {
		
		jQuery( '#companiesList' ).on('change', function( event ){
			event.preventDefault();
			//
			const companyID = jQuery( '#companiesList' ).val();
			console.log('companyID', companyID );
			
			jQuery.ajax({
				url: oCompanyForm.ajaxUrl,
				type: "POST",
				data: {
					'action': 'getStatsList',
					'companyID': companyID 
				},
				dataType: 'JSON',
				success:function(response){
					//
					const success = (typeof(response.success) !== 'undefined') ? response.success : false;
					if(success){
					 	const data = (typeof(response.data) !== 'undefined') ? response.data : false;
						//
						if(data){
							//--------------------------
							var seriesSections;
							var seriesQuestions;
							const code = (typeof(data.code) !== 'undefined') ? data.code : 2;
							var newHtml = (typeof(data.content) !== 'undefined') ? data.content : '';
							const hideCharts = ( code > 0 );
							if( code > 0 ){
								newHtml = '<p class="noDataFound">' + newHtml + '</p>';
							} else {
								seriesSections = (typeof(data.seriesSections) !== 'undefined') ? data.seriesSections : '';
								seriesQuestions = (typeof(data.seriesQuestions['points']) !== 'undefined') ? data.seriesQuestions['points'] : '';
							}
							//--------------------------
							//console.log( 'newHtml', newHtml);
							jQuery('#architectContainer').fadeOut( 400, function(){
								//
								if(hideCharts){
									jQuery('#chartByPoints, #chartBySections').hide();
								} else {
									//
									if(seriesSections !== '' ){
									   oCompanyForm.setSections_fn( seriesSections );
									}
									if(seriesQuestions !== '' ){
									   oCompanyForm.setPoints_fn(seriesQuestions);
									}
									//
								   jQuery('#chartByPoints, #chartBySections').fadeIn( 400 );
								}
								//
								jQuery('#architectContainer').html(newHtml).fadeIn( 400 );
							});
							//----------------------
						}
						
					 }
					console.log( 'response', response);
					
					// architectContainer
					//oCF.getMessageCallback_fn(response);
				},
				error: function(errorThrown){
					//alert('error');
					console.log(errorThrown);
				}
			}).done(function(response) {
				//oCF.getMessageCallback_fn(response);
			});
			
			//
		});
	}
	// -----------------------------
	// Points
	//------------------------------------
	if (typeof(oCompanyForm.setPoints_fn) !== 'function') {
        oCompanyForm.setPoints_fn = function( points ) {
			//
			if( oCompany.exists('#pointsChart') ) {
				
				const theLang = oCompanyForm.lang;
				const theColors = oCompanyForm.questionsColors;
				const theLabels = theLang.chartLabels;
				const theSeries = new Array();
				
				for( var i = 0; i < theColors.length; i++ ){
					theSeries.push(
						{
							values: [points[i]],
							text: theLabels[i],
							backgroundColor: theColors[i]
						}
					);
				}
				//
				var chartType = 'bar';// 'bar', 'pie'
				var tooltip = {};
				if(chartType === 'pie '){
				   tooltip = {
					   // turn individual point tooltip off
						// visible: false,
						padding: '10 15',
						borderRadius: 3,
						// % symbol represents a token to insert a value. Full list here:
						// https://www.zingchart.com/docs/tutorials/chart-elements/zingchart-tokens/
						text: '%plot-text %kl was %v',
						htmlMode: true
				   };
				} else {

				}
				//
				const myConfig = {
					type: chartType, 
					"theme": oCompanyForm.theme,
					globals: {
					  fontSize: 12
					},
					title: {
					  text: theLang.chartByPoints,
					  fontSize: 24,
					  color: '#5d7d9a'
					},
					plotarea: {
						//adjustLayout: true
					},
					legend: {
					  draggable: true,
					},
					plot: {
						valueBox: {
							placement: 'top-in',
							color: '#fff'
						},
						tooltip: tooltip,
						animation: {
							effect: 'ANIMATION_EXPAND_BOTTOM',
							method: 'ANIMATION_STRONG_EASE_OUT',
							sequence: 'ANIMATION_BY_NODE',
							speed: 800,
						}
					},
					scaleX: {
						//values: '0:100:10',
						item: {
						  fontSize: 10
						},
						label: {
							text: theLang.pointsbyQ
						},
						labels: [theLang.questions]
					},
					scaleY: {
						//values: '0:100:2',
						item: {
						  fontSize: 10
						},
					  label: {
						text: theLang.points
					  }
					},
					series: theSeries
				};

				zingchart.render({
				  id: 'pointsChart',
				  data: myConfig,
				  height: "100%",
				  width: "100%"
				});
			}
		};
	}
	//
	oCompanyForm.setPoints_fn( oCompanyForm.points );
	//-------------------------
	// RADAR
	//-------------------------
	if( oCompany.exists('#displayAllInRadar') ) {
		jQuery( '#displayAllInRadar' ).on('change', function(){
			const allSeries = theOptions.allSeries;

		});
	}
	// -----------------------------
	if (typeof(oCompanyForm.setSections_fn) !== 'function') {
        oCompanyForm.setSections_fn = function( series ) {
			//
			if( oCompany.exists('#sectionsGraph') ) {
				//
				oCompany.setGraph_fn('sectionsGraph', series );
				//
				oCompanyForm.checkMedia_fn('sectionsGraph');
				// -------------------------
				jQuery( '#sectionsGraph-save' ).on('click', function( event ){
					event.preventDefault();
					//
					oCompanyForm.createMedia_fn('sectionsGraph', 'sectionsGraph-photo' );
					//
				});
			}
			//
		};
	}
	//
	oCompanyForm.setSections_fn( oCompanyForm.series );
	// -----------------------------
})(jQuery);