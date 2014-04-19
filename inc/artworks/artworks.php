<?php

/*
 * Arte Fora do Museu
 * Artworks
 */

class ArteForaDoMuseu_Artworks {

	var $post_type = 'post';

	var $taxonomy_slugs = array(
		'style' => 'estilos'
	);

	var $directory_uri = '';

	var $directory = '';

	function __construct() {
		add_action('jeo_init', array($this, 'setup'));
	}

	function setup() {
		$this->set_directories();
		$this->setup_views();
		$this->setup_scripts();
		$this->register_taxonomies();
		$this->setup_meta_boxes();
		$this->hook_ui_elements();
		$this->setup_ajax();
	}

	function set_directories() {
		$this->directory_uri = apply_filters('artworks_directory_uri', get_stylesheet_directory_uri() . '/inc/artworks');
		$this->directory = apply_filters('artworks_directory', get_stylesheet_directory() . '/inc/artworks');
	}

	/*
	 * Add to view system
	 */
	function setup_views() {
		add_action('afdm_views_post_types', array($this, 'register_views'));
	}

	function register_views($post_types) {
		if(!in_array($this->post_type, $post_types))
			$post_types[] = $this->post_type;

		return $post_types;
	}

	/*
	 * Scripts
	 */

	function setup_scripts() {
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
		add_action('jeo_geocode_scripts', array($this, 'geocode_scripts'));

		//++ Adds advanced search query to default query
		add_action('pre_get_posts', array($this, 'advanced_search_query'));
	}

