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
 * @class RGS_CompanyForm
 * @fullname Eco Citoyen Management
 * @package RGS_CompanyForm
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_CompanyForm.php
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
if( ! class_exists( 'RGS_CompanyForm' ) ):
	//----------------------------------
	class RGS_CompanyForm
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
		static public $gCF7Installed;
		//---
		static public $gFIELDS;
		//---
		const K_SLUG = 'rgsCompanyForm';
		const K_PREFIX = 'rgsCompanyForm-';
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
			self::$gCF7Installed 	= is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
			//---
			self::$gFIELDS 			= array();
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
        	endif;
			if(is_admin()):
				
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
				//
			else:
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts_fn'), 11 );
			endif;
			//---------------------------------------------------
			if ( self::$gCF7Installed ) :
				//-----
				add_action( 'wpcf7_before_send_mail', array(__CLASS__, 'beforeSend_fn') );
				//-----
				add_filter('wpcf7_form_response_output', array(__CLASS__, 'filterResponseOutput_fn'), 10, 4);
				//-----
			
				//add_action( 'wp_footer', array(__CLASS__, 'customFooter_fn' ));
				//add_filter( 'wpcf7_load_js', '__return_false' );
				//add_filter( 'wpcf7_load_css', '__return_false' );
				/*
				add_filter(‘wpcf7_form_response_output’, function($output, $class, $content, $cf7) {

			  		return $output;

			   });
			   
			   add_filter(‘wpcf7_validation_error’, function($error, $name, $cf7) {

   					return $error;
				});
				*/
				add_action('wpcf7_mail_sent', array(__CLASS__, 'afterSubmission_fn'), 10, 1 );
				//
			endif;
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
		}
		//---------------------------------------------------------------
		static function getLocalize_fn()
		{
			$SLUG = self::getSlug_fn();
			$TD = self::getTD_fn();
			//
			return array( 
				'ajaxUrl' 			=> admin_url( 'admin-ajax.php' ),
				'nonce' 			=> wp_create_nonce( $SLUG . '-nonce' ),
				'isAdmin' 			=> is_admin()? true: false,
				'isFrontPage' 		=> is_front_page()? true: false,
				'slug' 				=> $SLUG,
				/*'assetsUrl' 		=> self::$gAssetsUrl,
				'stylesUrl'			=> self::$gCssUrl,
				'scriptsUrl'		=> self::$gJsUrl,
				'imgUrl' 			=> self::$gImgUrl,
				'delay' 			=> 1000,
				'isSingleOrPage' 	=> ( is_page() || is_single() )? true: false,
				'taxoCampaign'		=> self::getSlug_fn() . '-campaign',*/
				'pagePermalink' 	=> get_the_permalink(),
				'lang' 				=> array(
					'yes' 				=> __( 'Yes', $TD ),
					'saveImage'			=> __( 'Save Image', $TD )

				)
			);
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		static function initForm_fn()
		{
			
		}
		//---------------------------------------------------------------
		static function getFormElem_fn( $id )
		{
			
			$data = get_post($id);
			
			if( $data ):
				return $data;
			else:
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		static function getFormsList_fn()
		{
			$args = array(
				'post_type' => 'wpcf7_contact_form', 
				'posts_per_page' => -1
			);
			
			$rs = array();
			if( $data = get_posts($args)):
				foreach($data as $key) :
					$rs[$key->ID] = $key->post_title;
				endforeach;
				return $rs;
			else:
				$rs['0'] = esc_html__('No Form found', self::getTD_fn());
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		// SHORTCODE
		//---------------------------------------------------------------
		static function getCompanyShortcode_fn()
		{
			return '[contact-form-7 id="329" title="Questionnaire employés"]';
		}
		//---------------------------------------------------------------
		// CUSTOM FORM 7
		//---------------------------------------------------------------
		static function getFormTags_fn( $scannedFormTags, $fieldMax = 11 )
		{
			$tags = array();
			foreach ($scannedFormTags as $key => $wpcf7_FormTag):
				if( $key <= $fieldMax):
					$tags[$key] = $wpcf7_FormTag->values;
				endif;
			endforeach;
			return $tags;
		}
		//---------------------------------------------------------------
		static function getFormValues_fn( $postedData, $fieldMax = 11 )
		{
			$response = array();
			for ($fieldId = 0; $fieldId <= $fieldMax; $fieldId++) :
				$response[] = isset($postedData['radio-' . ($fieldId+1)][0]) ? $postedData['radio-' . ($fieldId+1)][0]: '';
			endfor;
			return $response;
		}
		//---------------------------------------------------------------
		static function getResults_fn( $responses, $tags )
		{
			//
			$results = array();
			//
			foreach ($responses as $key => $tagResponse):
				$choices = $tags[$key];
				$foundKey = array_search($tagResponse, $choices);
				if($foundKey === FALSE):
					$results[] = -1;
				else:
					$results[] = $foundKey;
				endif;
			
			endforeach;
			
			return $results;
		}
		//---------------------------------------------------------------
		static function setMail_fn( $wpcf7, $replaceTags = array() )
		{
			$mail = $wpcf7->prop('mail');
			//
			if(isset($replaceTags['company']) ):
				$companyTag = '***COMPANY***';
				$graphTag = '***GRAPH***';
				//
				$companyTitle = $replaceTags['company'];
				$mail['body'] = str_replace($companyTag,$companyTitle, $mail['body']);
				//
				$graphVal = '';
				$mail['body'] = str_replace($graphTag,$graphVal, $mail['body']);
			endif;
			//
			$mailHeaderTmpl = '<p>' . __( 'IP Address:', self::getTD_fn() ) . $_SERVER['REMOTE_ADDR'].'</p>'; //file_get_contents(dirname(__FILE__) . '/templates/emails/mail_header.php');
  			$mailFooterTmpl = ''; //self::createChart_fn($chartProp); //file_get_contents(dirname(__FILE__) . '/templates/emails/mail_footer.php');

  			// Replace the email body with the designed one
  			$mail['body'] = $mailHeaderTmpl . $mail['body'] . $mailFooterTmpl;
			
			return $mail;
		}
		//---------------------------------------------------------------
		static function getSeries_fn( $points )
		{
			$return = array();
			$return[0] = floatval($points[0]) + floatval($points[1]);
			$return[1] = floatval($points[2]);
			$return[2] = floatval($points[3]) + ($points[4]);
			$return[3] = floatval($points[5]) + floatval($points[6]) + floatval($points[7]);
			$return[4] = floatval($points[8]) + floatval($points[9]);
			$return[5] = floatval($points[10]) + floatval($points[11]);
			
			return $return;
		}
		//---------------------------------------------------------------
		static function getPoints_fn( $results )
		{
			$return = array();
			$return['total'] = 0;
			$return['points'] = array();
			$return['maxPoints'] = array();
			//
			foreach ( $results as $key => $val ) :
				//
				switch( $key ):
					case 5:
						// Par quel moyen principal vous rendez-vous à votre travail ?
						switch( $val ):
							case 0:
								$return['points'][$key] = 0.5; 
								break;
							case 1:
								$return['points'][$key] = 0.5 * 2; // 1
								break;
							case 2:
								$return['points'][$key] = 0.5 * 4; // 2
								break;
							case 3:
								$return['points'][$key] = 0.5 * 8; // 4
								break;
							case 4:
								$return['points'][$key] = 0.5 * 16; // 8
								break;
							case 5:
								$return['points'][$key] = 0.5 * 24; // 12
								break;
						endswitch;
						
						$return['total'] += floatval( $return['points'][$key]);
						break;
						
					default:
						$return['points'][$key] = floatval($val) + 1;
						$return['total'] += $return['points'][$key];
				endswitch;
				//
				switch( $key ):
					case 4:
					case 10:
						if( ! isset($return['maxPoints'][$key] ) ):
							$return['maxPoints'][$key] = 3;
						endif;
						break;
					case 5:
						if( ! isset($return['maxPoints'][$key] ) ):
							$return['maxPoints'][$key] = 0.5 * 24;
						endif;
						break;
					case 8:
					case 9:
					case 11:
						if( ! isset($return['maxPoints'][$key] ) ):
							$return['maxPoints'][$key] = 2;
						endif;
						break;
					default:
						if( ! isset($return['maxPoints'][$key] ) ):
							$return['maxPoints'][$key] = 4;
						endif;
				endswitch;
				//
			endforeach;
			// 
			return $return;
			//
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		public function beforeSend_fn($wpcf7)
		{
			$formId = null;
			$postId = null;
			$dbFields = array();
			$replaceTags = array();
			//
			//-----------------
			$submission = WPCF7_Submission::get_instance();
			//
			if ($submission) :
				$postedData = $submission->get_posted_data();
				//
				if ( empty( $postedData) ):
					return $wpcf7;
				else:
					//
					$FORM_ID = $postedData['_wpcf7'];
					//
					$POST_ID = $postedData['_wpcf7_container_post'];
					//
					$thePost = get_post( $POST_ID );
					if( $thePost ):
						// CHECK META FOEM ID
						$refDatas = get_post_meta($POST_ID, RGS_CompanyMBoxes::getOptionNameMB_fn(), TRUE);
						//
						$refFormID = 0;
						if(isset($refDatas['REFS']['REF_FormID'])):
							$refFormID = $refDatas['REFS']['REF_FormID'];
						endif;
						// CHECK IS FORM IS VALID...
						if( $FORM_ID != $refFormID):
							#$wpcf7->skip_mail = true;
							return $wpcf7;
						endif;
					endif;
					////////////////////////////////////////////////
					$replaceTags['company'] = $thePost->post_title;
					// UPDATE MAIL
					$mail = self::setMail_fn( $wpcf7, $replaceTags );
					//
					if( $thePost ):
						$mail['subject'] = $thePost->post_title;
					endif;
					// Save our new body content
					$wpcf7->set_properties( array("mail" => $mail)) ;
					//
				endif;
			endif;
			//-------------------------------------------------
			return $wpcf7;
			//-------------------------------------------------
		}
		//---------------------------------------------------------------
		static function filterResponseOutput_fn( $output, $class, $content, $instance ) { 
			// make filter magic happen here...
			$output = '<div id="rgsResponse" class="' . $class . ' " data-fields="'. json_encode(self::$gFIELDS) .'">' . esc_html( $content ) . '</div>';
			return $output;
		} 
		//---------------------------------------------------------------
		static function customFooter_fn()
		{
			?>
			<script type="text/javascript">
			document.addEventListener( 'wpcf7mailsent', function( event ) {
				if ( '123' == event.detail.contactFormId ) {
					ga( 'send', 'event', 'Contact Form', 'submit' );
				}
			}, false );
			</script>

			<script>
			document.addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'http://example.com/';
			}, false );
				
				
				var wpcf7Elm = document.querySelector( '.wpcf7' );
 
				wpcf7Elm.addEventListener( 'wpcf7submit', function( event ) {
				  alert( "Fire!" );
				}, false );
				
				document.addEventListener( 'wpcf7submit', function( event ) {
				  
					if ( '123' == event.detail.contactFormId ) {
						alert( "The contact form ID is 123." );
						// do something productive
					  }
					
					if ( '123' == event.detail.containerPostId ) {
						alert( "The contact form ID is 123." );
						// do something productive
					  }
					
					var inputs = event.detail.inputs;
 
					  for ( var i = 0; i < inputs.length; i++ ) {
						if ( 'your-name' == inputs[i].name ) {
						  alert( inputs[i].value );
						  break;
						}
					  }
				}, false );
				
			</script>
			<?php
		}
		//---------------------------------------------------------------
		static function afterSubmission_fn($wpcf7)
		{
			
			$formId = null;
			$postId = null;
			$dbFields = array();
			$replaceTags = array();
			//
			//-----------------
			$submission = WPCF7_Submission::get_instance();
			//
			if ($submission) :
				$postedData = $submission->get_posted_data();
				if ( empty( $postedData) ):
					return $wpcf7;
				else:
					//
					$dbFields['createdAt'] = gmdate('Y-m-d H:i:s');
					//
					$dbFields['uniqId'] = ( isset( $_POST['uniqid'] ) ) ? $_POST['uniqid'] : '';
					//
					$currentUser = wp_get_current_user();

					if ( 0 == $currentUser->ID ) :
						$dbFields['USER_ID'] = 0; // Not logged in.
					else :
						$dbFields['USER_ID'] = $currentUser->ID; // Logged in.
					endif;
					//
					$dbFields['unitTag'] = $postedData['_wpcf7_unit_tag'];
					//
					$dbFields['FORM_ID'] = $postedData['_wpcf7'];
					//
					$dbFields['POST_ID'] = $postedData['_wpcf7_container_post'];
					//
					$thePost = get_post( $dbFields['POST_ID'] );
					if( $thePost ):
						$ID = $dbFields['POST_ID'];
						// CHECK META FOEM ID
						$refDatas = get_post_meta($ID, RGS_CompanyMBoxes::getOptionNameMB_fn(), TRUE);
						#$dbFields['refDatas'] = $refDatas;
						$refFormID = 0;
						if(isset($refDatas['REFS']['REF_FormID'])):
							$refFormID = $refDatas['REFS']['REF_FormID'];
						endif;
						///////////////////////////////////
						// CHECK IS FORM IS VALID...
						if( $dbFields['FORM_ID'] != $refFormID):
							#$wpcf7->skip_mail = true;
							return $wpcf7;
						endif;
						//
						$dbFields['POST_TYPE'] = $thePost->post_type;
						//
						$dbFields['POST_TITLE'] = $thePost->post_title;
						//
						$TAXO = self::getSlug_fn() . '-campaign';
						// Get post type taxonomies.
						$terms = get_the_terms( $ID, $TAXO );
						if ( ! empty( $terms ) ) :
							
							$dbCampaigns = array();
							//$dbFields['taxos'] = $terms;
							//
							foreach($terms as $term):
								//
								$termMETAS = RGS_Company::campaignTaxoGetCustomFields_fn( $term->term_id );
								//
								$dbCampaigns[] = array(
									'campaignId' => $term->term_id,
									'slug' => $term->slug,
									'name' => $term->name,
									'description' => $term->description,
									'startDate' => $termMETAS['startDate'],
									'endDate' => $termMETAS['endDate'],
									'cLogo' => $termMETAS['cLogo'],
								);
							endforeach;
							//
							//$taxosString = sc_sql( serialize( $dbCampaigns ) );
							$dbFields['taxos'] = $dbCampaigns; 
						endif;
						//
						$dbFields['private'] = ( ! empty( $terms ) )? 'yes' : 'no';
						//
						$scannedFormTags = $wpcf7->scan_form_tags();
						$gTags = self::getFormTags_fn( $scannedFormTags );
						//$tagsString = sc_sql( serialize( $gTags ) );
						$dbFields['tags'] = $gTags;
						//
						$gResponses = self::getFormValues_fn($postedData);
						//$responseString = sc_sql( serialize( $gResponses ) );
						$dbFields['responses'] = $gResponses;
						//
						$gResults = self::getResults_fn( $gResponses, $gTags );
						//$resultsString = sc_sql( serialize( $gResults ) );
						$dbFields['results'] = $gResults;
						//
						foreach ( $gResults as $key => $val ) :
							$dbFields['result_' . $key ] = (int) $val;
						endforeach;
						// 
						$points = self::getPoints_fn( $gResults );
						//$pointsString = sc_sql( serialize( $points['points'] ) );
						$dbFields['points'] = $points;
						$dbFields['pointsTotal'] = $points['total'];
						//
						$series = self::getSeries_fn( $points['points'] );
						//$seriesString = esc_sql( serialize( $series ) );
						$dbFields['series'] = $series;
						//
						foreach ( $series as $key => $val ) :
							$dbFields['theme_' . $key ] = (int) $val;
						endforeach;
						//
						$dbFields['graphSeries'] = implode( ',', $series );
						// 
						$mail = $wpcf7->prop('mail');
						//
						$dbFields['mail'] = $mail;
						//
					endif;
					///////////////////////////////////
				endif;
				///////////////////////////////////////
			endif;
			///////////////////////////////////////////
			if( method_exists( 'RGS_CompanyInquest', 'insertInquest_fn' ) ):
				//
				RGS_CompanyInquest::insertInquest_fn( $dbFields );
				//
			endif;
			//-------------------------------------------------
			return $wpcf7;
			//-------------------------------------------------
		}
		//---------------------------------------------------------------
		// DB
		//---------------------------------------------------------------
		static function dbGetTableName_fn()
		{
			global $wpdb;
     		$tableName = $wpdb->prefix . self::getSlug_fn() . '_forms';
			return $tableName;
		}
		//---------------------------------------------------------------
		static function dbGetOptionName_fn()
		{
     		$optionName = self::getSlug_fn() . '_forms_db_version';
			return $optionName;
		}
		//---------------------------------------------------------------
		static function dbExists_fn()
		{
			global $wpdb;
			$tableName = self::dbGetTableName_fn();
			if ( $wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName ) :
				return TRUE;
			else :
				return FALSE;
			endif;
		}
		//---------------------------------------------------------------
		static function dbCreateTableForm_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `formId` bigint(20) UNSIGNED NOT NULL,
			  `postId` bigint(20) UNSIGNED NOT NULL,
			  `userId` bigint(20) UNSIGNED NULL,
			  
			  `private` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
			  
 			  `tags` text COLLATE utf8_unicode_ci NOT NULL,
 			  `responses` text COLLATE utf8_unicode_ci NOT NULL,
 			  `results` text COLLATE utf8_unicode_ci NOT NULL,
 			  `mail` text COLLATE utf8_unicode_ci NOT NULL,
 
			  `result_0` bigint(20) UNSIGNED NOT NULL,
			  `result_1` bigint(20) UNSIGNED NOT NULL,
			  `result_2` bigint(20) UNSIGNED NOT NULL,
			  `result_3` bigint(20) UNSIGNED NOT NULL,
			  `result_4` bigint(20) UNSIGNED NOT NULL,
			  `result_5` bigint(20) UNSIGNED NOT NULL,
			  `result_6` bigint(20) UNSIGNED NOT NULL,
			  `result_7` bigint(20) UNSIGNED NOT NULL,
			  `result_8` bigint(20) UNSIGNED NOT NULL,
			  `result_9` bigint(20) UNSIGNED NOT NULL,
			  `result_10` bigint(20) UNSIGNED NOT NULL,
			  `result_11` bigint(20) UNSIGNED NOT NULL,
			  
			  `theme_0` bigint(20) UNSIGNED NOT NULL,
			  `theme_1` bigint(20) UNSIGNED NOT NULL,
			  `theme_2` bigint(20) UNSIGNED NOT NULL,
			  `theme_3` bigint(20) UNSIGNED NOT NULL,
			  `theme_4` bigint(20) UNSIGNED NOT NULL,
			  `theme_5` bigint(20) UNSIGNED NOT NULL,
			  
			  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			  PRIMARY KEY (`id`)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$success = empty($wpdb->last_error);

			return $success;
		}
		//---------------------------------------------------------------
		static function dbCreateTableFormCampaign_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn() . '_campaign2form';
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `formId` bigint(20) UNSIGNED NOT NULL,
			  `campaignId` bigint(20) UNSIGNED NOT NULL,
			  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			  
			  PRIMARY KEY (`id`)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$success = empty($wpdb->last_error);

			return $success;
		}
		//---------------------------------------------------------------
		static function dbCreateTableCampaign_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn() . '_campaign';
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `campaignId` bigint(20) UNSIGNED NOT NULL,
			  `slug` varchar(100) DEFAULT NULL,
			  `name` varchar(100) DEFAULT NULL,
 			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `logoId` mediumint(11) DEFAULT NULL,
			  `dateStart` varchar(10) NOT NULL,
			  `dateEnd` varchar(10) NOT NULL,
			  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			  
			  PRIMARY KEY (`id`)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$success = empty($wpdb->last_error);

			return $success;
		}
		//---------------------------------------------------------------
		static function dbCreate_fn()
		{
			self::dbCreateTableForm_fn();
			self::dbCreateTableCampaign_fn();
			self::dbCreateTableFormCampaign_fn();
			
		}
		//---------------------------------------------------------------
		static function dbUpgradeNewVersion_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  id mediumint(8) unsigned NOT NULL auto_increment,
			  formId bigint(20) UNSIGNED NOT NULL,
			  postId bigint(20) UNSIGNED NOT NULL,
			  userId bigint(20) UNSIGNED NULL,
			  createdAt datetime NOT NULL,
			  expires_at datetime NOT NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$success = empty($wpdb->last_error);

			return $success;
			
		}
		//---------------------------------------------------------------
		static function dbUpgrade_fn( $version ) 
		{
			$optName = self::dbGetOptionName_fn();
			$savedVersion = get_option( $optName );

			/*if (( version_compare( $savedVersion, $version ) < 0 ) && self::dbUpgradeNewVersion_fn()) :
				update_option( $optName, $version );
			endif;*/
		}
		//---------------------------------------------------------------
		static function dbRemove_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
 
			$theRemovalQuery = "DROP TABLE IF EXISTS {$tableName}";

			$wpdb->query( $theRemovalQuery );
		}
		//---------------------------------------------------------------
		static function dbInit_fn()
		{
			if( ! self::dbExists_fn() ):
				self::dbCreate_fn();
			endif;
		}
		//---------------------------------------------------------------
		static function dbInsert_fn( $dbFields )
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
 			// Check NUMBER OF EMPLOYEES
			
			
			$insert = $wpdb->insert( $tableName, $dbFields );
		}
		//---------------------------------------------------------------
		static function dbGetTableList_fn()
		{
			global $wpdb;
			$tableName = self::dbGetTableName_fn();
			$results = $wpdb->get_results( "SELECT * FROM {$tableName}", OBJECT );
			return $results;
		}
		//---------------------------------------------------------------
		static function dbGetRow_fn( $id )
		{
			global $wpdb;
			$tableName = self::dbGetTableName_fn();
			$results = $wpdb->get_results( "SELECT * FROM {$tableName} WHERE id = {$id}", OBJECT );
			return $results;
		}
		//---------------------------------------------------------------
		static function dbGetCount_fn( $id )
		{
			global $wpdb;
			$tableName = self::dbGetTableName_fn();
			return $wpdb->get_var( "SELECT COUNT(*) FROM $tableName" );
		}
		//---------------------------------------------------------------
		// TEMPLATES
		//---------------------------------------------------------------
		static function includeTemplate_fn()
		{
			$file = RGS_Company::$gTemplatesDir .'companyForm.php';
			if( is_file( $file ) ):
				include_once( $file  );
			endif;
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
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsCompanyForm_fn' ) ):
		function rgsCompanyForm_fn() 
		{
			return RGS_CompanyForm::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_CompanyForm' ] ) ):
		$GLOBALS[ 'RGS_CompanyForm' ] = rgsCompanyForm_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------