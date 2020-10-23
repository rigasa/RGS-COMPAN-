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
 * @class EC_CampaignForm
 * @fullname Eco Citoyen Management
 * @package EC_CampaignForm
 * @category Core
 * @filesource assets/plugins/Entreprise/EC_CampaignForm.php
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
if( ! class_exists( 'EC_CampaignForm' ) ):
	//----------------------------------
	class EC_CampaignForm
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
		const K_SLUG = 'ecCampaignForm';
		const K_PREFIX = 'ecCampaignForm-';
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
			//---------------------------------------------------
			if ( self::$gCF7Installed ) :
				//-----
				add_action( 'wpcf7_before_send_mail', array(__CLASS__, 'beforeSend_fn') );
				//-----
				//add_action( 'wp_footer', array(__CLASS__, 'customFooter_fn' ));
				//add_filter( 'wpcf7_load_js', '__return_false' );
				//add_filter( 'wpcf7_load_css', '__return_false' );
				//------
			endif;
			//---------------------------------------------------
		}
		//---------------------------------------------------------------
		// CPT
		//---------------------------------------------------------------
		static function getSlug_fn()
		{
			return EC_Company::getSlug_fn();
		}
		//---------------------------------------------------------------
		static function getTD_fn()
		{
			return EC_Company::getTD_fn();
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
				$rs['0'] = esc_html__('No Contact Form found', $TD);
				return null;
			endif;
			
		}
		//---------------------------------------------------------------
		// SHORTCODE
		//---------------------------------------------------------------
		static function getCampaignForm_fn()
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
		static function setMail_fn( $wpcf7 )
		{
			$mail = $wpcf7->prop('mail');
			
			$mailHeaderTmpl = ''; //file_get_contents(dirname(__FILE__) . '/templates/emails/mail_header.php');
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
			endforeach;
			// 
			return $return;
			//
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		public function beforeSend_fn($wpcf7)
		{
			$formId = null;
			$postId = null;
			$dbFields = array();
			
			// Add in DB => Tags = radio buttons labels
			#echo '<pre>gTags::: '.print_r( $gTags, TRUE ).'</pre><br>';
			//-----------------
			$submission = WPCF7_Submission::get_instance();
			
			if ($submission) :
				$postedData = $submission->get_posted_data();
				if ($postedData) :
					//
					$dbFields['created_at'] = gmdate('Y-m-d H:i:s');
					//
					$currentUser = wp_get_current_user();

					if ( 0 == $currentUser->ID ) :
						$dbFields['user_id'] = 0; // Not logged in.
					else :
						$dbFields['user_id'] = $currentUser->ID; // Logged in.
					endif;
					//
					$dbFields['form_id'] = $postedData['_wpcf7'];
					//
					$dbFields['post_id'] = $postedData['_wpcf7_container_post'];
					//
					$scannedFormTags = $wpcf7->scan_form_tags();
					$gTags = self::getFormTags_fn( $scannedFormTags );
					$tagsString = mysql_escape_string( serialize( $gTags ) );
					$dbFields['tags'] = $tagsString;
					//
					$gResponses = self::getFormValues_fn($postedData);
					$responseString = mysql_escape_string( serialize( $gResponses ) );
					$dbFields['responses'] = $responseString;
					//
					$gResults = self::getResults_fn( $gResponses, $gTags );
					$resultsString = mysql_escape_string( serialize( $gResults ) );
					$dbFields['results'] = $resultsString;
					//
					foreach ( $gResults as $key => $val ) :
						$dbFields['result_' . $key ] = (int) $val;
					endforeach;
					// 
					$points = self::getPoints_fn( $gResults );
					$pointsString = mysql_escape_string( serialize( $points['points'] ) );
					$dbFields['points'] = $pointsString;
					$dbFields['total'] = $points['total'];
					//
					$series = self::getSeries_fn( $points['points'] );
					$seriesString = mysql_escape_string( serialize( $series ) );
					$dbFields['series'] = $seriesString;
					//
					foreach ( $series as $key => $val ) :
						$dbFields['theme_' . $key ] = (int) $val;
					endforeach;
					// 
					// UPDATE MAIL
					$mail = self::setMail_fn( $wpcf7 );
					//
					$thePost = get_post( $dbFields['post_id'] );
					if( $thePost ):
						$mail['subject'] = $thePost->post_title;
					endif;
					// Save our new body content
					$wpcf7->set_properties( array("mail" => $mail)) ;
					//
					$dbFields['mail'] = mysql_escape_string( serialize( $mail ) );
					//
					// SAVE IN DB
					// $insert = self::dbInsert_fn( $dbFields );
					//
				endif;
			endif;
			//-----------------
			echo '<pre>FIELDS::: '.print_r( $dbFields, TRUE ).'</pre><br>';
			die('beforeSend_fn');
			//-------------------------------------------------
			return FALSE; //$wpcf7;
			//-------------------------------------------------
		}
		//---------------------------------------------------------------
		public function beforeSend2_fn($wpcf7)
		{
			$formId = null;
			$postId = null;
			
			
			$scannedFormTags = $wpcf7->scan_form_tags();
			self::$gTags = self::getFormTags_fn( $scannedFormTags );
			
			echo '<pre>beforeSend_fn::: scannedFormTags::: '.print_r( self::$gTags, TRUE ).'</pre><br>';
			die('DEBUG');
			
			$submission = WPCF7_Submission::get_instance();
			if ($submission) :
				$postedData = $submission->get_posted_data();
				
				$formId = $postedData['_wpcf7']; // 329
				$postId = $postedData['_wpcf7_container_post']; // 288
				#echo '<pre>form_id::: '.print_r( $formId, TRUE ).'</pre><br>';
				#echo '<pre>post_id::: '.print_r( $postId, TRUE ).'</pre><br>';
				//
				self::$gResponses = self::getFormValues_fn($postedData);
				
				echo '<pre>beforeSend_fn::: getFormValues_fn::: '.print_r( self::$gResponses, TRUE ).'</pre><br>';
			
				/*$fieldMax = 11;
				for ($fieldId = 0; $fieldId <= $fieldMax; $fieldId++) :
    				self::$gResponses[] = isset($postedData['radio-' . ($fieldId+1)][0]) ? $postedData['radio-' . ($fieldId+1)][0]: '';
				endfor;*/
				self::$gResults = self::getResults_fn( self::$gResponses, self::$gTags );
				
				// array("Memory","Disk","Network","Slots","CPU")
				self::$gLabels = array();
			
				echo '<pre>beforeSend_fn::: getResults_fn::: '.print_r( self::$gResults, TRUE ).'</pre><br>';
			
				$currentUser = wp_get_current_user();

				if ( 0 == $currentUser->ID ) :
					// Not logged in.
				else :
					// Logged in.
				endif;
				// chart properties
				$chartProp = array();
				$chartProp['labels'] = self::$gLabels;
				$chartProp['values'] = self::$gResults;
				$chartProp['serieId'] = 'Serie1';
				$chartProp['serieName'] = __('Reference');
			
				
				$mail = self::setMail_fn( $wpcf7, $chartProp );
				#$mail['subject'] = "this is an alternate subject" ;
				// Save our new body content
				#$wpcf7->set_properties( array("mail" => $mail)) ;
				
				echo '<pre>beforeSend_fn::: setMail_fn::: '.print_r( $mail, TRUE ).'</pre><br>';
			
				// If you want to skip mailing the data, you can do it...
    			#wpcf7->skip_mail = true;
			
			endif;
			//-------------------------------------------------
			return FALSE; //$wpcf7;
			
		}
		//---------------------------------------------------------------
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
		static function dbCreate_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `form_id` bigint(20) UNSIGNED NOT NULL,
			  `post_id` bigint(20) UNSIGNED NOT NULL,
			  `user_id` bigint(20) UNSIGNED NULL,
			  
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
			  
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$success = empty($wpdb->last_error);

			return $success;
			
		}
		//---------------------------------------------------------------
		static function dbUpgradeNewVersion_fn()
		{
			global $wpdb;
			
			$tableName = self::dbGetTableName_fn();
    		$charsetCollate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE `{$tableName}` (
			  id mediumint(8) unsigned NOT NULL auto_increment,
			  form_id bigint(20) UNSIGNED NOT NULL,
			  post_id bigint(20) UNSIGNED NOT NULL,
			  user_id bigint(20) UNSIGNED NULL,
			  created_at datetime NOT NULL,
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

			if (( version_compare( $savedVersion, $version ) < 0 ) && self::dbUpgradeNewVersion_fn()) :
				update_option( $optName, $version );
			endif;
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
		/*
		$wpdb->insert("{$wpdb->base_prefix}cli_logins", [
			'public_key' => $magic->getKey(),
			'private_key' => $data['private'],
			'user_id' => $data['user'],
			'created_at' => gmdate('Y-m-d H:i:s'),
			'expires_at' => gmdate('Y-m-d H:i:s', $data['time'] + ceil($expires)),
		]);
		*/
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
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'ecCampaignForm_fn' ) ):
		function ecCampaignForm_fn() 
		{
			return EC_CampaignForm::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'EC_CampaignForm' ] ) ):
		$GLOBALS[ 'EC_CampaignForm' ] = ecCampaignForm_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------