	function scripts() {
		wp_enqueue_script('afdm-artworks', $this->directory_uri . '/js/artworks.js', array('jquery', 'afdm-lightbox', 'jquery-autosize', 'jquery-form'), '0.0.6');
		wp_localize_script('afdm-artworks', 'artworks', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'sending_msg' => __('Sending data...', 'arteforadomuseu'),
			'crunching_msg' => __('Crunching...', 'arteforadomuseu')
		));
	}

	function geocode_scripts() {
		$geocode_service = jeo_get_geocode_service();
		$gmaps_key = jeo_get_gmaps_api_key();
		if($geocode_service == 'gmaps' && $gmaps_key)
			wp_enqueue_script('google-maps-api');
		wp_enqueue_script('jeo.geocode.box');
	}

	/*
	 * Taxonomies
	 */

	function register_taxonomies() {
		$this->taxonomy_style();
	}

	function taxonomy_style() {

		$labels = array( 
			'name' => __('Styles', 'arteforadomuseu'),
			'singular_name' => __('Style', 'arteforadomuseu'),
			'search_items' => __('Search styles', 'arteforadomuseu'),
			'popular_items' => __('Popular styles', 'arteforadomuseu'),
			'all_items' => __('All styles', 'arteforadomuseu'),
			'parent_item' => __('Parent style', 'arteforadomuseu'),
			'parent_item_colon' => __('Parent style:', 'arteforadomuseu'),
			'edit_item' => __('Edit style', 'arteforadomuseu'),
			'update_item' => __('Update style', 'arteforadomuseu'),
			'add_new_item' => __('Add new style', 'arteforadomuseu'),
			'new_item_name' => __('New style name', 'arteforadomuseu'),
			'separate_items_with_commas' => __('Separate styles with commas', 'arteforadomuseu'),
			'add_or_remove_items' => __('Add or remove styles', 'arteforadomuseu'),
			'choose_from_most_used' => __('Choose from most used styles', 'arteforadomuseu'),
			'menu_name' => __('Styles', 'arteforadomuseu')
		);

		$args = array( 
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'rewrite' => array('slug' => $this->taxonomy_slugs['style'], 'with_front' => false),
			'query_var' => true,
			'show_admin_column' => true
		);

		register_taxonomy('style', array($this->post_type), $args);
	}

	/*
	 * Meta boxes
	 */

	function setup_meta_boxes() {
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_artwork'));
		add_action('admin_footer', array($this, 'admin_css'));
	}

	function admin_css() {
		wp_enqueue_style('artwork-admin', $this->directory_uri . '/css/admin.css');
	}

	// Add meta boxes
	function add_meta_boxes() {
/*		// Dimensions
		add_meta_box(
			'artwork_dimensions',
			__('Artwork dimensions', 'arteforadomuseu'),
			array($this, 'box_artwork_dimensions'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Dates
		add_meta_box(
			'artwork_dates',
			__('Artwork dates', 'arteforadomuseu'),
			array($this, 'box_artwork_dates'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Videos
		add_meta_box(
			'artwork_videos',
			__('Videos', 'arteforadomuseu'),
			array($this, 'box_artwork_videos'),
			$this->post_type,
			'advanced',
			'high'
		);
*/
		// Links
		add_meta_box(
			'artwork_links',
			__('Links', 'arteforadomuseu'),
			array($this, 'box_artwork_links'),
			$this->post_type,
			'advanced',
			'high'
		);

		// URL of the initiative
		add_meta_box(
			'artwork_urls',
			__('URL and contact', 'arteforadomuseu'),
			array($this, 'box_artwork_urls'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Place
		add_meta_box(
			'artwork_place',
			__('Place', 'arteforadomuseu'),
			array($this, 'box_artwork_place'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Organizations
		add_meta_box(
			'artwork_organizations',
			__('Organizations', 'arteforadomuseu'),
			array($this, 'box_artwork_organizations'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Languages
		add_meta_box(
			'artwork_languages',
			__('Languages', 'arteforadomuseu'),
			array($this, 'box_artwork_languages'),
			$this->post_type,
			'side',
			'low'
		);

		// Country
		add_meta_box(
			'artwork_country',
			__('Country', 'arteforadomuseu'),
			array($this, 'box_artwork_country'),
			$this->post_type,
			'advanced',
			'high'
		);

		// Licenses
		add_meta_box(
			'artwork_licenses',
			__('Licenses', 'arteforadomuseu'),
			array($this, 'box_artwork_licenses'),
			$this->post_type,
			'side',
			'low'
		);

		// Resources
		add_meta_box(
			'artwork_resources',
			__('Resources', 'arteforadomuseu'),
			array($this, 'box_artwork_resources'),
			$this->post_type,
			'side',
			'low'
		);

		// Input
		add_meta_box(
			'artwork_input',
			__('Input and output', 'arteforadomuseu'),
			array($this, 'box_artwork_input'),
			$this->post_type,
			'side',
			'low'
		);

		// Other
		add_meta_box(
			'artwork_other',
			__('Other', 'arteforadomuseu'),
			array($this, 'box_artwork_other'),
			$this->post_type,
			'advanced',
			'high'
		);

		/*
		 * Artists videos and links
		 */

		if(post_type_exists('country')) {

			// Videos
			add_meta_box(
				'artwork_videos',
				__('Videos', 'arteforadomuseu'),
				array($this, 'box_artwork_videos'),
				'country',
				'advanced',
				'high'
			);

			// Links
			add_meta_box(
				'artwork_links',
				__('Links', 'arteforadomuseu'),
				array($this, 'box_artwork_links'),
				'country',
				'advanced',
				'high'
			);

		}
	}

	function save_artwork($post_id) {

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if (defined('DOING_AJAX') && DOING_AJAX && !(defined('AFDM_ALLOWED_AJAX') && AFDM_ALLOWED_AJAX))
			return;

		if (false !== wp_is_post_revision($post_id))
			return;

//		$this->save_artwork_dimensions($post_id);
//		$this->save_artwork_dates($post_id);
//		$this->save_artwork_videos($post_id);
		$this->save_artwork_links($post_id);
		$this->save_artwork_urls($post_id);
		$this->save_artwork_place($post_id);
		$this->save_artwork_organizations($post_id);
		$this->save_artwork_languages($post_id);
		$this->save_artwork_licenses($post_id);
		$this->save_artwork_resources($post_id);
		$this->save_artwork_input($post_id);
		$this->save_artwork_other($post_id);
		$this->save_artwork_country($post_id);

		if(defined('AFDM_FRONTEND_SUBMIT') && AFDM_FRONTEND_SUBMIT) {
			jeo_geocode_save($post_id);
//			$this->save_artwork_styles($post_id);
			$this->save_artwork_categories($post_id);
			$this->save_artwork_images($post_id);
		}
	}

/*	function box_artwork_dimensions($post = false) {
		if($post) {
			$dimensions = $this->get_artwork_dimensions();
		}
		?>
		<div id="artwork_dimensions_box">
			<h4><?php _e('Artwork dimensions', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<p class="input-container dimensions">
					<textarea placeholder="<?php _e('Describe the dimensions', 'arteforadomuseu'); ?>" rows="5" cols="80" type="text" name="artwork_dimensions" id="artwork_dimensions"><?php echo $dimensions; ?></textarea>
				</p>
			</div>
		</div>
		<?php
	}

	function save_artwork_dimensions($post_id) {

		if(isset($_POST['artwork_dimensions'])) {
			update_post_meta($post_id, 'artwork_dimensions', $_POST['artwork_dimensions']);
		} else {
			delete_post_meta($post_id, 'artwork_dimensions');
		}
	}

	function box_artwork_dates($post = false) {

		wp_enqueue_style('jquery-ui-smoothness', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('jquery-chosen');
		wp_enqueue_script('artworks-box-dates', $this->directory_uri . '/js/artworks.box.dates.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-datepicker-pt-BR', 'jquery-chosen'), '0.0.9');
		wp_localize_script('artworks-box-dates', 'box_dates_settings', array(
			'dateFormat' => 'dd/mm/yy',
			'language' => get_bloginfo('language'),
			'isAdmin' => is_admin()
		));

		if($post) {
			$creation_date = $this->get_artwork_creation_date();
			$termination_date = $this->get_artwork_termination_date();
			$currently_active = $this->is_artwork_currently_active();
		}
		?>
		<div id="artwork_dates_box">
			<h4><?php _e('Creation and termination dates', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<?php 
				<p class="input-container creation-date">
					<input placeholder="<?php _e('Creation date', 'arteforadomuseu'); ?>" class="datepicker" type="text" name="artwork_date_creation" id="artwork_date_creation" value="<?php echo $creation_date; ?>" />
				</p>
				<p class="input-container termination-date">
					<input placeholder="<?php _e('Termination date', 'arteforadomuseu'); ?>" class="datepicker" type="text" name="artwork_date_termination" id="artwork_date_termination" value="<?php echo $termination_date; ?>" />
				</p>
				 ?>
				<p class="input-container creation-date">
					<select name="artwork_date_creation" id="artwork_date_creation" data-placeholder="<?php _e('Creation year', 'arteforadomuseu'); ?>" class="chosen">
						<option value=""><?php _e('Creation year', 'arteforadomuseu'); ?></option>
						<?php for($i = date('Y'); $i >= 1000; $i--) : ?>
							<option value="<?php echo $i; ?>" <?php if($creation_date == $i) echo 'selected'; ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</p>
				<p class="input-container termination-date">
					<select name="artwork_date_termination" id="artwork_date_termination" data-placeholder="<?php _e('Termination year', 'arteforadomuseu'); ?>" class="chosen">
						<option value=""><?php _e('Termination year', 'arteforadomuseu'); ?></option>
						<?php for($i = date('Y'); $i >= 1000; $i--) : ?>
							<option value="<?php echo $i; ?>" <?php if($termination_date == $i) echo 'selected'; ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</p>
				<p class="input-container currently-active">
					<input type="checkbox" name="artwork_currently_active" id="artwork_currently_active" <?php if($currently_active) echo 'checked'; ?> /> <label for="artwork_currently_active"><?php _e('Currently active', 'arteforadomuseu'); ?></label>
				</p>
			</div>
		</div>
		<?php
	}

	function save_artwork_dates($post_id) {

		if(isset($_POST['artwork_date_creation'])) {
			update_post_meta($post_id, 'artwork_date_creation', $_POST['artwork_date_creation']);
		}
		if(isset($_POST['artwork_date_termination'])) {
			update_post_meta($post_id, 'artwork_date_termination', $_POST['artwork_date_termination']);
		}
		if(isset($_POST['artwork_currently_active'])) {
			update_post_meta($post_id, 'artwork_currently_active', 1);
		} else {
			delete_post_meta($post_id, 'artwork_currently_active');
		}
	}

	function box_artwork_videos($post = false) {

		wp_enqueue_script('artworks-box-videos', $this->directory_uri . '/js/artworks.box.videos.js', array('jquery'), '0.0.3');

		if($post) {
			$videos = $this->get_artwork_videos();
			$featured_video = $this->get_artwork_featured_video();
		}

		?>
		<div id="artwork_videos_box" class="loop-box">
			<h4><?php _e('Videos', 'arteforadomuseu'); ?></h4>
			<p class="tip"><?php _e('Video URLs from YouTube, Vimeo, Blip.tv, Dailymotion, Qik or Flickr', 'arteforadomuseu'); ?></p>
			<a class="new-video button new-button secondary" href="#"><?php _e('Add video', 'arteforadomuseu'); ?></a>
			<div class="box-inputs">
				<ul class="video-template" style="display:none;">
					<li class="template">
						<?php $this->video_input_template(); ?>
					</li>
				</ul>
				<ul class="video-list">
					<?php if($videos) : foreach($videos as $video) : ?>
						<li>
							<?php
							$featured = ($featured_video == $video['id']);
							$this->video_input_template($video['id'], $video['url'], $featured);
							?>
						</li>
					<?php endforeach; endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	function video_input_template($id = false, $url = false, $featured = false) {
		?>
			<p class="input-container video-url main-input">
				<input type="text" class="video-input" size="60" <?php if($id) echo 'name="videos[' . $id . '][url]"'; ?> <?php if($url) echo 'value="' . $url . '"'; ?> placeholder="<?php _e('Video url', 'arteforadomuseu'); ?>" />
			</p>
			<p class="input-container featured">
				<input type="radio" <?php if($id) echo 'value="' . $id . '" id="featured_video_' . $id . '"'; ?> name="featured_video" class="featured-input" <?php if($featured) echo 'checked'; ?> /> <label <?php if($id) echo 'for="featured_video_' . $id . '"'; ?> class="featured-label"><?php _e('Featured', 'arteforadomuseu'); ?></label>
			</p>
			<input type="hidden" class="video-id" <?php if($id) echo 'name="videos[' . $id . '][id]" value="' . $id . '"'; ?> />
			<a class="remove-video button remove" href="#"><?php _e('Remove', 'arteforadomuseu'); ?></a>
		<?php
	}

	function save_artwork_videos($post_id) {

		if(isset($_POST['videos'])) {
			update_post_meta($post_id, 'artwork_videos', $_POST['videos']);
		} else {
			delete_post_meta($post_id, 'artwork_videos');
		}

		if(isset($_POST['featured_video'])) {
			update_post_meta($post_id, 'artwork_featured_video', $_POST['featured_video']);
		}

	}
*/
	function box_artwork_links($post = false) {

		wp_enqueue_script('artworks-box-links', $this->directory_uri . '/js/artworks.box.links.js', array('jquery'), '0.0.3');

		if($post) {
			$links = $this->get_artwork_links();
			$featured_link = $this->get_artwork_featured_link();
		}

		?>
		<div id="artwork_links_box" class="loop-box">
			<h4><?php _e('Links', 'arteforadomuseu'); ?></h4>
			<a class="new-link new-button button secondary" href="#"><?php _e('Add link', 'arteforadomuseu'); ?></a>
			<div class="box-inputs">
				<ul class="link-template" style="display:none;">
					<li class="template">
						<?php $this->link_input_template(); ?>
					</li>
				</ul>
				<ul class="link-list">
					<?php if($links) : foreach($links as $link) : ?>
						<li>
							<?php
							$featured = ($featured_link == $link['id']);
							$this->link_input_template($link['id'], $link['title'], $link['url'], $link['description'], $featured);
							?>
						</li>
					<?php endforeach; endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	function link_input_template($id = false, $title = false, $url = false, $description = false, $featured = false) {
		?>
			<h5><?php _e('Link title', 'arteforadomuseu'); ?></h5>
			<div class="clearfix">
				<div class="one-third-1"><input type="text" class="link-title" id="link-title-en" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][title][en]"'; ?> <?php if($title) echo 'value="' . $title['en'] . '"'; ?> placeholder="<?php _e('Link title (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="link-title" id="link-title-es" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][title][es]"'; ?> <?php if($title) echo 'value="' . $title['es'] . '"'; ?> placeholder="<?php _e('Link title (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="link-title" id="link-title-pt" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][title][pt]"'; ?> <?php if($title) echo 'value="' . $title['pt'] . '"'; ?> placeholder="<?php _e('Link title (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
			<h5><?php _e('Link URL', 'arteforadomuseu'); ?></h5>
			<div class="clearfix">
				<input type="text" class="link-url" size="98" <?php if($id) echo 'name="artwork_links[' . $id . '][url]"'; ?> <?php if($url) echo 'value="' . $url . '"'; ?> placeholder="<?php _e('Link url', 'arteforadomuseu'); ?>" />
			</div>
			<h5><?php _e('Link description', 'arteforadomuseu'); ?></h5>
			<div class="clearfix">
				<div class="one-third-1"><input type="text" class="link-description" id="link-description-en" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][description][en]"'; ?> <?php if($description) echo 'value="' . $description['en'] . '"'; ?> placeholder="<?php _e('Link description (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="link-description" id="link-description-es" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][description][es]"'; ?> <?php if($description) echo 'value="' . $description['es'] . '"'; ?> placeholder="<?php _e('Link description (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="link-description" id="link-description-pt" size="30" <?php if($id) echo 'name="artwork_links[' . $id . '][description][pt]"'; ?> <?php if($description) echo 'value="' . $description['pt'] . '"'; ?> placeholder="<?php _e('Link description (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
			<p class="input-container featured">
				<input type="radio" <?php if($id) echo 'value="' . $id . '" id="featured_link_' . $id . '"'; ?> name="featured_link" class="featured-input" <?php if($featured) echo 'checked'; ?> /> <label <?php if($id) echo 'for="featured_link_' . $id . '"'; ?> class="featured-label"><?php _e('Featured', 'arteforadomuseu'); ?></label>
			</p>
			<input type="hidden" class="link-id" <?php if($id) echo 'name="artwork_links[' . $id . '][id]" value="' . $id . '"'; ?> />
			<a class="remove-link button remove" href="#"><?php _e('Remove', 'arteforadomuseu'); ?></a>
		<?php
	}

	function save_artwork_links($post_id) {

		if(isset($_POST['artwork_links'])) {
			update_post_meta($post_id, 'artwork_links', $_POST['artwork_links']);
		} else {
			delete_post_meta($post_id, 'artwork_links');
		}

		if(isset($_POST['featured_link'])) {
			update_post_meta($post_id, 'artwork_featured_link', $_POST['featured_link']);
		} else {
			delete_post_meta($post_id, 'artwork_featured_link');
		}

	}

	function box_artwork_urls($post = false) {

		if($post) {
			$url = $this->get_artwork_url();
			$contact = $this->get_artwork_contact();
		}

		?>
		<div id="artwork_urls_box" class="loop-box">
			<div class="box-inputs">
				<div class="half-1"><input type="text" class="url" size="80" name="artwork_url" <?php if($url) echo 'value="' . $url . '"'; ?> placeholder="<?php _e('URL of the initiative', 'arteforadomuseu'); ?>" /></div>
				<div class="half-1"><input type="text" class="url" size="80" name="artwork_contact" <?php if($contact) echo 'value="' . $contact . '"'; ?> placeholder="<?php _e('Initiative contact', 'arteforadomuseu'); ?>" /></div>
			</div>
		</div>
		<?php
	}

	function save_artwork_urls($post_id) {

		if(isset($_POST['artwork_url'])) {
			update_post_meta($post_id, 'artwork_url', $_POST['artwork_url']);
		} else {
			delete_post_meta($post_id, 'artwork_url');
		}

		if(isset($_POST['artwork_contact'])) {
			update_post_meta($post_id, 'artwork_contact', $_POST['artwork_contact']);
		} else {
			delete_post_meta($post_id, 'artwork_contact');
		}
	}

	function box_artwork_place($post = false) {

		if($post) {
			$city = $this->get_artwork_city();
		}

		?>
		<div id="artwork_place_box" class="loop-box">
			<div class="box-inputs">
				<div class="one-third-1"><input type="text" class="city" size="30" name="artwork_city[en]" <?php if($city[en]) echo 'value="' . $city[en] . '"'; ?> placeholder="<?php _e('City (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="city" size="30" name="artwork_city[es]" <?php if($city[es]) echo 'value="' . $city[es] . '"'; ?> placeholder="<?php _e('City (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="city" size="30" name="artwork_city[pt]" <?php if($city[pt]) echo 'value="' . $city[pt] . '"'; ?> placeholder="<?php _e('City (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
		</div>
		<?php
	}

	function box_search_artwork_place($post) {

		if($post == 'search') {
			$city = $_REQUEST['artwork_city'];
		}

		?>

		<div id="artwork_place_box" class="loop-box box-inputs"><input type="text" class="city" size="30" name="artwork_city" <?php if($city) echo 'value="' . $city . '"'; ?> placeholder="<?php _e('City', 'arteforadomuseu'); ?>" /></div>
		<?php
	}

	function save_artwork_place($post_id) {

		if(isset($_POST['artwork_city'])) {
			update_post_meta($post_id, 'artwork_city', $_POST['artwork_city']);
		} else {
			delete_post_meta($post_id, 'artwork_city');
		}
	}

	function box_artwork_organizations($post = false) {

		if($post) {
			$organization = $this->get_artwork_organization();
			$organizations = $this->get_artwork_organizations();
			$funders = $this->get_artwork_funders();
		}

		?>
		<div id="artwork_organizations_box" class="loop-box">
			<h4><?php _e('Organization', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<div class="one-third-1"><input type="text" class="organization" size="30" name="artwork_organization[en]" <?php if($organization[en]) echo 'value="' . $organization[en] . '"'; ?> placeholder="<?php _e('Organization (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="organization" size="30" name="artwork_organization[es]" <?php if($organization[es]) echo 'value="' . $organization[es] . '"'; ?> placeholder="<?php _e('Organization (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="organization" size="30" name="artwork_organization[pt]" <?php if($organization[pt]) echo 'value="' . $organization[pt] . '"'; ?> placeholder="<?php _e('Organization (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
			<h4><?php _e('Organizations/Collaborators', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<div class="one-third-1"><input type="text" class="organizations" size="30" name="artwork_organizations[en]" <?php if($organizations[en]) echo 'value="' . $organizations[en] . '"'; ?> placeholder="<?php _e('Organizations/Collaborators (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="organizations" size="30" name="artwork_organizations[es]" <?php if($organizations[es]) echo 'value="' . $organizations[es] . '"'; ?> placeholder="<?php _e('Organizations/Collaborators (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="organizations" size="30" name="artwork_organizations[pt]" <?php if($organizations[pt]) echo 'value="' . $organizations[pt] . '"'; ?> placeholder="<?php _e('Organizations/Collaborators (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
			<h4><?php _e('Funders', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<div class="one-third-1"><input type="text" class="funders" size="30" name="artwork_funders[en]" <?php if($funders[en]) echo 'value="' . $funders[en] . '"'; ?> placeholder="<?php _e('Funders (English)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="funders" size="30" name="artwork_funders[es]" <?php if($funders[es]) echo 'value="' . $funders[es] . '"'; ?> placeholder="<?php _e('Funders (Spanish)', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="funders" size="30" name="artwork_funders[pt]" <?php if($funders[pt]) echo 'value="' . $funders[pt] . '"'; ?> placeholder="<?php _e('Funders (Portuguese)', 'arteforadomuseu'); ?>" /></div>
			</div>
		</div>
		<?php
	}

	function box_search_artwork_organizations($post) {

		if($post == 'search') {
			$organization = $_REQUEST['artwork_organization'];
			$organizations = $_REQUEST['artwork_organizations'];
			$funders = $_REQUEST['artwork_funders'];
		}

		?>
		<div id="artwork_organizations_box" class="loop-box">
			<div class="one-third-1">
				<h4><?php _e('Organization', 'arteforadomuseu'); ?></h4>
				<div class="box-inputs"><input type="text" class="organization" size="30" name="artwork_organization" <?php if($organization) echo 'value="' . $organization . '"'; ?> placeholder="<?php _e('Organization', 'arteforadomuseu'); ?>" /></div>
			</div>
			<div class="one-third-1">
				<h4><?php _e('Organizations/Collaborators', 'arteforadomuseu'); ?></h4>
				<div class="box-inputs"><input type="text" class="organizations" size="30" name="artwork_organizations" <?php if($organizations) echo 'value="' . $organizations . '"'; ?> placeholder="<?php _e('Organizations/Collaborators', 'arteforadomuseu'); ?>" /></div>
			</div>
			<div class="one-third-2">
				<h4><?php _e('Funders', 'arteforadomuseu'); ?></h4>
				<div class="box-inputs"><input type="text" class="funders" size="30" name="artwork_funders" <?php if($funders) echo 'value="' . $city . '"'; ?> placeholder="<?php _e('Funders', 'arteforadomuseu'); ?>" /></div>
			</div>
		</div>
		<?php
	}

	function save_artwork_organizations($post_id) {

		if(isset($_POST['artwork_organization'])) {
			update_post_meta($post_id, 'artwork_organization', $_POST['artwork_organization']);
		} else {
			delete_post_meta($post_id, 'artwork_organization');
		}

		if(isset($_POST['artwork_organizations'])) {
			update_post_meta($post_id, 'artwork_organizations', $_POST['artwork_organizations']);
		} else {
			delete_post_meta($post_id, 'artwork_organizations');
		}

		if(isset($_POST['artwork_funders'])) {
			update_post_meta($post_id, 'artwork_funders', $_POST['artwork_funders']);
		} else {
			delete_post_meta($post_id, 'artwork_funders');
		}
	}

	function box_artwork_languages($post = false) {

		if ($post == 'search') {
			$interface_languages = $_REQUEST['artwork_interface_languages'];
			$resource_languages = $_REQUEST['artwork_resource_languages'];
		} elseif($post) {
			$interface_languages = $this->get_artwork_interface_languages();
			$resource_languages = $this->get_artwork_resource_languages();
		} 
		?>

		<div id="artwork_languages_box" class="loop-box">
			<div class="half-1">
				<h4><?php _e('Interface languages', 'arteforadomuseu'); ?></h4>
				<ul class="input-container interface-languages main-input">
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('aymara', $interface_languages)) echo "checked"; ?> value="aymara" /><?php _e('Aymara', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('creole', $interface_languages)) echo "checked"; ?> value="creole" /><?php _e('Creole', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('spanish', $interface_languages)) echo "checked"; ?> value="spanish" /><?php _e('Spanish', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('french', $interface_languages)) echo "checked"; ?> value="french" /><?php _e('French', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('guarani', $interface_languages)) echo "checked"; ?> value="guarani" /><?php _e('Guarani', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('dutch', $interface_languages)) echo "checked"; ?> value="dutch" /><?php _e('Dutch', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('english', $interface_languages)) echo "checked"; ?> value="english" /><?php _e('English', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('portuguese', $interface_languages)) echo "checked"; ?> value="portuguese" /><?php _e('Portuguese', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('quechua', $interface_languages)) echo "checked"; ?> value="quechua" /><?php _e('Quechua', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="interface-languages" name="artwork_interface_languages[]" <?php if((is_array($interface_languages)) && in_array('others', $interface_languages)) echo "checked"; ?> value="others" /><?php _e('Others', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
			<div class="half-2">
				<h4><?php _e('Resource languages', 'arteforadomuseu'); ?></h4>
				<ul class="input-container resource-languages main-input">
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('aymara', $resource_languages)) echo "checked"; ?> value="aymara" /><?php _e('Aymara', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('creole', $resource_languages)) echo "checked"; ?> value="creole" /><?php _e('Creole', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('spanish', $resource_languages)) echo "checked"; ?> value="spanish" /><?php _e('Spanish', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('french', $resource_languages)) echo "checked"; ?> value="french" /><?php _e('French', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('guarani', $resource_languages)) echo "checked"; ?> value="guarani" /><?php _e('Guarani', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('dutch', $resource_languages)) echo "checked"; ?> value="dutch" /><?php _e('Dutch', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('english', $resource_languages)) echo "checked"; ?> value="english" /><?php _e('English', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('portuguese', $resource_languages)) echo "checked"; ?> value="portuguese" /><?php _e('Portuguese', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('quechua', $resource_languages)) echo "checked"; ?> value="quechua" /><?php _e('Quechua', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-languages" name="artwork_resource_languages[]" <?php if((is_array($resource_languages)) && in_array('others', $resource_languages)) echo "checked"; ?> value="others" /><?php _e('Others', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
		</div>
		<?php
	}

	function save_artwork_languages($post_id) {

		if(isset($_POST['artwork_interface_languages'])) {
			update_post_meta($post_id, 'artwork_interface_languages', $_POST['artwork_interface_languages']);
		} else {
			delete_post_meta($post_id, 'artwork_interface_languages');
		}

		if(isset($_POST['artwork_resource_languages'])) {
			update_post_meta($post_id, 'artwork_resource_languages', $_POST['artwork_resource_languages']);
		} else {
			delete_post_meta($post_id, 'artwork_resource_languages');
		}

	}

	function box_artwork_licenses($post = false) {

		if ($post == 'search') {
			$site_license = $_REQUEST['artwork_site_license'];
			$resource_license = $_REQUEST['artwork_resource_license'];
		} elseif($post) {
			$site_license = $this->get_artwork_site_license();
			$resource_license = $this->get_artwork_resource_license();
		}

		?>
		<div id="artwork_licenses_box" class="loop-box">
			<div class="half-1">
				<h4><?php _e('Site license', 'arteforadomuseu'); ?></h4>
				<ul class="input-container site-license main-input">
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by', $site_license)) echo "checked"; if ('cc_by' == $site_license) echo "checked";  ?> value="cc_by" /><?php _e('CC-BY', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by_sa', $site_license)) echo "checked"; if ('cc_by_sa' == $site_license) echo "checked"; ?> value="cc_by_sa" /><?php _e('CC-BY-SA', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by_nc_sa', $site_license)) echo "checked"; if ('cc_by_nc_sa' == $site_license) echo "checked"; ?> value="cc_by_nc_sa" /><?php _e('CC-BY-NC-SA', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by_nc', $site_license)) echo "checked"; if ('cc_by_nc' == $site_license) echo "checked"; ?> value="cc_by_nc" /><?php _e('CC-BY-NC', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by_nd', $site_license)) echo "checked"; if ('cc_by_nd' == $site_license) echo "checked"; ?> value="cc_by_nd" /><?php _e('CC-BY-ND', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('cc_by_nc_nd', $site_license)) echo "checked"; if ('cc_by_nc_nd' == $site_license) echo "checked"; ?> value="cc_by_nc_nd" /><?php _e('CC-BY-NC-ND', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('copyright', $site_license)) echo "checked"; if ('copyright' == $site_license) echo "checked"; ?> value="copyright" /><?php _e('Copyright', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('public_domain', $site_license)) echo "checked"; if ('public_domain' == $site_license) echo "checked"; ?> value="public_domain" /><?php _e('Public domain', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('none', $site_license)) echo "checked"; if ('none' == $site_license) echo "checked"; ?> value="none" /><?php _e('None', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="site-license" name="artwork_site_license[]" <?php if ((is_array($site_license)) && in_array('other', $site_license)) echo "checked";if ('other' == $site_license) echo "checked"; ?> value="other" /><?php _e('Other', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
			<div class="half-2">
				<h4><?php _e('Resource license', 'arteforadomuseu'); ?></h4>
				<ul class="input-container resource-languages main-input">
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by', $resource_license)) echo "checked"; if ('cc-by' == $resource_license) echo "checked"; ?> value="cc_by" /><?php _e('CC-BY', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by_sa', $resource_license)) echo "checked"; if ('cc_by_sa' == $resource_license) echo "checked"; ?> value="cc_by_sa" /><?php _e('CC-BY-SA', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by_nc_sa', $resource_license)) echo "checked"; if ('cc_by_nc_sa' == $resource_license) echo "checked"; ?> value="cc_by_nc_sa" /><?php _e('CC-BY-NC-SA', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by_nc', $resource_license)) echo "checked"; if ('cc_by_nc' == $resource_license) echo "checked"; ?> value="cc_by_nc" /><?php _e('CC-BY-NC', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by_nd', $resource_license)) echo "checked"; if ('cc_by_nd' == $resource_license) echo "checked"; ?> value="cc_by_nd" /><?php _e('CC-BY-ND', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('cc_by_nc_nd', $resource_license)) echo "checked"; if ('cc_by_nc_nd' == $resource_license) echo "checked"; ?> value="cc_by_nc_nd" /><?php _e('CC-BY-NC-ND', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('copyright', $resource_license)) echo "checked"; if ('copyright' == $resource_license) echo "checked"; ?> value="copyright" /><?php _e('Copyright', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('public_domain', $resource_license)) echo "checked"; if ('public_domain' == $resource_license) echo "checked"; ?> value="public_domain" /><?php _e('Public domain', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('none', $resource_license)) echo "checked"; if ('none' == $resource_license) echo "checked"; ?> value="none" /><?php _e('None', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource-license" name="artwork_resource_license[]" <?php if ((is_array($resource_license)) && in_array('other', $resource_license)) echo "checked"; if ('other' == $resource_license) echo "checked"; ?> value="other" /><?php _e('Other', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
		</div>
		<?php
	}

	function save_artwork_licenses($post_id) {

		if(isset($_POST['artwork_site_license'])) {
			update_post_meta($post_id, 'artwork_site_license', $_POST['artwork_site_license']);
		} else {
			delete_post_meta($post_id, 'artwork_site_license');
		}

		if(isset($_POST['artwork_resource_license'])) {
			update_post_meta($post_id, 'artwork_resource_license', $_POST['artwork_resource_license']);
		} else {
			delete_post_meta($post_id, 'artwork_resource_license');
		}

	}

	function box_artwork_resources($post = false) {

		if ($post == 'search') {
			$resource_types = $_REQUEST['artwork_resource_types'];
			$academic_level = $_REQUEST['artwork_academic_level'];
			$subject_areas = $_REQUEST['artwork_subject_areas'];
		} elseif($post) {
			$resource_types = $this->get_artwork_resource_types();
			$academic_level = $this->get_artwork_academic_level();
			$subject_areas = $this->get_artwork_subject_areas();
		}

		?>
		<div id="artwork_resources_box" class="loop-box">
			<div class="one-third-1">
				<h4><?php _e('Types of resources/ Media type', 'arteforadomuseu'); ?></h4>
				<ul class="input-container resource-type main-input">
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('animation', $resource_types)) echo "checked"; ?> value="animation" /><?php _e('Animation', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('audio', $resource_types)) echo "checked"; ?> value="audio" /><?php _e('Audio', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('digital_class', $resource_types)) echo "checked"; ?> value="digital_class" /><?php _e('Digital class', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('game', $resource_types)) echo "checked"; ?> value="game" /><?php _e('Game', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('image', $resource_types)) echo "checked"; ?> value="image" /><?php _e('Image', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('digital_book', $resource_types)) echo "checked"; ?> value="digital_book" /><?php _e('Digital book', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('simulation', $resource_types)) echo "checked"; ?> value="simulation" /><?php _e('Simulation', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('software', $resource_types)) echo "checked"; ?> value="software" /><?php _e('Software', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('text', $resource_types)) echo "checked"; ?> value="text" /><?php _e('Text', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="resource_types" name="artwork_resource_types[]" <?php if((is_array($resource_types)) && in_array('video', $resource_types)) echo "checked"; ?> value="video" /><?php _e('Video', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
			<div class="one-third-1">
				<h4><?php _e('Academic level', 'arteforadomuseu'); ?></h4>
				<ul class="input-container academic-level main-input">
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('0', $academic_level)) echo "checked"; ?> value="0" /><?php _e('0 - Early childhood education', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('1', $academic_level)) echo "checked"; ?> value="1" /><?php _e('1 - Primary', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('2', $academic_level)) echo "checked"; ?> value="2" /><?php _e('2 - Lower secondary', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('3', $academic_level)) echo "checked"; ?> value="3" /><?php _e('3 - Upper secondary', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('4', $academic_level)) echo "checked"; ?> value="4" /><?php _e('4 - Post-secondary non-tertiary', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('5', $academic_level)) echo "checked"; ?> value="5" /><?php _e('5 - Short-cycle tertiary', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('6', $academic_level)) echo "checked"; ?> value="6" /><?php _e('6 - Bachelor or equivalent', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('7', $academic_level)) echo "checked"; ?> value="7" /><?php _e('7 - Master or equivalent', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('8', $academic_level)) echo "checked"; ?> value="8" /><?php _e('8 - Doctoral or equivalent', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="academic-level" name="artwork_academic_level[]" <?php if((is_array($academic_level)) && in_array('9', $academic_level)) echo "checked"; ?> value="9" /><?php _e('9 - Not elsewhere classified', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
			<div class="one-third-2">
				<h4><?php _e('Subject area/ Areas of knowledge', 'arteforadomuseu'); ?></h4>
				<textarea name="artwork_subject_areas"><?php if(is_array($subject_areas)) echo implode(', ', $subject_areas); else echo $subject_areas; ?></textarea>
			</div>
		</div>
		<?php
	}

	function save_artwork_resources($post_id) {

		if(isset($_POST['artwork_resource_types'])) {
			update_post_meta($post_id, 'artwork_resource_types', $_POST['artwork_resource_types']);
		} else {
			delete_post_meta($post_id, 'artwork_resource_types');
		}

		if(isset($_POST['artwork_academic_level'])) {
			update_post_meta($post_id, 'artwork_academic_level', $_POST['artwork_academic_level']);
		} else {
			delete_post_meta($post_id, 'artwork_academic_level');
		}

		if(isset($_POST['artwork_subject_areas'])) {
			update_post_meta($post_id, 'artwork_subject_areas', $_POST['artwork_subject_areas']);
		} else {
			delete_post_meta($post_id, 'artwork_subject_areas');
		}

	}

	function box_artwork_input($post = false) {

		if ($post == 'search') {
			$output_interfaces = $_REQUEST['artwork_output_interfaces'];
			$input_by_users = $_REQUEST['artwork_input_by_users'];
		} elseif($post) {
			$output_interfaces = $this->get_artwork_output_interfaces();
			$input_by_users = $this->get_artwork_input_by_users();
		}

		?>
		<div id="artwork_input_box" class="loop-box">
			<div class="half-1">
				<h4><?php _e('Output Interfaces', 'arteforadomuseu'); ?></h4>
				<ul class="input-container output-interfaces main-input">
					<li><label><input type="checkbox" class="output-interfaces" name="artwork_output_interfaces[]" <?php if((is_array($output_interfaces)) && in_array('download', $output_interfaces)) echo "checked"; ?> value="download" /><?php _e('Download data (JSON, XML, CSV)', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="output-interfaces" name="artwork_output_interfaces[]" <?php if((is_array($output_interfaces)) && in_array('output', $output_interfaces)) echo "checked"; ?> value="output" /><?php _e('Output services (API, OAI, SQI, SRU, SPI)', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
			<div class="half-2">
				<h4><?php _e('Input by users', 'arteforadomuseu'); ?></h4>
				<ul class="input-container input-by-users main-input">
					<li><label><input type="checkbox" class="input-by-users" name="artwork_input_by_users[]" <?php if((is_array($input_by_users)) && in_array('contact', $input_by_users)) echo "checked"; ?> value="contact" /><?php _e('Contact (form/email)', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="input-by-users" name="artwork_input_by_users[]" <?php if((is_array($input_by_users)) && in_array('join', $input_by_users)) echo "checked"; ?> value="join" /><?php _e('Join (create account)', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="input-by-users" name="artwork_input_by_users[]" <?php if((is_array($input_by_users)) && in_array('comment', $input_by_users)) echo "checked"; ?> value="comment" /><?php _e('Comment (vote, tag)', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="input-by-users" name="artwork_input_by_users[]" <?php if((is_array($input_by_users)) && in_array('contribute', $input_by_users)) echo "checked"; ?> value="contribute" /><?php _e('Contribute (post/moderated post)', 'arteforadomuseu'); ?></label></li>
					<li><label><input type="checkbox" class="input-by-users" name="artwork_input_by_users[]" <?php if((is_array($input_by_users)) && in_array('community', $input_by_users)) echo "checked"; ?> value="community" /><?php _e('Community (forums, COP, bulletin boards)', 'arteforadomuseu'); ?></label></li>
				</ul>
			</div>
		</div>
		<?php
	}

	function save_artwork_input($post_id) {

		if(isset($_POST['artwork_output_interfaces'])) {
			update_post_meta($post_id, 'artwork_output_interfaces', $_POST['artwork_output_interfaces']);
		} else {
			delete_post_meta($post_id, 'artwork_output_interfaces');
		}

		if(isset($_POST['artwork_input_by_users'])) {
			update_post_meta($post_id, 'artwork_input_by_users', $_POST['artwork_input_by_users']);
		} else {
			delete_post_meta($post_id, 'artwork_input_by_users');
		}

	}

	function box_artwork_other($post = false) {

		if ($post == 'search') {
			$collections = $_REQUEST['artwork_collections'];
			$site_accessibility = $_REQUEST['artwork_site_accessibility'];
			$site_time_markers = $_REQUEST['artwork_site_time_markers'];
		} elseif($post) {
			$collections = $this->get_artwork_collections();
			$site_accessibility = $this->get_artwork_site_accessibility();
			$site_time_markers = $this->get_artwork_site_time_markers();
		}

		?>
		<div id="artwork_other_box" class="loop-box">
			<div class="box-inputs">
				<div class="one-third-1"><input type="text" class="collections" size="80" name="artwork_collections" <?php if($collections) echo 'value="' . $collections . '"'; ?> placeholder="<?php _e('Collections', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-1"><input type="text" class="site-accessibility" size="80" name="artwork_site_accessibility" <?php if($site_accessibility) echo 'value="' . $site_accessibility . '"'; ?> placeholder="<?php _e('Site accessibility', 'arteforadomuseu'); ?>" /></div>
				<div class="one-third-2"><input type="text" class="site-time-markers" size="80" name="artwork_site_time_markers" <?php if($site_time_markers) echo 'value="' . $site_time_markers . '"'; ?> placeholder="<?php _e('Start year–End year (if ended)', 'arteforadomuseu'); ?>" /></div>
			</div>
		</div>
		<?php
	}

	function save_artwork_other($post_id) {

		if(isset($_POST['artwork_collections'])) {
			update_post_meta($post_id, 'artwork_collections', $_POST['artwork_collections']);
		} else {
			delete_post_meta($post_id, 'artwork_collections');
		}

		if(isset($_POST['artwork_site_accessibility'])) {
			update_post_meta($post_id, 'artwork_site_accessibility', $_POST['artwork_site_accessibility']);
		} else {
			delete_post_meta($post_id, 'artwork_site_accessibility');
		}

		if(isset($_POST['artwork_site_time_markers'])) {
			update_post_meta($post_id, 'artwork_site_time_markers', $_POST['artwork_site_time_markers']);
		} else {
			delete_post_meta($post_id, 'artwork_site_time_markers');
		}
	}

	/*
	 * Taxonomy boxes, for non-admin dashboard usage only
	 */
/*
	function box_artwork_styles($post = false) {

		wp_enqueue_script('jquery-tag-it');
		wp_enqueue_style('jquery-tag-it');

		if($post) {
			$post_style_names = $this->get_artwork_style_names();
		}

		$styles = get_terms('style', array('hide_empty' => 0));
		$style_names = array();
		if($styles) {
			foreach($styles as $style) {
				$style_names[] = $style->name;
			}
		}
		?>
		<div id="artwork_styles_box">
			<h4><?php _e('Tag styles for this artwork', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<ul id="style-tags">
					<?php
					if($post_style_names) {
						foreach($post_style_names as $style_name) {
							echo '<li>' . $style_name . '</li>';
						}
					}
					?>
				</ul>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						$('#style-tags').tagit({
							fieldName: 'styles[]',
							tagLimit: 5,
							availableTags: <?php echo json_encode($style_names); ?>,
							autocomplete: { delay: 0, minLength: 2 },
							allowSpaces: true,
							caseSensitive: false
						});
					});
				</script>
			</div>
		</div>
		<?php
	}

	function save_artwork_styles($post_id) {

		if(isset($_REQUEST['categories'])) {
			wp_set_object_terms($post_id, $_REQUEST['styles'], 'style');
		}

	}
*/
	function box_artwork_categories($post = false) {

		if($post == 'search') {
			$category_name = $_REQUEST['category_name'];
		} elseif($post) {
			$category = array_shift(get_the_category($post->ID));
			$category_name = $category->slug;
		}

		$categories = get_categories(array('hide_empty' => 0));

		if(!$categories)
			return false;

		?>
		<div id="artwork_categories_box">
			<h4><?php _e('Select a category', 'arteforadomuseu'); ?></h4>
			<div class="box-inputs">
				<select id="artwork_categories_select" name="category_name">
					<option></option>
					<?php foreach($categories as $category) : ?>
						<option value="<?php echo $category->slug; ?>" <?php if($category->slug == $category_name) echo 'selected'; ?>><?php echo $category->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php
	}

	function save_artwork_categories($post_id) {

		if(isset($_REQUEST['category_name'])) {
			wp_set_object_terms($post_id, $_REQUEST['category_name'], 'category');
		}

	}

	function box_artwork_country($post = false) {

		$artists = get_posts(array(
			'post_type' => 'country',
			'post_status' => array('publish', 'private', 'pending', 'draft', 'future'),
			'posts_per_page' => -1,
			'not_geo_query' => 1,
			'orderby' => 'title',
			'order' => 'ASC'
		));

		if(!$artists)
			return false;

		if($post == 'search') {
			$country_id = $_REQUEST['artwork_country'];
		} elseif($post) {
			$country_id = $this->get_artwork_country();
		}

		?>
		<div id="add_country_box">
			<?php if($artists) : ?>
				<h4><?php _e('Select a country', 'arteforadomuseu'); ?></h4>
				<div class="box-inputs">
					<select class="artists" name="artwork_country">
						<option></option>
						<?php foreach($artists as $artist) : ?>
							<option value="<?php echo $artist->ID; ?>" <?php if($artist->ID == $country_id) echo 'selected'; ?>><?php echo $artist->post_title; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	function save_artwork_country($post_id) {

		if(isset($_REQUEST['artwork_country'])) {

			$countries = get_posts(array(
				'post_type' => 'country',
				'meta_query' => array(
					array(
						'key' => '_artworks',
						'value' => $post_id
					)
				),
				'posts_per_page' => -1
			));

			foreach ($countries as $country) {
				delete_post_meta($country->ID, '_artworks', $post_id);
			}
			update_post_meta($_POST['artwork_country'], '_artworks', $post_id);
		} else {
			delete_post_meta($_POST['artwork_country'], '_artworks');
		}
	}

	function box_artwork_images($post = false) {

		wp_enqueue_script('artworks-box-images', $this->directory_uri . '/js/artworks.box.images.js', array('jquery'), '0.0.4');

		if($post) {
			$images = $this->get_artwork_images();
			$featured_link = $this->get_artwork_featured_image();
		}

		?>
		<div id="artwork_images_box" class="loop-box">
			<h4><?php _e('Images', 'arteforadomuseu'); ?></h4>
			<a class="new-image new-button button secondary" href="#"><?php _e('Add image', 'arteforadomuseu'); ?></a>
			<div class="box-inputs">
				<ul class="image-template" style="display:none;">
					<li class="template">
						<?php $this->image_input_template(); ?>
					</li>
				</ul>
				<ul class="image-list">
					<?php if($images) : foreach($images as $image) : ?>
						<li>
							<?php
							$featured = ($featured_link == $image['id']);
							$this->image_input_template($image['id'], $image['title'], false, $featured);
							?>
						</li>
					<?php endforeach; endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	function image_input_template($id = false, $title = false, $thumb_url = false, $featured = false) {
		?>
			<p class="input-container image main-input">
				<input type="text" class="image-title" size="30" <?php if($id) echo 'name="artwork_images[' . $id . '][title]"'; ?> <?php if($title) echo 'value="' . $title . '"'; ?> placeholder="<?php _e('Image title', 'arteforadomuseu'); ?>" />
				<input type="file" class="image-file" size="40" <?php if($id) echo 'name="artwork_image_files[]"'; ?> placeholder="<?php _e('Image file', 'arteforadomuseu'); ?>" />
			</p>
			<p class="input-container featured">
				<input type="radio" <?php if($id) echo 'value="' . $id . '" id="featured_image_' . $id . '"'; ?> name="featured_image" class="featured-input" <?php if($featured) echo 'checked'; ?> /> <label <?php if($id) echo 'for="featured_image_' . $id . '"'; ?> class="featured-label"><?php _e('Featured', 'arteforadomuseu'); ?></label>
			</p>
			<input type="hidden" class="image-id" <?php if($id) echo 'name="artwork_images[' . $id . '][id]" value="' . $id . '"'; ?> />
			<a class="remove-image button remove" href="#"><?php _e('Remove', 'arteforadomuseu'); ?></a>
		<?php
	}

	function save_artwork_images($post_id) {

		if(isset($_FILES['artwork_image_files'])) {
			$files = $_FILES['artwork_image_files'];
			$data = $_REQUEST['artwork_images'];

			$i = 0;
			foreach($files['name'] as $key => $value) {
				if($files['name'][$key]) {
					$file = array(
						'name'     => $files['name'][$key],
						'type'     => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error'    => $files['error'][$key],
						'size'     => $files['size'][$key]
					);

					$_FILES = array("artwork_images" => $file);
					foreach($_FILES as $file => $array) {
						if(getimagesize($array['tmp_name'])) {
							$attachment_id = media_handle_upload($file, $post_id, array('post_title' => $data['image-' . $i]['title']));
							if(isset($_REQUEST['featured_image']) && $data['image-' . $i]['id'] === $_REQUEST['featured_image']) {
								set_post_thumbnail($post_id, $attachment_id);
							}
						}
					}
					$i++;
				}
			}
		}

	}

	/*
	 * UI
	 */

	function hook_ui_elements() {
		if(is_user_logged_in()) { 
			add_action('afdm_logged_in_user_menu_items', array($this, 'user_menu_items'));
			add_action('wp_footer', array($this, 'add_artwork_box'));
		}
		add_action('wp_footer', array($this, 'advanced_search'));
	}

	function user_menu_items() {
		?>
		<li><a href="#" class="add_artwork"><?php _e('Submit an OER suggestion', 'arteforadomuseu'); ?></a></li>
		<?php
	}

	function add_artwork_box() {
		?>
		<div id="add_artwork">
			<h2 class="lightbox_title"><span class="lsf">&#xE041;</span> <?php _e('Submit new OER suggestion', 'arteforadomuseu'); ?></h2>
			<div class="lightbox_content">
				<form id="new_artwork" method="post" enctype="multipart/form-data">
					<div class="form-inputs">
						<?php $this->artwork_form_inputs(); ?>
					</div>
					<div class="form-actions">
						<input type="submit" value="<?php _e('Submit', 'arteforadomuseu'); ?>" />
						<a class="close button secondary" href="#"><?php _e('Cancel', 'arteforadomuseu'); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	function artwork_form_inputs($post = false) {
		?>
		<div class="clearfix">
			<div class="one-third-1">
				<input type="text" name="title_en" class="title" placeholder="<?php _e('Title (English)', 'arteforadomuseu'); ?>" />
			</div>
			<div class="one-third-1">
				<input type="text" name="title_es" class="title" placeholder="<?php _e('Title (Spanish)', 'arteforadomuseu'); ?>" />
			</div>
			<div class="one-third-2">
				<input type="text" name="title_pt" class="title" placeholder="<?php _e('Title (Portuguese)', 'arteforadomuseu'); ?>" />
			</div>
		</div>
		<div class="clearfix">
			<div class="one-third-1">
				<textarea name="content_en" placeholder="<?php _e('Description (English)', 'arteforadomuseu'); ?>"></textarea>
			</div>
			<div class="one-third-1">
				<textarea name="content_es" placeholder="<?php _e('Description (Spanish)', 'arteforadomuseu'); ?>"></textarea>
			</div>
			<div class="one-third-2">
				<textarea name="content_pt" placeholder="<?php _e('Description (Portuguese)', 'arteforadomuseu'); ?>"></textarea>
			</div>
		</div>
		<div class="clearfix">
			<div class="half-1">
				<div class="categories">
					<?php $this->box_artwork_categories($post); ?>
				</div>
			</div>
			<div class="half-2">
				<div class="categories">
					<?php $this->box_artwork_country($post); ?>
				</div>
			</div>
		</div>
		<h3><?php _e('Multimedia', 'arteforadomuseu'); ?></h3>
		<div class="multimedia form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_links($post); ?>
			</div>
		</div>
		<div class="multimedia form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_images($post); ?>
			</div>
		</div>
		<h3><?php _e('Contact', 'arteforadomuseu'); ?></h3>
		<div class="contact form-section row clearfix">
			<?php $this->box_artwork_urls($post); ?>
		</div>
		<h3><?php _e('City', 'arteforadomuseu'); ?></h3>
		<div class="city form-section row clearfix">
			<?php $this->box_artwork_place($post); ?>
		</div>
		<h3><?php _e('Organizations', 'arteforadomuseu'); ?></h3>
		<div class="organizations form-section row clearfix">
			<?php $this->box_artwork_organizations($post); ?>
		</div>
		<h3><?php _e('Languages', 'arteforadomuseu'); ?></h3>
		<div class="languages form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_languages($post); ?>
			</div>
		</div>
		<h3><?php _e('Licenses', 'arteforadomuseu'); ?></h3>
		<div class="licenses form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_licenses($post); ?>
			</div>
		</div>
		<h3><?php _e('Resources', 'arteforadomuseu'); ?></h3>
		<div class="resources form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_resources($post); ?>
			</div>
		</div>
		<h3><?php _e('Input and output', 'arteforadomuseu'); ?></h3>
		<div class="input form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_input($post); ?>
			</div>
		</div>
		<h3><?php _e('Other', 'arteforadomuseu'); ?></h3>
		<div class="other form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_other($post); ?>
			</div>
		</div>
		<div class="clearfix">
			<?php jeo_geocode_box($post); ?>
		</div>
		<?php
	}

	/* ++
	 * Advanced search
	 */
	

	//++ Creates search lightbox
	function advanced_search() {
		?>
		<div id="advanced_search">
			<h2 class="lightbox_title"><span class="lsf">&#xE116;</span> <?php printf(__('Advanced search', 'arteforadomuseu'), '<span class="title"></span>'); ?></h2>
			<div class="lightbox_content">
				<form id="advanced_search_form" role="search" class="clearfix" method="get" action="<?php bloginfo('url'); ?>">
					<div class="form-inputs">
						<?php $this->search_form_inputs(); ?>
					</div>
					<div class="form-actions">
						<input type="submit" value="<?php _e('Search', 'arteforadomuseu'); ?>" />
						<a class="close button secondary" href="#"><?php _e('Cancel', 'arteforadomuseu'); ?></a>
					</div>
				</form>
			</div>
		</div>
	<?php
	}

	//++ Defines search form
	function search_form_inputs($post = false) {
		?>
		<input type="text" name="s" class="title" placeholder="<?php _e('Keyword', 'arteforadomuseu'); ?>" />
		<div class="clearfix">
			<div class="half-1">
				<div class="categories">
					<?php $this->box_artwork_categories('search'); ?>
				</div>
			</div>
			<div class="half-2">
				<div class="categories">
					<?php $this->box_artwork_country('search'); ?>
				</div>
			</div>
		</div>
		<h3><?php _e('City', 'arteforadomuseu'); ?></h3>
		<div class="city form-section row clearfix">
			<?php $this->box_search_artwork_place('search'); ?>
		</div>
		<h3><?php _e('Organizations', 'arteforadomuseu'); ?></h3>
		<div class="organizations form-section row clearfix">
			<?php $this->box_search_artwork_organizations('search'); ?>
		</div>
		<h3><?php _e('Languages', 'arteforadomuseu'); ?></h3>
		<div class="languages form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_languages('search'); ?>
			</div>
		</div>
		<h3><?php _e('Licenses', 'arteforadomuseu'); ?></h3>
		<div class="licenses form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_licenses('search'); ?>
			</div>
		</div>
		<h3><?php _e('Resources', 'arteforadomuseu'); ?></h3>
		<div class="resources form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_resources('search'); ?>
			</div>
		</div>
		<h3><?php _e('Input and output', 'arteforadomuseu'); ?></h3>
		<div class="input form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_input('search'); ?>
			</div>
		</div>
		<h3><?php _e('Other', 'arteforadomuseu'); ?></h3>
		<div class="other form-section row clearfix">
			<div class="clearfix">
				<?php $this->box_artwork_other('search'); ?>
			</div>
		</div>
		<?php
	}

	//++ Changes default search query
	function advanced_search_query( $query ) {
		if (isset($_GET['s']) && empty($_GET['s']) && $query->is_main_query()) {
			$query->is_search = true;
			$query->is_home = false;
		}

		if ($query->is_search()) {
			$query->set('post_type', 'post');
			$meta_query = array();

			$fields = array(
				'artwork_country', 
				'artwork_city', 
				'artwork_organization', 
				'artwork_organizations', 
				'artwork_funders', 
				'artwork_collections', 
				'artwork_site_accessibility', 
				'artwork_site_time_markers', 
				'artwork_interface_languages', 
				'artwork_resource_languages', 
				'artwork_site_license', 
				'artwork_resource_license', 
				'artwork_resource_types', 
				'artwork_academic_level',
				'artwork_subject_areas',
				'artwork_output_interfaces',
				'artwork_input_by_users'
			);

			foreach ($fields as $field) {

				if ($_REQUEST[$field] != '') {

					if ($field == 'artwork_country') {
						$artist_artworks = get_post_meta($_REQUEST[$field], '_artworks', false);
						if (empty($artist_artworks) && is_numeric($field)) {
							$query->set('post__in', array(0));
						} else {
							$query->set('post__in', $artist_artworks);
						}
					} elseif (
						$field == 'artwork_interface_languages' ||
						$field == 'artwork_resource_languages' ||
						$field == 'artwork_site_license' ||
						$field == 'artwork_resource_license' ||
						$field == 'artwork_resource_types' ||
						$field == 'artwork_academic_level' ||
						$field == 'artwork_subject_areas' ||
						$field == 'artwork_output_interfaces' ||
						$field == 'artwork_input_by_users'
					) {
						foreach ($_REQUEST[$field] as $field_row) {
							$meta_query[] = array(
								'key' => $field,
								'value' => $field_row,
								'compare' => 'LIKE',
							);
						}
					} else {
						$meta_query[] = array(
							'key' => $field,
							'value' => $_REQUEST[$field],
							'compare' => 'LIKE'
						);
					}
				}
			}

			$meta_query['relation'] = 'AND';
			$query->set( 'meta_query', $meta_query );

			return $query;
		}
	}

	/*
	 * Ajax stuff
	 */
	function setup_ajax() {
		add_action('wp_ajax_nopriv_submit_artwork', array($this, 'ajax_add_artwork'));
		add_action('wp_ajax_submit_artwork', array($this, 'ajax_add_artwork'));
	}

	function ajax_response($data) {
		header('Content Type: application/json');
		echo json_encode($data);
		exit;
	}

	function ajax_add_artwork() {

		define('AFDM_ALLOWED_AJAX', true);
		define('AFDM_FRONTEND_SUBMIT', true);

		$data = $_REQUEST;

		if(!$data['title_en'] || !$data['title_es'] || !$data['title_pt'])
			$this->ajax_response(array('error_msg' => __('You must enter a title', 'arteforadomuseu')));

		if(!$data['artwork_country'])
			$this->ajax_response(array('error_msg' => __('You must select a country', 'arteforadomuseu')));

		$post_id = wp_insert_post(array(
			'post_type' => $this->post_type,
			'post_status' => 'pending',
			'post_title' => '<!--:en-->'.$data['title_en'].'<!--:--><!--:es-->'.$data['title_es'].'<!--:--><!--:pt-->'.$data['title_pt'].'<!--:-->',
			'post_content' => '<!--:en-->'.$data['content_en'].'<!--:--><!--:es-->'.$data['content_es'].'<!--:--><!--:pt-->'.$data['content_pt'].'<!--:-->'
		));

		$this->save_artwork($post_id);

		$this->ajax_response(array('success_msg' => __('Congratulations! Your OER is now pending approval, soon you\'ll see it here.', 'arteforadomuseu')));

	}

	/*
	 * Functions
	 */

	function get_popular($amount = 5) {
		$query = array(
			'post_type' => $this->post_type,
			'posts_per_page' => $amount,
		);
		return get_posts(afdm_get_popular_query($query));
	}

/*	function get_artwork_dimensions($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_dimensions', true);
	}

	function get_artwork_creation_date($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_date_creation', true);
	}

	function get_artwork_termination_date($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_date_termination', true);
	}

	function is_artwork_currently_active($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_currently_active', true);
	}

	function get_artwork_videos($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_videos', true);
	}

	function get_artwork_featured_video($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_featured_video', true);
	}
*/
	function get_artwork_links($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_links', true);
	}

	function get_artwork_featured_link($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_featured_link', true);
	}

	function get_artwork_url($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_url', true);
	}

	function get_artwork_contact($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_contact', true);
	}

	function get_artwork_city($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_city', true);
	}

	function get_artwork_organization($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_organization', true);
	}

	function get_artwork_organizations($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_organizations', true);
	}

	function get_artwork_funders($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_funders', true);
	}

	function get_artwork_interface_languages($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_interface_languages', true);
	}

	function get_artwork_resource_languages($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_resource_languages', true);
	}

	function get_artwork_site_license($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_site_license', true);
	}

	function get_artwork_resource_license($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_resource_license', true);
	}

	function get_artwork_resource_types($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_resource_types', true);
	}

	function get_artwork_academic_level($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_academic_level', true);
	}

	function get_artwork_subject_areas($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_subject_areas', true);
	}

	function get_artwork_output_interfaces($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_output_interfaces', true);
	}

	function get_artwork_input_by_users($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_input_by_users', true);
	}

	function get_artwork_collections($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_collections', true);
	}

	function get_artwork_site_accessibility($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_site_accessibility', true);
	}

	function get_artwork_site_time_markers($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;
		return get_post_meta($post_id, 'artwork_site_time_markers', true);
	}

	function get_artwork_country($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;

		$artists = get_posts(array(
			'post_type' => 'country',
			'meta_query' => array(
				array(
					'key' => '_artworks',
					'value' => $post_id
				)
			),
			'posts_per_page' => -1
		));

		return $artists[0]->ID;
	}

/*	function get_artwork_styles() {
		return false;
	}

	function get_artwork_style_names() {
		return false;
	}
*/
	function get_artwork_images($post_id = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;

		$images = get_posts(array(
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'post_status' => null,
			'posts_per_page' => -1
		));

		if(!$images)
			return false;

		$formatted_images = array();
		foreach($images as $image) {
			$formatted = $this->_get_artwork_image($image->ID);
			if($formatted)
				$formatted_images[] = $formatted;
		}

		return $formatted_images;
	}

	function _get_artwork_image($attachment_id) {

		if(!$attachment_id)
			return false;

		$image = array(
			'thumb' => wp_get_attachment_image_src($attachment_id, 'thumbnail'),
			'large' => wp_get_attachment_image_src($attachment_id, 'large'),
			'full' => wp_get_attachment_image_src($attachment_id, 'full')
		);

		if(!$image['thumb'])
			return false;

		return $image;
	}

}

$artworks = new ArteForaDoMuseu_Artworks();

/*function afdm_get_videos($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_videos($post_id);
}

function afdm_get_featured_video_id($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_featured_video($post_id);
}
*/
function afdm_get_links($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_links($post_id);
}

function afdm_get_url($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_url($post_id);
}

function afdm_get_contact($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_contact($post_id);
}

function afdm_get_city($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_city($post_id);
}

function afdm_get_organization($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_organization($post_id);
}

function afdm_get_organizations($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_organizations($post_id);
}

function afdm_get_funders($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_funders($post_id);
}

function afdm_get_interface_languages($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_interface_languages($post_id);
}

function afdm_get_resource_languages($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_resource_languages($post_id);
}

function afdm_get_site_license($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_site_license($post_id);
}

function afdm_get_resource_license($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_resource_license($post_id);
}

function afdm_get_resource_types($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_resource_types($post_id);
}

function afdm_get_academic_level($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_academic_level($post_id);
}

function afdm_get_subject_areas($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_subject_areas($post_id);
}

function afdm_get_output_interfaces($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_output_interfaces($post_id);
}

function afdm_get_input_by_users($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_input_by_users($post_id);
}

function afdm_get_collections($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_collections($post_id);
}

function afdm_get_site_accessibility($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_site_accessibility($post_id);
}

function afdm_get_site_time_markers($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_site_time_markers($post_id);
}
/*
function afdm_get_artwork_dimensions($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_dimensions($post_id);
}

function afdm_get_creation_date($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_creation_date($post_id);
}

function afdm_get_termination_date($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_termination_date($post_id);
}

function afdm_is_artwork_active($post_id = false) {
	global $artworks;
	return $artworks->is_artwork_currently_active($post_id);
}

*/
function afdm_get_artwork_images($post_id = false) {
	global $artworks;
	return $artworks->get_artwork_images($post_id);
}