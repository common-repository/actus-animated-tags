<?php
/**
 * The administration options.
 *
 * @package    Actus_Animated_Tags
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Loads the necessary CSS files
 */
function actus_anit_depedencies_admin() {
    global $anit_terms, $anit_options, $anit_default_style, $anit_taxonomies;
    wp_enqueue_media();
	
	
	$args = array(
	  'public'   => true,
	  '_builtin' => false
	);
	$custom_taxonomies = get_taxonomies( $args );
	$taxonomies = array('post_tag', 'category');
	foreach($custom_taxonomies as $row){
		$taxonomies[] = $row;
	}
	foreach($taxonomies as $row){
		$terms = get_terms( array(
				'taxonomy' => $row,
				'get'      => 'all',
		));
		$anit_taxonomies[ $row ] = array();
		foreach($terms as $term){
			$anit_taxonomies[ $row ][] = $term->name;
		}
	}
	
	
	// Add the color picker css file       
	wp_enqueue_style( 'wp-color-picker' ); 
	
    wp_enqueue_style( 
        'actus-animated-tags-admin-styles', 
        ACTUS_ANIT_URL . 'css/actus-animated-tags-admin.css',
        false, '2.0.1', 'all' );
    
    wp_enqueue_script(
        'actus_animated_tags_admin_script',
        ACTUS_ANIT_URL . 'js/actus-animated-tags-admin.js',
        array('jquery', 'wp-color-picker', 'actus_animated_tags_script'), '2.0.1', true);
    
    $title_nonce = wp_create_nonce( 'actus_nonce' );
    $actus_anit_params_admin = array(
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'nonce'      => $title_nonce,
        'plugin_dir' => ACTUS_ANIT_URL,
        'current' => $anit_options[0],
        'opts'    => $anit_options,
        'taxonomies' => $anit_taxonomies,
        'terms'   => $anit_terms,
        'default_style' => $anit_default_style,
    );
    wp_localize_script(
        'actus_animated_tags_admin_script',
        'actusAnitParamsAdmin', $actus_anit_params_admin );
    
}

add_action( 'admin_enqueue_scripts', 'actus_anit_depedencies' );
add_action( 'admin_enqueue_scripts', 'actus_anit_depedencies_admin' );




/*
 * Adds ACTUS menu on admin panel
 */
if ( !function_exists( 'actus_menu' ) ) {
    function actus_menu(){
        add_menu_page( 
            'ACTUS Plugins',
            'ACTUS',
            'manage_options',
            'actus-plugins',
            'actus_plugins_page',
            ACTUS_ANIT_URL . 'img/actus_white_20.png',
            66
        );
    }
    if ( is_admin() ) {
        add_action( 'admin_menu', 'actus_menu' );
    }
}
/*
 * Adds submenu on ACTUS menu
 */
if ( !function_exists( 'actus_aaws_submenu' ) ) {
    function actus_animated_tags_menu() {
        add_submenu_page(
            'actus-plugins', 
			'ACTUS Animated Tags Options', 
			'ACTUS Animated Tags', 
            'manage_options', 
			'actus-animated-tags.php', 
			'actus_animated_tags_options'
        );
    }
    if ( is_admin() ) {
        add_action( 'admin_menu', 'actus_animated_tags_menu' );
    }
}









/*
 * The ACTUS plugins page content
 */
if ( !function_exists( 'actus_plugins_page' ) ) {
    function actus_plugins_page() {
        
        // Enque styles
        wp_enqueue_style( 
            'actus-admin-styles',
            ACTUS_ANIT_URL . 'css/actus-admin.css' ,
            false, '1.0.1', 'all' );

        $actus_plugins_url = ACTUS_ANIT_DIR . '/includes/actus-plugins.php';
        include $actus_plugins_url;
        ?>

        <?php
    }
}















/*
 * Settings Page
 *
 * @global array    $anit_current  Array of plugin options.
 */
