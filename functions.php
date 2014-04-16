<?php

require_once(STYLESHEETPATH . '/inc/lightbox/lightbox.php');
require_once(STYLESHEETPATH . '/inc/artguides/artguides.php');
require_once(STYLESHEETPATH . '/inc/artists/artists.php');
require_once(STYLESHEETPATH . '/inc/artworks/artworks.php');
require_once(STYLESHEETPATH . '/inc/geolocator/geolocator.php');
require_once(STYLESHEETPATH . '/inc/views.php');

function afdm_setup() {
	load_child_theme_textdomain('arteforadomuseu', get_stylesheet_directory() . '/languages');
	add_theme_support('post-thumbnails');
	add_image_size('page-featured', 680, 270, true);
	add_image_size('featured-squared', 400, 400, true);
	add_image_size('featured-rectangle', 388, 150, true);
}
add_action('after_setup_theme', 'afdm_setup');

function afdm_scripts() {

	wp_deregister_script('jeo-site');

	wp_enqueue_style('afdm-main', get_stylesheet_directory_uri() . '/css/main.css', array(), '1.7');
	wp_enqueue_script('responsive-nav', get_stylesheet_directory_uri(). '/lib/responsive-nav.min.js', '', '1.0');
	wp_enqueue_script('afdm', get_stylesheet_directory_uri(). '/js/arteforadomuseu.js', array('responsive-nav', 'shadowbox'), '0.1.9');
}

function afdm_after_markers_scripts() {

	if(!is_single()) {
		wp_enqueue_script('afdm-filter', get_stylesheet_directory_uri() . '/js/arteforadomuseu.filterCategories.js', array('jquery', 'jeo.markers'), '0.0.5');
		wp_localize_script('afdm-filter', 'afdmFilter', array(
			'categories' => array(
				array(
					'slug' => 'colaborativo',
					'title' => 'Apenas obras colaborativas'
				)
			)
		));
	}
}

function afdm_register_lib() {
	wp_register_style('jquery-wysiwyg', get_stylesheet_directory_uri() . '/lib/jquery.wysiwyg.css');
	wp_register_script('jquery-wysiwyg', get_stylesheet_directory_uri() . '/lib/jquery.wysiwyg.js', array('jquery'));
	wp_register_script('jquery-jeditable', get_stylesheet_directory_uri() . '/lib/jquery.jeditable.mini.js', array('jquery'));
	wp_register_script('jquery-jeditable-wysiwyg', get_stylesheet_directory_uri() . '/lib/jquery.jeditable.wysiwyg.js', array('jquery', 'jquery-jeditable', 'jquery-wysiwyg'));
	wp_register_script('jquery-autosize', get_stylesheet_directory_uri() . '/lib/jquery.autosize-min.js', array('jquery'), '1.16.7');
	wp_register_script('jquery-ui-datepicker-pt-BR', get_stylesheet_directory_uri() . '/lib/jquery.ui.datepicker.pt-BR.js', array('jquery-ui-datepicker'));
	wp_register_script('jquery-chosen', get_stylesheet_directory_uri() . '/lib/jquery.chosen.min.js', array('jquery'), '0.9.14');
	wp_register_style('jquery-chosen', get_stylesheet_directory_uri() . '/lib/chosen.css');
	wp_deregister_script('jquery-form');
	wp_register_script('jquery-form', get_stylesheet_directory_uri() . '/lib/jquery.form.js', array('jquery'), '3.34.0-test1');

	wp_register_script('shadowbox', get_stylesheet_directory_uri() . '/lib/shadowbox/shadowbox.js', array('jquery'), '3.0.3');
	wp_enqueue_style('shadowbox', get_stylesheet_directory_uri() . '/lib/shadowbox/shadowbox.css');

	wp_register_style('jquery-tag-it', get_stylesheet_directory_uri() . '/lib/jquery.tagit.css');
	wp_register_script('jquery-tag-it', get_stylesheet_directory_uri() . '/lib/tag-it.min.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-autocomplete'), '2.0');
}

