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
//if (typeof(oCompanySingle) === undefined) { var oCompanySingle = {}; }
//if (typeof(zingchart) === undefined) { var zingchart = {}; }
// ---------------------------------
(function() {
	"use strict";
	//
	//if (typeof(oCompanyF) === undefined) { var oCompany = {}; }
	//if (typeof(oCompanySingle) === undefined) { var oCompanySingle = {}; }
	//if (typeof(zingchart) === undefined) { var zingchart = {}; }
	const theOptions = oCompanySingle.options;
	// -----------------------------
	// ONLY TAB COMPANIES STATS PAGE
	// -----------------------------
	oCompanySingle.theme = 'light';
	if( typeof(theOptions.themeChoice) !== 'undefined' ) {
		oCompanySingle.theme = theOptions.themeChoice;
	}
	// -----------------------------
	// GRAPH BY SECTIONS
	// -----------------------------
	if (typeof(oCompanySingle.checkMedia_fn) !== 'function') {
        oCompanySingle.checkMedia_fn = function(pSelector) {
			
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
					oCompanySingle.checkMedia_fn(pSelector);
				});
				
			}
		};
	}
	// -----------------------------
	if (typeof(oCompanySingle.createMedia_fn) !== 'function') {
        oCompanySingle.createMedia_fn = function(pSelector, pTarget) {
            if(oCompany.zingChartExists){
				zingchart.exec( pSelector, 'getimagedata', {
					filetype : 'png',
					callback : function(imagedata) {
						//console.log(imagedata);
						if( oCompany.exists('#' + pTarget) ) {
							jQuery('#' + pTarget).html('<img src="' + imagedata + '" alt="" />');
							oCompanySingle.checkMedia_fn( pSelector );
						}
					}
				});
			//
			}
    	};
    }
	//------------------------------------
	// CHANGE COMPANY
	// --------nbInquest
	if( oCompany.exists('#companiesList') ) {
		
		jQuery( '#companiesList' ).on('change', function( event ){
			event.preventDefault();
			//
			const companyID = jQuery( '#companiesList' ).val();
			//console.log('companyID', companyID );
			
			jQuery.ajax({
				url: oCompanySingle.ajaxUrl,
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
							var nbInquest;
							const code = (typeof(data.code) !== 'undefined') ? data.code : 2;
							var newHtml = (typeof(data.content) !== 'undefined') ? data.content : '';
							const hideCharts = ( code > 0 );
							if( code > 0 ){
								newHtml = '<p class="noDataFound">' + newHtml + '</p>';
								nbInquest = 0;
							} else {
								seriesSections = (typeof(data.seriesSections) !== 'undefined') ? data.seriesSections : '';
								seriesQuestions = (typeof(data.seriesQuestions['points']) !== 'undefined') ? data.seriesQuestions['points'] : '';
								
								nbInquest = (typeof(data.nbInquest) !== 'undefined') ? parseInt( nbInquest ) : 0;
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
									   oCompanySingle.setSections_fn( seriesSections );
									}
									if(seriesQuestions !== '' ){
									   oCompanySingle.setPoints_fn(seriesQuestions);
									}
									//
								   jQuery('#chartByPoints, #chartBySections').fadeIn( 400 );
								}
								//
								jQuery('#architectContainer').html(newHtml).fadeIn( 400 );
								//
								jQuery('#nbInquest').html(nbInquest);
								//
							});
							//----------------------
						}
					 }
					//
				},
				error: function(errorThrown){
					//alert('error');
					console.log(errorThrown);
				}
			}).done(function(response) {
				//oCF.getInquestCallback_fn(response);
			});
			
			//
		});
	}
	// -----------------------------
	// Points
	// -----------------------------
	if (typeof(oCompanySingle.setPoints_fn) !== 'function') {
        oCompanySingle.setPoints_fn = function( points ) {
			//
			if( oCompany.exists('#pointsChart') ) {
				
				const theLang = oCompanySingle.lang;
				const theColors = oCompanySingle.questionsColors;
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
					"theme": oCompanySingle.theme,
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
	oCompanySingle.setPoints_fn( oCompanySingle.points );
	// -----------------------------
	// RADAR
	// -----------------------------
	if( oCompany.exists('#displayAllInRadar') ) {
		jQuery( '#displayAllInRadar' ).on('change', function(){
			const allSeries = theOptions.allSeries;

		});
	}
	// -----------------------------
	if (typeof(oCompanySingle.setSections_fn) !== 'function') {
        oCompanySingle.setSections_fn = function( series ) {
			//
			if( oCompany.exists('#sectionsGraph') ) {
				//
				oCompany.setGraph_fn('sectionsGraph', series );
				//
				oCompanySingle.checkMedia_fn('sectionsGraph');
				// -------------------------
				jQuery( '#sectionsGraph-save' ).on('click', function( event ){
					event.preventDefault();
					//
					oCompanySingle.createMedia_fn('sectionsGraph', 'sectionsGraph-photo' );
					//
				});
			}
			//
		};
	}
	// -----------------------------
	// PDF
	// -----------------------------
	if( oCompany.exists('#createPDF') ) {
		jQuery( '#createPDF' ).on('click', function( event ){
			event.preventDefault();
			const dataHTML = document.getElementById('architectPDF').innerHTML;
			//console.log('craete pdf', dataHTML);

			jQuery.ajax({
				url: oCompanySingle.ajaxUrl,
				type: "POST",
				data: {
					'action': 'createPDF',
					//'pageHTML': encodeURIComponent( dataHTML )
					//'pageHTML': JSON.stringify( dataHTML )
					'pageHTML': dataHTML
				},
				dataType: 'JSON',
				success:function(response){
					//
					const success = (typeof(response.success) !== 'undefined') ? response.success : false;
					if(success){
					 	const data = (typeof(response.data) !== 'undefined') ? response.data : false;
						//
						if(data){
							
						}
					 }
					//
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		});
	}
	//
	oCompanySingle.setSections_fn( oCompanySingle.series );
	// -----------------------------
})(jQuery);