function actus_animated_tags_options() {
    global $anit_current, $anit_taxonomies;
    
	if ( ! current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    ?>
    <div class="wrap actus-settings">
        
        <!-- HEADER -->
        <div class="actus-settings-header">
            <img class="actus-logo" src="<?php echo ACTUS_ANIT_URL . 'img/actus_white.png'; ?>">
            <img class="actus-anit-title" src="<?php echo ACTUS_ANIT_URL . 'img/title.png'; ?>">
        </div>
        
        <!-- INFO -->
        <div class="actus-settings-info actus-settings-info1">
            <div class="actus-settings-info-icon">
                <img src="<?php echo ACTUS_ANIT_URL . 'img/info.png'; ?>">
            </div>
            <div class="actus-settings-info-text">
                <p>An animated tags cloud.</p>
                <p>Random Post Tags, in different size, position and opacity, animated horizontally in various speed, over a background image or color.</p>
            </div>
            <div style="clear:both"></div>
        </div>
        
        <!-- PREVIEW -->
		<div class="A-flex-wrap">
			
			<!-- STYLES LIST -->
			<div class="actus-anit-styles">
				<h2>Styles</h2>
				<div class="anit-styles-list"></div>
			</div>
			
			<!-- PREVIEW -->
			<div class="actus-settings-panel actus-settings-panel-preview">
				<h2>Preview</h2>
				<div class="actus-anit-seperator-1"></div>
				<div class="actus-anit-preview">
					<?php echo do_shortcode( '[actus_animated_tags]' ); ?>
				</div>
            	<div class="actus-anit-shortcode"></div>
			</div>
			
        </div>
            
        <!-- OPTIONS -->
        <div class="actus-settings-panel actus-settings-panel-options">
            <h2>Settings</h2>
			<div class="actus-save">SAVE</div>
            <div class="actus-saving">
                <img src="<?php echo ACTUS_ANIT_URL . 'img/gear.png'; ?>">
            </div>
            <!-- <div class="actus-anit-seperator-1"></div> -->
            <div class="actus-anit-options">
                
				<!-- name -->
                <div class="actus-anit-option actus-anit-option-name">
                    <input class="actus-anit-input actus-anit-name" 
                           name="ACTUS_ANIT_name"
                           type="text"
                           value="<?php echo $anit_current['name'] ?>">
                    <div class="label">style name</div>
                </div>
				
                <!-- height -->
                <div class="actus-anit-option actus-anit-option-height">
                    <input class="actus-anit-input actus-anit-height" 
                           name="ACTUS_ANIT_height"
                           type="number"
                           value="<?php echo $anit_current['height'] ?>">
                    <div class="label">height (pixels)</div>
                </div>
                <!-- density -->
                <div class="actus-anit-option actus-anit-option-density">
                    <input class="actus-anit-input actus-anit-density" 
                           name="ACTUS_ANIT_density"
                           type="number" 
                           value="<?php echo $anit_current['density'] ?>">
                    <div class="label">density</div>
                </div>
                <!-- clickable -->
                <div class="actus-anit-option actus-anit-option-clickable">
					<?php
					$checked = '';
					if ( $anit_current['clickable'] )
						$checked = 'checked';
					?>
                    <input class="actus-anit-input actus-anit-clickable" 
						   <?php echo $checked; ?> 
                           name="ACTUS_ANIT_clickable"
                           type="checkbox" 
                           value="<?php echo $anit_current['clickable'] ?>">
                    <div class="label">clickable terms</div>
                </div>
                <!-- terms color -->
                <div class="actus-anit-option actus-anit-option-terms-color">
                    <input class="actus-anit-input actus-anit-terms-color" 
                           name="ACTUS_ANIT_color"
                           type="text" 
                           value="<?php echo $anit_current['color'] ?>">
                    <div class="label">Terms color</div>
                </div>
				
                <!-- taxonomies -->
                <div class="actus-anit-option actus-anit-option-tax">
                    <div class="label">taxonomies</div>
					<div class="anit-flex">
						<?php
						foreach($anit_taxonomies as $key => $row){
							$clss = '';
							if ( in_array($key, $anit_current['taxonomies'] ) )
								$clss = 'selected';
							echo "<div class='row $clss'>$key</div>";
						}
						?>
					</div>
                </div>
				
                <!-- background -->
                <div class="actus-anit-option actus-anit-option-backg">
                    <div class="label">background</div>
                    <div class="actus-anit-thumbs">
						
						
						<?php $clss = 'actus-anit-add-image'; ?>
                        <div class="actus-anit-thumb <?php echo $clss ?>" alt="library">
							<span class="dashicons dashicons-format-gallery"></span>
							IMAGE FROM LIBRARY
                        </div>
						
						<?php $clss = 'actus-anit-color'; ?>
                        <div class="actus-anit-thumb <?php echo $clss ?>" alt="color">
							<span class="dashicons dashicons-color-picker"></span>
							COLOR
                        </div>
						
						<?php /*
                        <?php 
                            $clss = "actus-anit-random";
                            if ( $anit_current['background'] == 'random' ) $clss = 'actus-anit-random selected';
                        ?>
                        <div class="actus-anit-thumb <?php echo $clss ?>" alt="random">
							<span class="dashicons dashicons-randomize"></span>
							RANDOM
                        </div>
						*/ ?>
						
						
						
                        <?php 
                        for ( $n=1; $n<7; $n++ ) { 
                            $clss = "";
                            if ( $anit_current['background'] == "0" . $n . ".jpg" ) {
                                $clss = 'selected';
                            }
                        ?>
                            <div class="actus-anit-thumb <?php echo $clss ?>" 
                                 alt="0<?php echo $n ?>.jpg">
                                <img src="<?php echo ACTUS_ANIT_URL . 'img/back/0' . $n . '.jpg'; ?>">
                            </div>
                        <?php } ?>
                    </div>
					
                </div>
				
					
				
            </div>
            <div style="clear:both"></div>
            <div class="actus-anit-seperator-1"></div>
        </div>
        
        <!-- HELP -->
        <div class="actus-settings-info">
            <div class="actus-settings-info-icon">
                <img src="<?php echo ACTUS_ANIT_URL . 'img/help.png'; ?>">
            </div>
            <div class="actus-settings-info-text">
                <p>Use our widget or the shortcode <b>[actus_animated_tags]</b> to embed ACTUS Animated Tags anywhere in your website.</p>

            </div>
            <div style="clear:both"></div>
        </div>
        <div class="actus-settings-footer">
            <div class="actus">created by <a href="https://wp.actus.works" target="_blank">ACTUS anima</a></div>
            <div class="actus-sic">code & design:  <a href="mailto:sic@actus.works" target="_blank">Stelios Ignatiadis</a></div>
        </div>
        <div class="actus-settings-channel">send us your comments and suggestions to <a href="mailto:sic@actus.works" target="_blank">sic@actus.works</a></div>
    </div>
    <?php
}




/*
 * AJAX save settings
 *
 * @global array    $anit_current  Array of plugin options.
 */
add_action( 'wp_ajax_actus_anit_save', 'actus_anit_save_ajax_handler' );
function actus_anit_save_ajax_handler() {
    check_ajax_referer( 'actus_nonce' );
    //update_option( $_POST['name'], $_POST['value'] );
    update_option( 'ACTUS_ANIT_array', $_POST['options'] );
    wp_die();
}
?>