add_action('wp_enqueue_scripts', 'afdm_register_lib', 10);
add_action('wp_enqueue_scripts', 'afdm_scripts', 10);
add_action('jeo_markers_enqueue_scripts', 'afdm_scripts', 10);
add_action('admin_footer', 'afdm_register_lib', 10);

function afdm_marker_extent() {
	return true;
}
add_action('jeo_use_marker_extent', 'afdm_marker_extent');

add_filter('show_admin_bar', '__return_false');

function afdm_prevent_admin_access() {
	if (!current_user_can('edit_others_posts') && !defined('DOING_AJAX')) {
		wp_redirect(home_url());
		exit();
	}
}
add_action('admin_init', 'afdm_prevent_admin_access', 0);

function afdm_get_user_menu() {
	?>
	<div class="user-meta hide-if-mobile">
		<?php
		if(!is_user_logged_in()) :
			?>
			<span class="dropdown-title login"><span class="lsf icon">&#xE087;</span> <?php _e('Login', 'arteforadomuseu'); ?> <span class="lsf arrow">&#xE03e;</span></span>
			<div class="dropdown-content">
				<div class="login-content">
					<p><?php _e('Login with: ', 'arteforadomuseu'); ?></p>
					<?php do_action('oa_social_login'); ?>
				</div>
			</div>
			<?php
		else :
			?>
			<span class="dropdown-title login"><span class="lsf icon">&#xE137;</span> <span class="user-name"><?php echo wp_get_current_user()->display_name; ?></span> <span class="lsf arrow">&#xE03e;</span></span>
			<div class="dropdown-content">
				<div class="logged-in">
					<p><?php _e('Hello', 'arteforadomuseu'); ?>, <?php echo wp_get_current_user()->display_name; ?>. <a class="logout" href="<?php echo wp_logout_url(home_url()); ?>" title="<?php _e('Logout', 'arteforadomuseu'); ?>"><?php _e('Logout', 'arteforadomuseu'); ?> <span class="lsf">&#xE088;</span></a></p>
					<ul class="user-actions">
						<?php do_action('afdm_logged_in_user_menu_items'); ?>
						<?php if(current_user_can('edit_others_posts')) : ?>
							<li><a href="<?php echo get_admin_url(); ?>"><?php _e('Dashboard', 'arteforadomuseu'); ?></a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<?php
		endif;
	?>
	</div>
	<?php
}

function afdm_city_not_found_message() {
	global $wp_query;
	if(get_query_var('city_not_found')) {
		?>
		<!-- <div class="content-message">
			<p>
				<?php _e('We couldn\'t find anything for your city.', 'arteforadomuseu'); ?><br />
				<?php _e('Showing all cities results', 'arteforadomuseu'); ?>
			</p>
			<?php if(is_user_logged_in()) { ?>
				<p><a href="#" class="button add_artwork"><?php _e('Click here to add an OER', 'arteforadomuseu'); ?></a></p>
			<?php } else { ?>
				<p><a href="#"><?php _e('Login to submit an OER!', 'arteforadomuseu'); ?></a></p>
			<?php } ?>
		</div> -->
		<?php
	}
}

add_action('afdm_before_content', 'afdm_city_not_found_message');

function afdm_flush_rewrite() {
	global $pagenow;
	if(is_admin() && $_REQUEST['activated'] && $pagenow == 'themes.php') {
		global $wp_rewrite;
		$wp_rewrite->init();
		$wp_rewrite->flush_rules();
	}
}
add_action('init', 'afdm_flush_rewrite');

function bhasia_artist_permalink($url, $post) {

	if(get_post_type($post->ID) == 'artist') {
		$ids = get_post_meta($post->ID, '_artworks');
		if($ids) {
			$url = get_permalink(array_shift($ids));
		}	
	}

	return $url;

}
add_filter('post_type_link', 'bhasia_artist_permalink', 10, 2);
//


