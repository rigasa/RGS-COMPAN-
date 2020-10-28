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
 * @class RGS_CompanySettings
 * @fullname RiGaSa Companion
 * @package RGS_CompanySettings
 * @category Core
 * @filesource assets/plugins/Entreprise/RGS_CompanySettings.php
 * @version 0.0.1
 * @created 2020-10-02
 * @author  Ri.Ga.Sa <rigasa@rigasa.ch>
 * @updated 2020-10-02
 * @copyright 2020 Ri.Ga.Sa
 * @license http://www.php.net/license/3_01.txt  PHP License 3.01
 * pChart http://pchart.sourceforge.net/screenshots.php
*/                                                                                 

//--------------------------------------
if ( ! defined( 'ABSPATH' ) ) exit; // SECURITY : Exit if accessed directly
//--------------------------------------
if( ! class_exists( 'RGS_CompanySettings' ) ):
	//----------------------------------
	class RGS_CompanySettings
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
		static public $gDefOptions;
		//---
		const K_SLUG = 'rgsCompanySettings';
		const K_PREFIX = 'rgsCompanySettings-';
		const K_VERS = '1.0.0';
		
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
			self::$gDefOptions = self::getDefaultOptions_fn();
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
            	add_action( 'admin_init', array( __CLASS__, 'initSettings_fn' ) );
				//-----------------------------------------------
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueColorpicker_fn' ));
        	endif;
			//----------------------------------------------------
		}
		//---------------------------------------------------------------
		// ENQUEUE
		//---------------------------------------------------------------
		static function enqueueColorpicker_fn()
		{
			wp_enqueue_style( 'wp-color-picker' );
			//
			wp_enqueue_script( 'wp-color-picker' );
			//
			if( file_exists( RGS_Company::$gJsDir . 'wp-color-picker-alpha.min.js' ) ):
				wp_enqueue_script( 
					'wp-color-picker-alpha', 
					RGS_Company::$gJsUrl . 'wp-color-picker-alpha.min.js',
					array( 'wp-color-picker' ), 
					'0.0.1', 
					true 
				);
			endif;
		}
		//---------------------------------------------------------------
		// GETTERS
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
		static function getSettingsName_fn()
		{
			return self::getSlug_fn() . '_Setings'; 
		}
		//---------------------------------------------------------------
		static function getOptionName_fn()
		{
			return self::getSlug_fn() . '_options';
		}
		//--------------------------------------------------------------
		// OPTIONS
		//---------------------------------------------------------------
		static function getDefaultOptions_fn()
		{
			$formChoice = 329;
			//
			$settings = array(
				'themeChoice' 			=> 'light',
				'displayAllInRadar' 	=> 1,
				'formChoice' 			=> $formChoice,
    			'colorSection_0' 		=> '#191970',
				'colorQuestion_0' 		=> '#ADFF2F',
				'colorQuestion_1' 		=> '#AFEEEE',
				'colorSection_1' 		=> '#FFFFE0',
				'colorQuestion_2' 		=> '#EEE8AA',
				'colorSection_2' 		=> '#66CDAA',
				'colorQuestion_3' 		=> '#FF8C00',
				'colorQuestion_4' 		=> '#00FA9A',
				'colorSection_3' 		=> '#FFFACD',
				'colorQuestion_5' 		=> '#FFD700',
				'colorQuestion_6' 		=> '#8FBC8F',
				'colorQuestion_7' 		=> '#00FF7F',
				'colorSection_4' 		=> '#808080',
				'colorQuestion_8' 		=> '#ADFF2F',
				'colorQuestion_9' 		=> '#FFEBCD',
				'colorSection_5' 		=> '#008000',
				'colorQuestion_10' 		=> '#FFDEAD',
				'colorQuestion_11' 		=> '#FFF5EE'
			);
			//
			return $settings;
		}
		//
		//---------------------------------------------------------------
		static function saveOption_fn($optName = '')
		{
			if( empty( $optName ) ): $optName = self::getOptionName_fn(); endif;
		}
		//---------------------------------------------------------------
		static function getOption_fn($optName = '')
		{
			//------------------
			if( empty( $optName ) ): $optName = self::getOptionName_fn(); endif;
			//
			$rgsOptions = (array) get_option( $optName, self::$gDefOptions );
			//------------------
			//echo '<pre>IN::: '. print_r($rgsOptions, TRUE).'</pre>';

			//$rgsOptions = wp_parse_args( $rgsOptions, $defOptions );
			$rgsOptions = self::parseArgs_fn( $rgsOptions, self::$gDefOptions );

			//echo '<pre>OUT::: '. print_r($rgsOptions, TRUE).'</pre>';

			//die('DEBUG');
			//------------------
			return $rgsOptions;
			//------------------
		}
		//---------------------------------------------------------------
		// Settings initialization.
		//---------------------------------------------------------------
		public function initSettings_fn() 
		{
			$pageID = RGS_Company::getAdminMenuId_fn();
			$rgsOptionID = self::getOptionName_fn();
			$args = array(
				'sanitize_callback' => array( __CLASS__, 'settingsPageValidate_fn' )
			);
			register_setting( $pageID, $rgsOptionID );
			//register_setting( $pageID, $rgsOptionID, $args );
			register_setting( $pageID . '_chart', $rgsOptionID, $args  );
			
			 // 
			add_settings_section(
				'rgsSectionChart',
				__( 'Chart Options', self::getTD_fn() ), 
				array(__CLASS__, 'sectionChartCallback_fn'),
				$pageID . '_chart'
			);

			// Register a new field
			add_settings_field(
				'themeChoice', 
				__( 'Theme', self::getTD_fn() ),
				array(__CLASS__, 'fieldSelectThemeCallback_fn'),
				$pageID . '_chart',
				'rgsSectionChart',
				array(
					'label_for'         => 'themeChoice',
					'class'             => 'campaings_row',
					'_custom_data' 		=> 'custom',
				)
			);
			// Register a new field
			add_settings_field(
				'displayAllInRadar', 
				__( 'Display All In Radar', self::getTD_fn() ),
				array(__CLASS__, 'fieldDisplayAllInRadarCallback_fn'),
				$pageID . '_chart',
				'rgsSectionChart',
				array(
					'label_for'         => 'displayAllInRadar',
					'class'             => 'campaings_row',
					'_custom_data' 		=> 'custom',
				)
			);
			//-----------------
			// FORMS
			//-----------------
			register_setting( $pageID . '_form', $rgsOptionID );
			//
			add_settings_section(
				'rgsSectionForm',
				__( 'Default Form', self::getTD_fn() ), 
				array(__CLASS__, 'sectionFormCallback_fn'),
				$pageID . '_form'
			);
			//
			add_settings_field(
				'formChoice', 
				__( 'Form', self::getTD_fn() ),
				array(__CLASS__, 'fieldSelectFormCallback_fn'),
				$pageID . '_form',
				'rgsSectionForm',
				array(
					'label_for'         => 'formChoice',
					'class'             => 'campaings_row'
				)
			);
			//-----------------
			// SECTIONS COLORS
			//-----------------
			register_setting( $pageID . '_colors', $rgsOptionID );
			//
			add_settings_section(
				'rgsSectionColors',
				__( 'Architecture colors', self::getTD_fn() ), 
				array(__CLASS__, 'sectionColorsCallback_fn'),
				$pageID . '_colors'
			);
			// GET OPTIONS
			$rgsOptions = self::getOption_fn();
			$formChoice = $rgsOptions['formChoice'];
			//
			$formArchitect = RGS_FormStats::getFormArchitect_fn($formChoice);
			//
			$qPos = 0;
			foreach($formArchitect as $ftId => $formThemeArr):
				//
				add_settings_field(
					'colorSection_' . $ftId, 
					__( $formThemeArr['theme'], self::getTD_fn() ),
					array(__CLASS__, 'colorFieldSectionCallback_fn'),
					$pageID . '_colors',
					'rgsSectionColors',
					array(
						'label_for'         => 'colorSection_' . $ftId,
						'class'             => 'sectionPicker',
						'_custom_data' 		=> 'custom',
					)
				);
				//
				$theFormThemeQ = $formThemeArr['questions'];
				//
				foreach($theFormThemeQ as $formQId => $formQ):
					/*$settings['colorQuestion_' . $qPos ] = array_rand( $colors, 1 );*/
					//
					add_settings_field(
						'colorQuestion_' . $qPos, 
						__( $formQ, self::getTD_fn() ),
						array(__CLASS__, 'colorFieldQuestionCallback_fn'),
						$pageID . '_colors',
						'rgsSectionColors',
						array(
							'label_for'         => 'colorQuestion_' . $qPos,
							'class'             => 'questionPicker',
							'_custom_data' 		=> 'custom',
						)
					);
					//
					$qPos ++;
				endforeach;
				
			endforeach;
			
			//return $settings;
			//
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		// Settings usage
		//---------------------------------------------------------------
		public function useSettings_fn() 
		{
			
		}
		//---------------------------------------------------------------
		//---------------------------------------------------------------
		// SETTINGS CALLBACK SECTIONS
		//---------------------------------------------------------------
		static function sectionChartCallback_fn( $args )
		{
			?>
    		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'The light theme is enabled by default implicitly.',  self::getTD_fn() ); ?></p>
    	<?php
		}
		//---------------------------------------------------------------
		static function sectionFormCallback_fn( $args )
		{
			?>
    		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Select the default form for companies',  self::getTD_fn() ); ?></p>
    	<?php
		}
		//---------------------------------------------------------------
		static function sectionColorsCallback_fn( $args )
		{
			?>
    		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Select the colors of the architecture',  self::getTD_fn() ); ?></p>
    	<?php
		}
		//---------------------------------------------------------------
		// SETTINGS CALLBACK FIELDS
		//---------------------------------------------------------------
		function fieldSelectThemeCallback_fn( $args ) 
		{
			$cDatas = $args['_custom_data'];
			$labelFor = $args['label_for'];
			// Get the value of the setting we've registered with register_setting()
			$options = self::getOption_fn();
			//
			if( ! isset($options[ $labelFor ]) ):
				$options[ $labelFor ] = 'light';
			endif;
			//
			?>
			<select
					id="<?php echo esc_attr( $labelFor ); ?>"
					data-custom="<?php echo esc_attr( $cDatas ); ?>"
					name="<?php echo self::getOptionName_fn(); ?>[<?php echo esc_attr( $labelFor ); ?>]">
				<option value="light" <?php echo ( selected( $options[ $labelFor ], 'light', false ) ); ?>>
					<?php esc_html_e( 'Light Theme', self::getTD_fn() ); ?>
				</option>
				<option value="dark" <?php echo ( selected( $options[ $labelFor ], 'dark', false ) ); ?>>
					<?php esc_html_e( 'Dark Theme', self::getTD_fn()); ?>
				</option>
			</select>
			<?php
		}
		//---------------------------------------------------------------
		function fieldDisplayAllInRadarCallback_fn( $args ) 
		{
			$labelFor = $args['label_for'];
			// Get the value of the setting we've registered with register_setting()
			$options = self::getOption_fn();
			//
			//self::$gDefOptions
			$val = isset($options[ $labelFor ]) ? $options[ $labelFor ]: 0;
			//
			?>
			<input id="<?php echo esc_attr( $labelFor ); ?>" type="checkbox" name="<?php echo self::getOptionName_fn(); ?>[<?php echo esc_attr( $labelFor ); ?>]" value="1" <?php checked(1, $val, true); ?> />
			<?php
		}
		// FORM
		//---------------------------------------------------------------
		function fieldSelectFormCallback_fn( $args ) 
		{
			//
			$labelFor = $args['label_for'];
			// Get the value of the setting we've registered with register_setting()
			$options = self::getOption_fn();
			//
			$list = RGS_CompanySingle::getFormsList_fn();
			//
			if($list):
			
				if( ! isset($options[ $labelFor ]) ):
					// Set the first key from an associative array 
					$options[ $labelFor ] = ! empty($list) ? key($list) : '';
				endif;
				// ?>
				<select
						id="<?php echo esc_attr( $labelFor ); ?>"
						name="<?php echo self::getOptionName_fn(); ?>[<?php echo esc_attr( $labelFor ); ?>]">

					<?php
					foreach ( $list as $theId => $theName ): ?>

						<option value="<?php echo $theId; ?>" <?php echo ( selected( $options[ $labelFor ], "$theId", false ) ); ?>>
						<?php esc_html_e( $theName, self::getTD_fn() ); ?>
					</option>

					<?php endforeach; ?>

				</select>
			<?php
			endif;
		}
		//---------------------------------------------------------------
		static function colorFieldSectionCallback_fn( $args )
		{
			$options = self::getOption_fn();
			$cDatas = $args['_custom_data'];
			$labelFor = $args['label_for'];
			$colors = RGS_CompanySettings::getColorsByName_fn();
			//
			if($colors):
				if( ! isset($options[ $labelFor ]) ):
					$options[ $labelFor ] = ! empty($colors) ? key($colors) : '';
				endif;
				// ?>
				<input type="text" id="<?php echo esc_attr( $labelFor ); ?>"
						name="<?php echo self::getOptionName_fn(); ?>[<?php echo esc_attr( $labelFor ); ?>]" value="<?php echo $options[ $labelFor ]; ?>" class="color-field" data-alpha="true">
			<?php
			endif;
			//
		}
		//---------------------------------------------------------------
		static function colorFieldQuestionCallback_fn( $args )
		{
			$options = self::getOption_fn();
			$cDatas = $args['_custom_data'];
			$labelFor = $args['label_for'];
			$colors = RGS_CompanySettings::getColorsByName_fn();
			//
			if($colors):
				//
				if( ! isset($options[ $labelFor ]) ):
					$options[ $labelFor ] = ! empty($colors) ? key($colors) : ''; //array_rand( $colors, 1 );
				endif;
				// ?>
				<input type="text" id="<?php echo esc_attr( $labelFor ); ?>"
						name="<?php echo self::getOptionName_fn(); ?>[<?php echo esc_attr( $labelFor ); ?>]" value="<?php echo $options[ $labelFor ]; ?>" class="color-field" data-alpha="true">
			<?php
			endif;
			//
		}
		//---------------------------------------------------------------
		static function getSelectForm_fn( $name ='TEST', $value ='' ) 
		{
			//
			$labelFor = 'formChoice';
			// Get the value of the setting we've registered with register_setting()
			$options = self::getOption_fn();
			//
			$list = RGS_CompanySingle::getFormsList_fn();
			
			if($list):
			
				if( ! isset($options[ $labelFor ]) ):
					// Set the first key from an associative array 
					$options[ $labelFor ] = ! empty($list) ? key($list) : '';
				endif;
				
				if( empty( $value )) :
					$value = $options[ $labelFor ];
				endif;
				// ?>
				<select
						id="<?php echo esc_attr( $labelFor ); ?>"
						name="<?php echo $name; ?>">

					<?php
					foreach ( $list as $theId => $theName ): ?>

					<option value="<?php echo $theId; ?>" <?php echo ( selected( $value, "$theId", false ) ); ?>>
						<?php esc_html_e( $theName, self::getTD_fn() ); ?>
					</option>

					<?php endforeach; ?>

				</select>
			<?php
			endif;
		}
		
		//---------------------------------------------------------------
		// SETTINGS CALLBACK VALIDATES
		//---------------------------------------------------------------
		static function settingsPageValidate_fn($input) 
		{
			#echo '<pre>input SRC::: '. print_r($input, TRUE).'</pre>';

			if( ! isset( $input['displayAllInRadar']) ):
				$input['displayAllInRadar'] = 0;
			endif;

			#echo '<pre>input UPDATE::: '. print_r($input, TRUE).'</pre>';
			#echo '<pre>POST::: '. print_r($_POST, TRUE).'</pre>';

			#die('DEBUG');

			return $input;
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
		// COLORS
		//---------------------------------------------------------------
		static function getSectionsColors_fn()
		{
			$options = self::getOption_fn();
			$list = array();
			
			for ($i = 0; $i < 6; $i++) :
				$id = 'colorSection_' . $i;
				$color = $options[$id];
				$list[$i] = $color;
			endfor;
			
			return $list; 
		}
		//---------------------------------------------------------------
		static function getQuestionsColors_fn()
		{
			$options = self::getOption_fn();
			$list = array();
			
			for ($i = 0; $i < 12; $i++) :
				$id = 'colorQuestion_' . $i;
				$color = $options[$id];
				$list[$i] = $color;
			endfor;
			
			return $list; 
		}
		//---------------------------------------------------------------
		static function getSelectColors_fn()
		{
			$cName = array();
			$colors = self::getColorsList_fn();
			
			// ?>
				<select
						id="<?php echo 'color'; ?>"
						name="<?php echo 'color'; ?>">

					<?php
					for ($i = 0; $i < count($colors); $i+=2) : ?>

					<option value="<?php echo $colors[$i]; ?>" <?php echo ( selected( $value, "$colors[$i]", false ) ); ?>>
						<?php esc_html_e( $colors[$i+1], self::getTD_fn() ); ?>
					</option>

					<?php endfor; ?>

				</select>
			<?php
		}
		//---------------------------------------------------------------
		static function getColorsByName_fn()
		{
			$cName = array();
			$colors = self::getColorsList_fn();
			
			for ($i = 0; $i < count($colors); $i+=2) :
				$cName[$colors[$i]] = $colors[$i+1];
			endfor;
			
			return $cName;
		}
		//---------------------------------------------------------------
		static function getColorsByCode_fn()
		{
			$cCode = array();
			$colors = self::getColorsList_fn();
			
			for ($i = 0; $i < count($colors); $i+=2) :
				$cName[$colors[$i+1]] = $colors[$i];
			endfor;
			
			return $cCode;
		}
		//---------------------------------------------------------------
		static function getColorsList_fn()
		{
			// HTML color values and names
			return array(
				"800000", "Maroon",
				"8B0000", "DarkRed",
				"B22222", "FireBrick",
				"FF0000", "Red",
				"FA8072", "Salmon",
				"FF6347", "Tomato",
				"FF7F50", "Coral",
				"FF4500", "OrangeRed",
				"D2691E", "Chocolate",
				"F4A460", "SandyBrown",
				"FF8C00", "DarkOrange",
				"FFA500", "Orange",
				"B8860B", "DarkGoldenrod",
				"DAA520", "Goldenrod",
				"FFD700", "Gold",
				"808000", "Olive",
				"FFFF00", "Yellow",
				"9ACD32", "YellowGreen",
				"ADFF2F", "GreenYellow",
				"7FFF00", "Chartreuse",
				"7CFC00", "LawnGreen",
				"008000", "Green",
				"00FF00", "Lime",
				"32CD32", "LimeGreen",
				"00FF7F", "SpringGreen",
				"00FA9A", "MediumSpringGreen",
				"40E0D0", "Turquoise",
				"20B2AA", "LightSeaGreen",
				"48D1CC", "MediumTurquoise",
				"008080", "Teal",
				"008B8B", "DarkCyan",
				"00FFFF", "Aqua",
				"00FFFF", "Cyan",
				"00CED1", "DarkTurquoise",
				"00BFFF", "DeepSkyBlue",
				"1E90FF", "DodgerBlue",
				"4169E1", "RoyalBlue",
				"000080", "Navy",
				"00008B", "DarkBlue",
				"0000CD", "MediumBlue",
				"0000FF", "Blue",
				"8A2BE2", "BlueViolet",
				"9932CC", "DarkOrchid",
				"9400D3", "DarkViolet",
				"800080", "Purple",
				"8B008B", "DarkMagenta",
				"FF00FF", "Fuchsia",
				"FF00FF", "Magenta",
				"C71585", "MediumVioletRed",
				"FF1493", "DeepPink",
				"FF69B4", "HotPink",
				"DC143C", "Crimson",
				"A52A2A", "Brown",
				"CD5C5C", "IndianRed",
				"BC8F8F", "RosyBrown",
				"F08080", "LightCoral",
				"FFFAFA", "Snow",
				"FFE4E1", "MistyRose",
				"E9967A", "DarkSalmon",
				"FFA07A", "LightSalmon",
				"A0522D", "Sienna",
				"FFF5EE", "SeaShell",
				"8B4513", "SaddleBrown",
				"FFDAB9", "Peachpuff",
				"CD853F", "Peru",
				"FAF0E6", "Linen",
				"FFE4C4", "Bisque",
				"DEB887", "Burlywood",
				"D2B48C", "Tan",
				"FAEBD7", "AntiqueWhite",
				"FFDEAD", "NavajoWhite",
				"FFEBCD", "BlanchedAlmond",
				"FFEFD5", "PapayaWhip",
				"FFE4B5", "Moccasin",
				"F5DEB3", "Wheat",
				"FDF5E6", "Oldlace",
				"FFFAF0", "FloralWhite",
				"FFF8DC", "Cornsilk",
				"F0E68C", "Khaki",
				"FFFACD", "LemonChiffon",
				"EEE8AA", "PaleGoldenrod",
				"BDB76B", "DarkKhaki",
				"F5F5DC", "Beige",
				"FAFAD2", "LightGoldenrodYellow",
				"FFFFE0", "LightYellow",
				"FFFFF0", "Ivory",
				"6B8E23", "OliveDrab",
				"556B2F", "DarkOliveGreen",
				"8FBC8F", "DarkSeaGreen",
				"006400", "DarkGreen",
				"228B22", "ForestGreen",
				"90EE90", "LightGreen",
				"98FB98", "PaleGreen",
				"F0FFF0", "Honeydew",
				"2E8B57", "SeaGreen",
				"3CB371", "MediumSeaGreen",
				"F5FFFA", "Mintcream",
				"66CDAA", "MediumAquamarine",
				"7FFFD4", "Aquamarine",
				"2F4F4F", "DarkSlateGray",
				"AFEEEE", "PaleTurquoise",
				"E0FFFF", "LightCyan",
				"F0FFFF", "Azure",
				"5F9EA0", "CadetBlue",
				"B0E0E6", "PowderBlue",
				"ADD8E6", "LightBlue",
				"87CEEB", "SkyBlue",
				"87CEFA", "LightskyBlue",
				"4682B4", "SteelBlue",
				"F0F8FF", "AliceBlue",
				"708090", "SlateGray",
				"778899", "LightSlateGray",
				"B0C4DE", "LightsteelBlue",
				"6495ED", "CornflowerBlue",
				"E6E6FA", "Lavender",
				"F8F8FF", "GhostWhite",
				"191970", "MidnightBlue",
				"6A5ACD", "SlateBlue",
				"483D8B", "DarkSlateBlue",
				"7B68EE", "MediumSlateBlue",
				"9370DB", "MediumPurple",
				"4B0082", "Indigo",
				"BA55D3", "MediumOrchid",
				"DDA0DD", "Plum",
				"EE82EE", "Violet",
				"D8BFD8", "Thistle",
				"DA70D6", "Orchid",
				"FFF0F5", "LavenderBlush",
				"DB7093", "PaleVioletRed",
				"FFC0CB", "Pink",
				"FFB6C1", "LightPink",
				"000000", "Black",
				"696969", "DimGray",
				"808080", "Gray",
				"A9A9A9", "DarkGray",
				"C0C0C0", "Silver",
				"D3D3D3", "LightGrey",
				"DCDCDC", "Gainsboro",
				"F5F5F5", "WhiteSmoke",
				"FFFFFF", "White"
			);

		}
		//---------------------------------------------------------------
		static function getRandColor_fn()
		{
			$colors = self::getColorsByName_fn();
			$randPos = array_rand( $colors, 1 );
			return array( 'code' => $randPos, 'name' => $colors[$randPos] );
		}
		//---------------------------------------------------------------
		static function parseArgs_fn( &$a, $b ) 
		{
			$a = (array) $a;
			$b = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = meks_wp_parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}
			return $result;
		}
		//---------------------------------------------------------------
		
	};
	//-------------------------------------------------------------------
	if( ! function_exists( 'rgsCompanySettings_fn' ) ):
		function rgsCompanySettings_fn() 
		{
			return RGS_CompanySettings::getInstance_fn();
		};
	endif;
	//-------------------------------------------------------------------
	if( ! isset( $GLOBALS[ 'RGS_CompanySettings' ] ) ):
		$GLOBALS[ 'RGS_CompanySettings' ] = rgsCompanySettings_fn();
	endif;
	//-------------------------------------------------------------------
endif;
//-----------------------------------------------------------------------