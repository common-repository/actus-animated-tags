<?php
/**
 * 
 * @package     Actus_Animated_Tags
 *
 * Plugin Name: ACTUS Animated Tags
 * Plugin URI:  https://wp.actus.works/actus-animated-tags/
 * Description: Algorithmically Animated Tag Cloud. Random Post Tags, in different size, position and opacity, animated horizontally in various speed, over a background image.
 * Version:     2.0.1
 * Author:      Stelios Ignatiadis
 * Author URI:  https://wp.actus.works/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


global $anit_default_style;

$anit_default_style = array(
	'name'  		=> 'Default style',
	'height'        => 300,
	'density'       => 7,
	'min_font_size' => 16,
	'max_font_size' => 80,
	//'background'    => '#777777',
	'background'    => '03.jpg',
	'color'    		=> '#ffffff',
	'taxonomies'	=> array('post_tag'),
	'words'			=> array(),
	'images'		=> array(),
	'clickable'		=> 1,
);

 

/**
 * Define Path Constants
 *
 * @since 0.1.0
 * @constant string ACTUS_THEME_DIR    Directory of the current Theme.
 * @constant string ACTUS_ANIT_NAME    Plugin Basename.
 * @constant string ACTUS_ANIT_DIR     Directory of the Plugin.
 * @constant string ACTUS_ANIT_DIR     URL of the Plugin.
 * @constant string ACTUS_ANIT_VERSION Plugin Version.
 */
function actus_anit_define_constants() {
    if ( ! defined( 'ACTUS_ANIT_NAME' ) ) {
        define( 'ACTUS_ANIT_NAME', trim( dirname( plugin_basename(__FILE__) ), '/') );
    }
    if ( ! defined( 'ACTUS_ANIT_DIR' ) ) {
        define( 'ACTUS_ANIT_DIR', plugin_dir_path( __FILE__ ) );
    }
    if ( ! defined( 'ACTUS_ANIT_URL' ) ) {
        define( 'ACTUS_ANIT_URL', plugin_dir_url( __FILE__ ) );
    }
    if ( ! defined( 'ACTUS_ANIT_VERSION' ) ) {
        define( 'ACTUS_ANIT_VERSION', '2.0.1' );
    }
}
actus_anit_define_constants();



/**
 * Actions that run during plugin activation.
 */
function activate_actus_anit() {
	require_once ACTUS_ANIT_DIR . '/includes/actus-anit-activator.php';
}
register_activation_hook( __FILE__, 'activate_actus_anit' );




/*
 * Loads the necessary CSS files
 */
function actus_anit_depedencies( ) {
    wp_enqueue_style( 
        'actus-animated-tags-styles',
        ACTUS_ANIT_URL . 'css/actus-animated-tags.css',
        false, '2.0.1', 'all'
    );
}


/*
 * The Administration Options.
 */
if ( is_admin() ) {
    require_once ACTUS_ANIT_DIR . '/includes/actus-anit-admin.php';
}




/*
 * Add settings link on plugin page
 *
 * @since 1.1.0
 */
function actus_anit_settings_link( $links ) { 
  $settings_link = '<a href="themes.php?page=actus-animated-tags.php">Settings</a>'; 
  array_unshift( $links, $settings_link ); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'actus_anit_settings_link' );





/**
 * Plugin Initialization.
 *
 * @global array  $anit_options          plugin options
 * @global array  $anit_current        plugin options (legacy)
 * @global array  $actus_anit_default_terms  default terms
 */
function actus_anit_init() {
    global $anit_options, $anit_current, $anit_terms;
    
	actus_anit_load_options();
	actus_anit_default_terms();
	
    update_option( 'ACTUS_ANIT_VERSION',    ACTUS_ANIT_VERSION );
}
// Reads the options from database
function actus_anit_load_options() {
    global $anit_options, $anit_current, $anit_terms, $anit_default_style;
	
	// load version 2.x options
	$anit_options = get_option( 'ACTUS_ANIT_array' );


	if ( ! $anit_options ) {

		if ( get_option( 'ACTUS_ANIT_height' ) ) {
		// load version 1.x options

			$anit_current = array(
				'height'         => get_option( 'ACTUS_ANIT_height' ),
				'density'        => get_option( 'ACTUS_ANIT_density' ),
				'min_font_size'  => get_option( 'ACTUS_ANIT_min_font_size' ),
				'max_font_size'  => get_option( 'ACTUS_ANIT_max_font_size' ),
				'background'     => get_option( 'ACTUS_ANIT_background' ),
				'color'     	 => get_option( 'ACTUS_ANIT_color' ),
				'plugin_dir'     => ACTUS_ANIT_URL,
				'plugin_version' => ACTUS_ANIT_VERSION,
			);
			$anit_options = array( $anit_current );

		} else {
		// version 2.x defaults

			$anit_options = array(
				$anit_default_style
			);

		}

	}
	$anit_current = $anit_options[0];
	
}
// set the default terms
function actus_anit_default_terms() {
    global $anit_options, $anit_current, $anit_terms;
	
	 $anit_terms = get_terms( array(
            'taxonomy' => 'post_tag',
            'get'      => 'all',
    ));
	
	// demo terms
    if ( $anit_terms == null ) {
        $anit_terms = array(
            0 => array( 'name' => 'ACTUS anima' ),
            1 => array( 'name' => 'animated tags' ),
            2 => array( 'name' => 'ajax' ),
            3 => array( 'name' => 'wordpress' ),
            4 => array( 'name' => 'jquery' ),
            5 => array( 'name' => 'algorithmic animation' ),
            6 => array( 'name' => 'post tags' ),
            7 => array( 'name' => 'actus' ),
            8 => array( 'name' => 'css' ),
            9 => array( 'name' => 'php' ),
            10 => array( 'name' => 'plugin' ),
        );
    }
	
}



// The Main Plugin Code
require_once ACTUS_ANIT_DIR . '/includes/actus-anit-main.php';



// INITIALIZE
add_action( 'wp_enqueue_scripts', 'actus_anit_depedencies' );
add_action( 'init', 'actus_anit_init' );
add_action( 'init', 'actus_anit_shortcodes_init' );




?>