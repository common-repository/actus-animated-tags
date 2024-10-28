<?php

/**
 * The main plugin code.
 *
 * @package    Actus_Animated_Tags
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




/**
 * Executes the animation function when a shortcode is called.
 * This action is documented in includes/actus-anit-main.php
 */
function actus_anit_shortcodes_init() {
    add_shortcode( 'actus_animated_tags', 'actus_anit_animate' );
}



/**
 * Starts the execution of the script that creates the animation.
 * Generates the HTML container of the animation
 *
 * @var string  $actus_anit_id       The unique id for this instance.
 *
 * @global array    $anit_terms    The terms to be animated.
 * @global array    $anit_options  Array of plugin options.
 * @global array    $anit_current  current item options.
 */
function actus_anit_animate( $_atts = [], $content = null, $tag = '' ) {
    global $anit_terms, $anit_options, $anit_current, $anit_taxonomies;
   
	// normalize attribute keys, lowercase
    $_atts = array_change_key_case( (array) $_atts, CASE_LOWER );
 
    // override default attributes with user attributes
    $atts = shortcode_atts(
        array(
            'style' => 0,
        ), $_atts, $tag
    );
	
	// get selected style
	$anit_current = $anit_options[ $atts['style'] ];
	
	
	// get terms for current style
	$current_terms = array();
	foreach($anit_current['taxonomies'] as $row){
		$terms = get_terms( array(
				'taxonomy' => $row,
				'get'      => 'all',
		));
		foreach($terms as $term){
			$current_terms[] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
				'link' => get_term_link( $term->term_id ),
			);
		}
	}
	
	
    // Set ID and script parameters array
    $actus_anit_id = "ANIT-" . time() . rand( 10,99 );
    $actus_anit_params = array(
        'id'      => $actus_anit_id,
        'current' => $anit_current,
        'opts'    => $anit_options,
        'terms'   => $current_terms,
        'style'   => $atts['style'],
        'site_url'=> site_url(),
		'plugin_dir' => ACTUS_ANIT_URL,
    );
	
	
    // Call the animation script.
    wp_enqueue_script(
        'actus_animated_tags_script',
        ACTUS_ANIT_URL . 'js/actus-animated-tags.js',
        array('jquery'), '2.0.1', true
    );
    //
    // Send parameters to the animation script.
    wp_localize_script( 
        'actus_animated_tags_script',
        'actusAnitParams', $actus_anit_params
    );
    
    return actus_anit_html( $actus_anit_id );
}
function actus_anit_html( $id ){
    
    // Set Plugin Main HTML Container.
    $actus_anit_html = "";
    $actus_anit_html .= '<div style="clear:both"></div>';
    $actus_anit_html .= '<div id="' . $id . '" class="actus-anit">';
    $actus_anit_html .= '<div class="actus-anit-cloud"></div>';
    $actus_anit_html .= '</div>';
    $actus_anit_html .= '<div style="clear:both"></div>';
    
	return $actus_anit_html;
}




/**
 * Plugin Widget
 */
class actus_anit extends WP_Widget {

	// constructor
	function actus_anit() {
        parent::WP_Widget(
            false, $name = __('ACTUS Animated Tags Widget', 'wp_widget_actus_anit')
        );
	}

	// widget form creation
	function form($instance) {	
        // Check values
        if( $instance) {
             $title = esc_attr($instance['title']);
        } else {
             $title = '';
        }
        ?>

        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
        <?php
	}

	// widget update
	function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	// widget display
	function widget($args, $instance) {
		echo actus_anit_animate();
	}
}

// register widget
add_action( 'widgets_init', create_function( '', 'return register_widget("actus_anit");') );