//----customizer functions----//
function bh_customize_register( $wp_customize ) {
	 $wp_customize->add_setting( 'bh_logo' ); // Add setting for logo uploader

	 // Add control for logo uploader (actual uploader)
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bh_logo', array(
        'label'    => __( 'Upload Logo', 'bh' ),
        'section'  => 'title_tagline',
        'settings' => 'bh_logo',
    ) ) );

    $wp_customize->add_setting(
	    'bh_bg_color',
	    array(
	        'default'     => '#000000'
	    )
	);

	$wp_customize->add_control(
	    new WP_Customize_Color_Control(
	        $wp_customize,
	        'bg_color',
	        array(
	            'label'      => __( 'Background Color', 'bh' ),
	            'section'    => 'colors',
	            'settings'   => 'bh_bg_color'
	        )
	    )
	);
}
add_action( 'customize_register', 'bh_customize_register' );

function bh_customizer_css() {
    ?>
    <style type="text/css">
        #masthead, 
        .displaying-map #masthead, 
        #masthead #masthead-nav #searchform > div, 
        .displaying-map #masthead #masthead-nav #searchform > div  {
    		background-color: <?php echo get_theme_mod( 'bh_bg_color' ); ?>; 
    	}
    </style>
    <?php
}
add_action( 'wp_head', 'bh_customizer_css' );

register_sidebar( array(
    'name'         => __( 'header languages' ),
    'id'           => 'header-1',
    'description'  => __( 'Widgets in this area will be shown on the header side.' ),
    'before_title' => '<h1>',
    'after_title'  => '</h1>',
) );


