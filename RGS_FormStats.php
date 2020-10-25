<?php
/*
 ____   _   ____         ____          
|  _ \ (_) / ___|  __ _ / ___|   __ _  
| |_) || || |  _  / _` |\___ \  / _` | 
|  _ < | || |_| || (_| | ___) || (_| | 
|_| \_\|_| \____| \__,_||____/  \__,_|                                          
*/
//-----------------------------------------------------------------------
/**
 * @class RGS_FormStats
 * @fullname Eco Citoyen Management
 * @package RGS_FormStats
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_FormStats.php
 * @version 0.0.1
 * @created 2020-10-07
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-07
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
 * pChart http://pchart.sourceforge.net/screenshots.php
*/                                                                                 

//--------------------------------------
if ( ! defined( 'ABSPATH' ) ) exit; // SECURITY : Exit if accessed directly
//--------------------------------------
if( ! class_exists( 'RGS_FormStats' ) ):
	//----------------------------------
	class RGS_FormStats
	{
		//------------------------------
		//---------------------------------------------------------------
		private static $instance; // THE only instance of the class
		//---------------------------------------------------------------
		/**
		 * Slug
		 *
		 * @var string
		 */
		static public $gSlug;
		//---
		static public $gFile;
		static public $gDir;
		static public $gUrl;
		//---
		static public $gBasename;
		// Plugin Hierarchy
		//---
		static public $gAdminPageId;
		//---
		const K_SLUG = 'rgsFormStats';
		const K_PREFIX = 'rgsFormStats-';
		const K_VERS = '0.0.1';
		const K_DBVERS = '0.0.1';
		
		//------------------------------
		public function __construct()
		{
			self::setupGlobals_fn();
			self::loadDependencies_fn();
			self::setupHooks_fn();
		}
		//---------------------------------------------------------------
		/**
		 * Return an instance of this class.
		 *
		 * @access public
		 *
		 * @return object A single instance of this class.
		 * @static
		 */
		public static function getInstance_fn() 
		{
			// If the single instance hasn't been set, set it now.
			if ( NULL == self::$instance ) :
				self::$instance = new self;
			endif;

			return self::$instance;
		}
		//---------------------------------------------------------------
		// Constructor Methods
		//---------------------------------------------------------------
		/**
		 * Sets some globals for the class
		 *
		 * @access private
		 */
		private function setupGlobals_fn() 
		{
			//---
			$this->_version        	= self::K_VERS;
			self::$gFile          	= __FILE__;
			self::$gDir     		= trailingslashit( dirname( self::$gFile ) );
	
			self::$gUrl				= trailingslashit( get_site_url() ) . str_replace( ABSPATH, '', self::$gDir );
			//---
			$lName 					= basename( self::$gDir );
			$gBasename 				= explode( $lName, self::$gFile );
			self::$gBasename       	= $lName . $gBasename[ 1 ];
			//
			// Directories Hierarchy
			//
			//---
			self::$gAdminPageId = null;
			//---
			//---
			self::$gSlug 			= sanitize_title( self::K_SLUG );
			//---
		}
		//---------------------------------------------------------------
		/**
		 * Load the required dependencies for this theme.
		 *
		 * Include the following files that make up the theme:
		 *
		 * @since    0.1.0
		 * @access   private
		 */
		private function loadDependencies_fn() 
		{
			//
		}
		//---------------------------------------------------------------
		private function setupHooks_fn() 
		{
			// SETUPS
			if ( is_admin() ) :
				//-----------------------------------------------
            	add_action( 'admin_init',     array( __CLASS__, 'initForm_fn' ) );
				//-----------------------------------------------
				// ADMIN MENU OPTIONS AND VIEWERS
				add_action( 'admin_menu', array( __CLASS__, 'adminMenu_fn' ) );
        	endif;
			if(is_admin()):
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
				//
			else:
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
			endif;
			//---------------------------------------------------
			//------
			// GET MESSAGE BY AJAX
			//------
			add_action( 'wp_ajax_getStatsList', array(__CLASS__, 'getStatsList_fn') );
			add_action( 'wp_ajax_nopriv_getStatsList', array(__CLASS__, 'getStatsList_fn') );
			//---------------------------------------------------
		}
		//---------------------------------------------------------------
		// CPT
		//---------------------------------------------------------------
		static function getSlug_fn()
		{
			return RGS_Company::getSlug_fn();
		}
		//---------------------------------------------------------------
		static function getTD_fn()
		{
			return RGS_Company::getTD_fn();
		}
		//---------------------------------------------------------------
		static function enqueueScripts_fn()
		{
			//------------------------------------------------------
			if( method_exists( 'RGS_Company', 'loadScripts_fn' ) ):
				RGS_Company::loadScripts_fn();
			endif;
			//------------------------------------------------------
			if( file_exists( RGS_Company::$gJsDir . 'companyForm.js' ) ):
				//
				wp_enqueue_script( 
					self::getSlug_fn() . '_companyForm', 
					RGS_Company::$gJsUrl . 'companyForm.js', 
					array( 'jquery' ), 
					'0.0.1', 
					true 
				);
				//-------------------------
				$localize = self::getLocalize_fn();
				$_args = apply_filters( self::getSlug_fn() .'_localize', $localize );
				$localize = array_replace_recursive( $localize, $_args );
				//
				wp_localize_script( 
					self::getSlug_fn() . '_companyForm', 
					'oCompanyForm', 
					$localize
				); 
			
			endif;
			//------------------------------------------------------
			$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'companies';
			if($activeTab == 'companies') :
				//
				if( file_exists( RGS_Company::$gJsDir . 'statsPage.js' ) ):
					wp_enqueue_script( 
						self::getSlug_fn() . '_statsPage', 
						RGS_Company::$gJsUrl . 'statsPage.js', 
						array( 'jquery' ), 
						'0.0.1', 
						true 
					);
				endif;
				//
			endif;
			//------------------------------------------------------
		}
		//---------------------------------------------------------------
		static function getLocalize_fn()
		{
			$SLUG = self::getSlug_fn();
			$TD = self::getTD_fn();
			$options = RGS_CompanySettings::getOption_fn();
			//---------------------------------------------------------
			$allInquest = RGS_FormStats::getAllInquest_fn();
			//
			$arrReturn = RGS_FormStats::getStructInquest_fn($allInquest);
			$arrSeries = (isset($arrReturn['series']) ) ? $arrReturn['series'] : array();
			//----
			$arrPoints = (isset($arrReturn['points']['points']) ) ? $arrReturn['points']['points'] : array();
			//---------------------------------------------------------
			$arrColors = RGS_CompanySettings::getQuestionsColors_fn();
			//---------------------------------------------------------
			$return = array( 
				'ajaxUrl' 			=> admin_url( 'admin-ajax.php' ),
				'nonce' 			=> wp_create_nonce( $SLUG . '-nonce' ),
				'isAdmin' 			=> is_admin()? true: false,
				'isFrontPage' 		=> is_front_page()? true: false,
				'slug' 				=> $SLUG,
				'pagePermalink' 	=> get_the_permalink(),
				'series' 			=> $arrSeries,
				'allSeries' 		=> $arrSeries,
				'points' 			=> $arrPoints,
				'questionsColors' 	=> $arrColors,
				'options' 			=> $options,
				'lang' 				=> array(
					'yes' 				=> __( 'Yes', $TD ),
					'saveImage'			=> __( 'Save Image', $TD ),
					'chartByPoints'		=> __( 'Chart by Points', $TD ),
					'pointsbyQ'			=> __( 'Points per question', $TD ),
					'questions'			=> __( 'Questions', $TD ),
					'points'			=> __( 'Points', $TD ),
					'chartLabels' 		=> array(
						__( 'Lighting', $TD ), //"Eclairage", 
						__( 'Equipment', $TD ), //"Matériel", 
						__( 'Temperature', $TD ), //"Température", 
						__( 'Duration', $TD ), //"Durée", 
						__( 'Printer', $TD ), //"Imprimante", 
						__( 'Movement', $TD ), //"Déplacement", 
						__( 'Distance', $TD ), //"Distance", 
						__( 'Teleworking', $TD ), //"Télétravail", 
						__( 'Drink', $TD ), //"Boire", 
						__( 'Eat', $TD ), //"Manger", 
						__( 'Production', $TD ), //"Production", 
						__( 'Solutions', $TD ) //"Solutions"
					)

				)
			);
			//---------------------------------------------------------
			// Clear method
			unset($SLUG);
			unset($TD);
			unset($options);
			unset($allInquest);
			unset($arrReturn);
			unset($arrSeries);
			unset($arrPoints);
			unset($arrColors);
			//---------------------------------------------------------
			return $return;
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		static function initForm_fn()
		{
			
		}
		//---------------------------------------------------------------
		// TEMPLATES
		//---------------------------------------------------------------
		static function includeTemplate_fn()
		{
			/*$file = RGS_Company::$gTemplatesDir .'companyForm.php';
			if( is_file( $file ) ):
				include_once( $file  );
			endif;*/
		}
		//---------------------------------------------------------------
		// MENU
		//---------------------------------------------------------------
		static function getStatsMenuId_fn()
		{
			return self::getSlug_fn() . '-stats';
		}
		//--------------------------------------------------
		// MENU
		//--------------------------------------------------
		static function adminStatsPage_fn($pTab = 'companies')
		{
			// check user capabilities
    		if ( ! current_user_can( 'manage_options' ) ) :
        		return;
    		endif;
			
			if( is_file( RGS_Company::$gViewsDir . 'statsPage.php' ) ):
				include_once(RGS_Company::$gViewsDir . 'statsPage.php');
			else:
				echo '<h1>';
				esc_html_e( 'No Statistics Page Loaded!', self::getTD_fn() ); 
				echo '</h1>';
			endif;
			
		}
		//--------------------------------------------------
		static function adminAddHelpTab_fn()
		{
			$screen = get_current_screen();
			if ( $screen->id != self::$gAdminPageId ) { return; }
			//
			$screen->add_help_tab( array(
				'id' => self::getSlug_fn() . '_helpTab',
				'title' => __('Statistics Help Tab', self::getTD_fn()),
				'content' => '<p>'
				. __( 'Descriptive content that will show in Statistics Help Tab body goes here.', self::getTD_fn() )
				. '</p>',
			) );
			// Load settings page scripts
			if(is_admin()):
				//add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueSettingsScripts_fn'), 11 );
        	endif;
		}
		//---------------------------------------------------------------
		static function adminMenu_fn()
		{
			self::$gAdminPageId = add_menu_page(
				__( 'EC Statistics', self::getTD_fn() ),
				__( 'Statistics', self::getTD_fn() ),
				'manage_options',
				self::getStatsMenuId_fn(),
				array( __CLASS__, 'adminStatsPage_fn' ),
				'dashicons-chart-line',
				17
			);
			//
			// add_action( 'load-' . self::$gAdminPageId, array( __CLASS__,'load_admin_js' ) );
			add_action( 'admin_print_styles-' . self::$gAdminPageId, array( __CLASS__, 'adminEnqueueStyles_fn' ) );
			
			// Adds my_help_tab when my_admin_page loads
    		#add_action( 'load-' . self::$gAdminPageId, array( __CLASS__, 'adminAddHelpTab_fn' ) );
			
		}
		//---------------------------------------------------------------
		static function adminEnqueueStyles_fn()
		{
			
			if( file_exists( RGS_Company::$gCssDir  . 'formStats.css' ) ):
				
				wp_enqueue_style( 
					self::getSlug_fn() . '_formStats', 
					RGS_Company::$gCssUrl . 'formStats.css', 
					array( ), 
					'0.0.1', 
					'all' 
				);
			endif;
			
		}
		//---------------------------------------------------------------
		// TOOLS
		//---------------------------------------------------------------
		static function getCompaniesList_fn()
		{
			$args = array(
				'post_type' => RGS_Company::getSlug_fn(), 
				'posts_per_page' => -1
			);
			$rs = array();
			if( $data = get_posts($args)):
				foreach($data as $key) :
					$rs[$key->ID] = $key->post_title;
				endforeach;
				return $rs;
			else:
				$rs['0'] = esc_html__('No Companies found', self::getTD_fn());
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getCompanyInquest_fn( $companyId = 0 )
		{
			$args = array(
				'post_type' 		=> RGS_CompanyInquest::getCptName_fn(), 
				'posts_per_page' 	=> -1,
				'post_status' 		=> 'draft',
				'meta_key' 			=> 'COMPANY_ID',
				'meta_value' 		=> $companyId
			);
			
			$rs = array();
			$msgList = get_posts( $args );
			//
			if( $msgList ):
				foreach($msgList as $key) :
					$rs[] = self::getDisplayStats_fn( $key );
				endforeach;
				return $rs;
			else:
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getAllInquest_fn()
		{
			$args = array(
				'post_type' 		=> RGS_CompanyInquest::getCptName_fn(),
				'posts_per_page' 	=> -1,
				'post_status' 		=> 'draft',
			);
			
			$rs = array();
			$msgList = get_posts( $args );
			//
			if( $msgList):
				foreach($msgList as $key) :
					$rs[] = self::getDisplayStats_fn( $key );
				endforeach;
				return $rs;
			else:
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getPrivateList_fn()
		{
			$args = array(
				'post_type' 	=> RGS_CompanyInquest::getCptName_fn(),
				'post_status' 	=> 'draft',
				'posts_per_page' => -1,
				'meta_key' 		=> 'private',
				'meta_value' 	=> 'yes'
			);
			
			$rs = array();
			if( $data = get_posts($args)):
				foreach($data as $key) :
					$rs[$key->ID] = $key->post_title;
				endforeach;
				return $rs;
			else:
				$rs['0'] = esc_html__('No Companies found', self::getTD_fn());
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getPublicList_fn()
		{
			$args = array(
				'post_type' 	=> RGS_CompanyInquest::getCptName_fn(),
				'post_status' 	=> 'draft',
				'posts_per_page' => -1,
				'meta_key' 		=> 'private',
				'meta_value' 	=> 'no'
			);
			
			$rs = array();
			if( $data = get_posts($args)):
				foreach($data as $key) :
					$rs[$key->ID] = $key->post_title;
				endforeach;
				return $rs;
			else:
				$rs['0'] = esc_html__('No Public found', self::getTD_fn());
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getTagsList_fn( $postID )
		{
			return get_post_meta( $postID, 'tags', TRUE );
		}
		//---------------------------------------------------------------
		static function getResultsList_fn( $postID )
		{
			return get_post_meta( $postID, 'results', TRUE );
		}
		//---------------------------------------------------------------
		static function getPointsList_fn( $postID )
		{
			return get_post_meta( $postID, 'points', TRUE );
		}
		//---------------------------------------------------------------
		static function getGraphSeriesList_fn( $postID )
		{
			return get_post_meta( $postID, 'graphSeries', TRUE );
		}
		//---------------------------------------------------------------
		static function getDisplayStats_fn( $post )
		{
			
			if(! $post ):
				return null;
			endif;
			//
			$arrDisplay = array();
			//
			$arrDisplay['tags'] = self::getTagsList_fn( $post->ID );
			$arrDisplay['results'] = self::getResultsList_fn( $post->ID );
			$arrDisplay['points'] = self::getPointsList_fn( $post->ID );
			$arrDisplay['graphSeries'] = self::getGraphSeriesList_fn( $post->ID );
			
			return $arrDisplay;
		}
		//---------------------------------------------------------------
		static function getMaxPoints_fn( $arrTags )
		{
			$arrMaxPoints = array();
			$totalPoints = 0;
			//
			$rgsOptions = RGS_CompanySettings::getOption_fn();
			$formID = (isset($rgsOptions['formChoice']) ) ? (int) $rgsOptions['formChoice'] : 0;
			
			if( $formID > 0 ):
				
				$formMail = get_post( $formID );
				$formContent = $formMail->post_content;
				preg_match_all('/<div class="qe-onglet-title">(.*?)<\/div>/s', $formContent, $arrThemes);
				//
				$maxContent = strlen($formContent);
				//
				foreach($arrThemes[1] as $pos => $Theme ):
			
					$formQ = array();
					$curPos = strpos($formContent, $Theme );
					$nextPos = intval( $pos ) + 1;
					if( $nextPos <= ( count($arrThemes[1]) - 1 ) ):
						$lastPos = strpos($formContent, $arrThemes[1][$nextPos] );
					else:
						$lastPos = strlen($formContent);
					endif;
					//
					$subString = substr ( $formContent , $curPos, ($lastPos - $curPos ) );
					//
					$tagStart = 'default:0 ';
					$tagEnd = ']';
					
					preg_match_all('/' . $tagStart . '(.*?)' . $tagEnd.  '/s', $subString, $formQ);
					//
					$questPoints = 0;
					$childs = array();
					$arrQuestions = $formQ[1];
					//
					foreach( $formQ[1] as $posQ => $question ):
						// Remove no valid lines
						if(strlen($question) < 10 ):
							unset($arrQuestions[$posQ]);
						endif;
					endforeach;
					//
					foreach( $arrQuestions as $posQ => $question ):
						$childs = explode('" ', $question);
						$newChilds = array();
			
						foreach($childs as $posQ2 => $q2 ):
							//
							$childs[$posQ2] = trim($q2, '"');
							if( empty($childs[$posQ2]) or strlen($childs[$posQ2]) < 10  ):
								unset($childs[$posQ2]);
							else :
								$newChilds[] = $childs[$posQ2];
							endif;
						endforeach;
						//
						if($pos == 3 and $posQ == 0 ):
							$countChilds = 8;
						else:
							$countChilds = (int) count($newChilds);
						endif;
						//
						$questPoints += $countChilds;
						//
						$arrMaxPoints[$pos]['childs'][$posQ] = $countChilds;
						//
					endforeach;
					//
					$arrMaxPoints[$pos]['nbChilds'] = count($arrMaxPoints[$pos]['childs']); 
					//
					$arrMaxPoints[$pos]['maxPoints'] = $questPoints;
					//
				endforeach;
				//
			endif;
			//
			return $arrMaxPoints;
		}
		//---------------------------------------------------------------
		static function getStructInquest_fn($listInquest)
		{
			$arrReturn = array();
			$arrTags = array();
			$arrPoints = array();
			//$arrMaxPoints = array();
			$arrSeries = array();
			$arrResults = array();
			$arrPercents = array();
			$msgNb = count( $listInquest );
			//
			foreach( $listInquest as $oInquest ) : 
				if( empty($arrTags) ):
					$arrTags = $oInquest['tags'];
				endif;
				//POINTS
				$thePointsTotal = (int) $oInquest['points']['total'];
				$arrPoints['total'] += $thePointsTotal;

				$thePoints = $oInquest['points']['points'];
				foreach( $thePoints as $pos => $oPoint ) : 
					//echo '<pre>P ' . $pos . ':::'.print_r($oPoint, TRUE).'</pre>';
					$arrPoints['points'][$pos] += floatval( $oPoint );
				endforeach;
				//SERIES
				$theSeries = explode( ',', $oInquest['graphSeries'] );
				foreach( $theSeries as $pLine => $oLine ) : 
					$arrSeries[$pLine] += floatval( $oLine );
				endforeach;
				//
				foreach( $arrSeries as $pLine => $oLine ) : 
					$newVal = $arrSeries[$pLine] / $msgNb;
					$arrSeries[$pLine] = floatval( $newVal );
				endforeach;

				//RESULTS
				$theResults = $oInquest['results'];
				foreach( $arrTags as $pTag => $oTag) : 
					$resp = $theResults[$pTag];
					foreach( $oTag as $pTagR => $oTagR) : 
						if( $pTagR == $resp ):
							$arrResults[$pTag][$pTagR] = floatval( $arrResults[$pTag][$pTagR] ) + 1;
						else: 
							if( empty( $arrResults[$pTag][$pTagR] ) ):
								$arrResults[$pTag][$pTagR] = 0;
							endif;
						endif;
					endforeach;
					//
				endforeach;
				// PERCENTS
				foreach( $arrTags as $pTag => $oTag) : 
					foreach( $oTag as $pTagR => $oTagR) : 
						$val = $arrResults[$pTag][$pTagR];
						$arrPercents[$pTag][$pTagR] = ($val * 100 ) / $msgNb;

					endforeach;
					//
				endforeach;
				//
			endforeach;
			//
			$arrMaxPoints = self::getMaxPoints_fn( $arrTags );
			//
			$arrReturn['tags'] = $arrTags;
			$arrReturn['results'] = $arrResults;
			$arrReturn['percents'] = $arrPercents;
			$arrReturn['points'] = $arrPoints;
			$arrReturn['maxPoints'] = $arrMaxPoints;
			$arrReturn['series'] = $arrSeries;
			
			return $arrReturn;
		}
		//---------------------------------------------------------------
		static function getFormArchitect_fn($formID)
		{
			$formMail = get_post( $formID );
			$formContent = $formMail->post_content;
			$formArchitect = array();
			preg_match_all('/<div class="qe-onglet-title">(.*?)<\/div>/s', $formContent, $arrThemes);
			//
			$maxContent = strlen($formContent);
			//
			foreach($arrThemes[1] as $pos => $Theme ):
				
				$formQ = array();
				$curPos = strpos($formContent, $Theme );
				$nextPos = intval( $pos ) + 1;
				if( $nextPos <= ( count($arrThemes[1]) - 1 ) ):
					$lastPos = strpos($formContent, $arrThemes[1][$nextPos] );
				else:
					$lastPos = strlen($formContent);
				endif;
				//
				$subString = substr ( $formContent , $curPos, ($lastPos - $curPos ) );
				preg_match_all('/<p class="qe-question-title">(.*?)<\/p>/s', $subString, $formQ);
				//
				$formArchitect[$pos] = array( 'theme' => $Theme, 'questions' => $formQ[1] );
				//
			endforeach;
			//
			return $formArchitect;
			//
		}
		//---------------------------------------------------------------
		static function getArchitectHtml_fn( $listInquest, $formChoice )
		{
			
			$formArchitect = RGS_FormStats::getFormArchitect_fn($formChoice);
			//
			$architectHTML = '';
			$arrTags = array();
			$arrPoints = array();
			$arrSeries = array();
			$arrResults = array();
			$arrPercents = array();

			$arrReturn = RGS_FormStats::getStructInquest_fn($listInquest);
			$arrTags = (isset($arrReturn['tags']) ) ? $arrReturn['tags'] : array();
			$arrResults = (isset($arrReturn['results']) ) ? $arrReturn['results'] : array();
			$arrPercents = (isset($arrReturn['percents']) ) ? $arrReturn['percents'] : array();
			$arrPoints = (isset($arrReturn['points']) ) ? $arrReturn['points'] : array();
			$arrMaxPoints = (isset($arrReturn['maxPoints']) ) ? $arrReturn['maxPoints'] : array();
			$arrSeries = (isset($arrReturn['series']) ) ? $arrReturn['series'] : array();
			//---------------------------------------------------------
			$colorsSections = RGS_CompanySettings::getSectionsColors_fn();
			$colorsQuestions = RGS_CompanySettings::getQuestionsColors_fn();
			//---------------------------------------------------------
			if( is_array($formArchitect) and count($formArchitect) > 0 ):
				$architectHTML = '<ul id="architect">';
				$mainCount = 0;
				$questionCount = 0;
				foreach( $formArchitect as $thePos => $theTheme ):
					//
					$themeName = $theTheme['theme'];
					$themeMaxPoints = $arrMaxPoints[$thePos]; // [maxPoints][nbChilds][childs]
					//
					$architectHTML .= '<li>'; 
					$architectHTML .= '<span class="architect-theme" style="border-left-color: '  . $colorsSections[$thePos] . '">' . $themeName . '</span>'; 
					//$architectHTML .= ' <span class="architect-series">(' . number_format( floatval( $arrSeries[$mainCount] ), 2 ) . ')</span>'; 
					//
					$themeQuestions = $theTheme['questions'];
					//
					if( is_array($themeQuestions) and count($themeQuestions) > 0 ):

						//
						$architectHTML .= '<ul class="architect-child">';
						//
						foreach( $themeQuestions as $thePosQ => $theQuestion ):
							//
							$maxQ = $themeMaxPoints['childs'][$thePosQ];
							//
							$points = floatval ( $arrPoints['points'][ $questionCount ] );
			
							$pointsDisplay = $points . ' ' . ngettext('point', 'points', $points );
							#$pointsDisplay .= ' ' . __( 'of', $TD ) . ' ' . $maxQ;
							//$plural = ($points != 1) ? 's' : '';
							//$pointsDisplay = $points . ' point' . $plural;
							//$pointsDisplay = $points . ' ' . 'point' . $plural . ' ' . __( 'of', $TD ) . ' ' . $maxQ;

							$responses = $arrTags[ $questionCount ];
							$nbResponses = (is_array($responses) ) ? count($responses) : 0;
							$percents = $arrPercents[ $questionCount ];
							//
							$colorQ = $colorsQuestions[$questionCount ];
							//
							$architectHTML .= '<li id="architecte-q-' . $questionCount . '" style="border-left-color: '  . $colorQ . '">'; 
							$architectHTML .= '<span class="architect-points">(' . $pointsDisplay . ')</span>';
							$architectHTML .= ' <span class="architect-question">' . $theQuestion . '</span>'; 
							//
							if( is_array($responses) and $nbResponses > 0 ):
								//
								$architectHTML .= '<table class="architect-percents"  width="100%" border="0" cellspacing="1" cellpadding="1">';
								$architectHTML .= '<tbody>';
								//
								foreach( $responses as $thePosR => $theReponse ):
									//
									$percent = floatval( $percents[$thePosR] );
									//
									$architectHTML .= '<tr>'; 
									$architectHTML .= '<td class="architect-percent">'; 
									$architectHTML .= '' . number_format( $percent, 0 ) . ' %';
									$architectHTML .= '</td>'; 
									$architectHTML .= '<td class="architect-response">'; 
									$architectHTML .= $theReponse; 
									$architectHTML .= '</td>';
									$architectHTML .= '</tr>';
									//
								endforeach;
								//
								$architectHTML .= '</tbody>';
								$architectHTML .= '</table>';
								//
							endif;
							//
							$architectHTML .= '</li>';
							// 
							$questionCount ++;
							// 
						endforeach;
						//
						$architectHTML .= '</ul>';
						//
					endif;
					//
					$architectHTML .= '</li>';
					//
					$mainCount ++;
					//
				endforeach;
				$architectHTML .= '</ul>';

			endif;
			//
			return $architectHTML;
		}
		//---------------------------------------------------------------
		// AJAX CALLBACK
		//---------------------------------------------------------------
		static function getStatsList_fn()
		{
			$companyID = (isset( $_POST['companyID'] ) ) ? (int) $_POST['companyID'] : -1;
			//
			if( $companyID === -1 ) :
				wp_send_json_error( __('companyID is mandatory', self::getTD_fn() ) );
			else:
				//
				$rgsOptions = RGS_CompanySettings::getOption_fn();
				$formChoice = (int) $rgsOptions['formChoice'];
				//
				if($companyID == 0 ):
					$msgList = self::getAllInquest_fn();
				else:
					$msgList = self::getCompanyInquest_fn($companyID);
				endif;
				
				if( ! $msgList ):
					
					wp_send_json_success( array( 
						'code' => 1, 
						'content' => __('No data found', self::getTD_fn() )
					) );
			
				else:
					
					$msgListHtml = self::getArchitectHtml_fn( $msgList , $formChoice );
					
					if( ! $msgListHtml ):
						wp_send_json_success( array( 
							'code' => 2, 
							'content' => __( 'No data found', self::getTD_fn() ) 
						) );
						//wp_send_json_error( __('Empty List', self::getTD_fn() ) );
					else:
						$arrReturn = RGS_FormStats::getStructInquest_fn($msgList);
						$arrPoints = (isset($arrReturn['points']) ) ? $arrReturn['points'] : array();
						$arrSeries = (isset($arrReturn['series']) ) ? $arrReturn['series'] : array();
						//---------------------------------------------------------
			
						wp_send_json_success( array( 
							'code' => 0, 
							'content' => $msgListHtml,
							'seriesSections' => $arrSeries,
							'seriesQuestions' => $arrPoints
						) );
					endif;
			
				endif;
			
			endif;
			
			wp_die();
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsFormStats_fn' ) ):
		function rgsFormStats_fn() 
		{
			return RGS_FormStats::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_FormStats' ] ) ):
		$GLOBALS[ 'RGS_FormStats' ] = rgsFormStats_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------