function mira_lang_array($array) {
	if(is_array($array)) {
		foreach($array as $language => $translation) {
			$string .= "<!--:".$language."-->".$translation."<!--:-->";
		}
		global $q_config;
		return qtrans_use($q_config['language'], $string, true);
	}
}
function mira_lang_vocab_lic($array) {
	foreach ($array as $string) {
		switch($string) {
			case "cc_by" :
			$result_array[] = __('CC-BY', 'arteforadomuseu');
			break;

			case "cc_by_sa" :
			$result_array[] = __('CC-BY-SA', 'arteforadomuseu');
			break;

			case "cc_by_nc_sa" :
			$result_array[] = __('CC-BY-NC-SA', 'arteforadomuseu');
			break;

			case "cc_by_nc" :
			$result_array[] = __('CC-BY-NC', 'arteforadomuseu');
			break;

			case "cc_by_nd" :
			$result_array[] = __('CC-BY-ND', 'arteforadomuseu');
			break;

			case "cc_by_nc_nd" :
			$result_array[] = __('CC-BY-NC-ND', 'arteforadomuseu');
			break;

			case "copyright" :
			$result_array[] = __('Copyright', 'arteforadomuseu');
			break;

			case "public_domain" :
			$result_array[] = __('Public domain', 'arteforadomuseu');
			break;

			case "none" :
			$result_array[] = __('None', 'arteforadomuseu');
			break;

			case "other" :
			$result_array[] = __('Other', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_lang($array) {
	foreach ($array as $string) {
		switch($string) {
			case "aymara" :
			$result_array[] = __('Aymara', 'arteforadomuseu');
			break;

			case "creole" :
			$result_array[] = __('Creole', 'arteforadomuseu');
			break;

			case "spanish" :
			$result_array[] = __('Spanish', 'arteforadomuseu');
			break;

			case "french" :
			$result_array[] = __('French', 'arteforadomuseu');
			break;

			case "guarani" :
			$result_array[] = __('Guarani', 'arteforadomuseu');
			break;

			case "dutch" :
			$result_array[] = __('Dutch', 'arteforadomuseu');
			break;

			case "english" :
			$result_array[] = __('English', 'arteforadomuseu');
			break;

			case "portuguese" :
			$result_array[] = __('Portuguese', 'arteforadomuseu');
			break;

			case "quechua" :
			$result_array[] = __('Quechua', 'arteforadomuseu');
			break;

			case "others" :
			$result_array[] = __('Others', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_res($array) {
	foreach ($array as $string) {
		switch($string) {
			case "animation" :
			$result_array[] = __('Animation', 'arteforadomuseu');
			break;

			case "audio" :
			$result_array[] = __('Audio', 'arteforadomuseu');
			break;

			case "digital_class" :
			$result_array[] = __('Digital class', 'arteforadomuseu');
			break;

			case "game" :
			$result_array[] = __('Game', 'arteforadomuseu');
			break;

			case "image" :
			$result_array[] = __('Image', 'arteforadomuseu');
			break;

			case "digital_book" :
			$result_array[] = __('Digital book', 'arteforadomuseu');
			break;

			case "simulation" :
			$result_array[] = __('Simulation', 'arteforadomuseu');
			break;

			case "software" :
			$result_array[] = __('Software', 'arteforadomuseu');
			break;

			case "text" :
			$result_array[] = __('Text', 'arteforadomuseu');
			break;

			case "video" :
			$result_array[] = __('Video', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_acad($array) {
	foreach ($array as $string) {
		switch($string) {
			case "0" :
			$result_array[] = __('0 - Early childhood education', 'arteforadomuseu');
			break;

			case "1" :
			$result_array[] = __('1 - Primary', 'arteforadomuseu');
			break;

			case "2" :
			$result_array[] = __('2 - Lower secondary', 'arteforadomuseu');
			break;

			case "3" :
			$result_array[] = __('3 - Upper secondary', 'arteforadomuseu');
			break;

			case "4" :
			$result_array[] = __('4 - Post-secondary non-tertiary', 'arteforadomuseu');
			break;

			case "5" :
			$result_array[] = __('5 - Short-cycle tertiary', 'arteforadomuseu');
			break;

			case "6" :
			$result_array[] = __('6 - Bachelor or equivalent', 'arteforadomuseu');
			break;

			case "7" :
			$result_array[] = __('7 - Master or equivalent', 'arteforadomuseu');
			break;

			case "8" :
			$result_array[] = __('8 - Doctoral or equivalent', 'arteforadomuseu');
			break;

			case "9" :
			$result_array[] = __('9 - Not elsewhere classified', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_subj($array) {
	foreach ($array as $string) {
		switch($string) {
			case "philosophy" :
			$result_array[] = __('Philosophy', 'arteforadomuseu');
			break;

			case "sociology" :
			$result_array[] = __('Sociology', 'arteforadomuseu');
			break;

			case "geography" :
			$result_array[] = __('Geography', 'arteforadomuseu');
			break;

			case "history" :
			$result_array[] = __('History', 'arteforadomuseu');
			break;

			case "biology" :
			$result_array[] = __('Biology', 'arteforadomuseu');
			break;

			case "science" :
			$result_array[] = __('Science', 'arteforadomuseu');
			break;

			case "chemistry" :
			$result_array[] = __('Chemistry', 'arteforadomuseu');
			break;

			case "physics" :
			$result_array[] = __('Physics', 'arteforadomuseu');
			break;

			case "art" :
			$result_array[] = __('Art', 'arteforadomuseu');
			break;

			case "spanish" :
			$result_array[] = __('Spanish', 'arteforadomuseu');
			break;

			case "english" :
			$result_array[] = __('English', 'arteforadomuseu');
			break;

			case "portuguese" :
			$result_array[] = __('Portuguese', 'arteforadomuseu');
			break;

			case "mathematics" :
			$result_array[] = __('Mathematics', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_out($array) {
	foreach ($array as $string) {
		switch($string) {
			case "download" :
			$result_array[] = __('Download data (JSON, XML, CSV)', 'arteforadomuseu');
			break;

			case "output" :
			$result_array[] = __('Output services (API, OAI, SQI, SRU, SPI)', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}
function mira_lang_vocab_in($array) {
	foreach ($array as $string) {
		switch($string) {
			case "contact" :
			$result_array[] = __('Contact (form/email)', 'arteforadomuseu');
			break;

			case "join" :
			$result_array[] = __('Join (create account)', 'arteforadomuseu');
			break;

			case "comment" :
			$result_array[] = __('Comment (vote, tag)', 'arteforadomuseu');
			break;

			case "contribute" :
			$result_array[] = __('Contribute (post/moderated post)', 'arteforadomuseu');
			break;

			case "community" :
			$result_array[] = __('Community (forums, COP, bulletin boards)', 'arteforadomuseu');
			break;
		}
	}
	$result = implode('<br>', $result_array);
	return $result;
}