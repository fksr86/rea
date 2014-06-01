<?php get_header(); ?>

<?php if(have_posts()) : the_post(); ?>

	<?php
	$links = afdm_get_links();
	$url = afdm_get_url();
	$contact = afdm_get_contact();
//	$videos = afdm_get_videos();
	$images = afdm_get_artwork_images();
//	$featured_video_id = afdm_get_featured_video_id();
//	$dimensions = afdm_get_artwork_dimensions();
//	$creation_date = afdm_get_creation_date();
//	$termination_date = afdm_get_termination_date();
	$city = afdm_get_city();
	$organization = afdm_get_organization();
	$organizations = afdm_get_organizations();
	$funders = afdm_get_funders();
	$interface_languages = afdm_get_interface_languages();
	$resource_languages = afdm_get_resource_languages();
	$site_license = afdm_get_site_license();
	$resource_license = afdm_get_resource_license();
	$resource_types = afdm_get_resource_types();
	$academic_level = afdm_get_academic_level();
	$subject_areas = afdm_get_subject_areas();
	$output_interfaces = afdm_get_output_interfaces();
	$input_by_users = afdm_get_input_by_users();
	$collections = afdm_get_collections();
	$site_accessibility = afdm_get_site_accessibility();
	$site_time_markers = afdm_get_site_time_markers();
	?>

	<?php jeo_map(); ?>

	<article>
		<section id="content" class="single-post">
			<header class="single-post-header clearfix">
				<?php the_post_thumbnail('page-featured'); ?>
				<?php echo get_the_category_list(); ?>
				<h1><?php the_title(); ?></h1>
				<?php if(afdm_has_artist()) : ?>
					<p class="artists"><?php afdm_the_artist(); ?></p>
				<?php endif; ?>
			</header>
			<div class="menu">
				<?php if($links) : ?>
					<a href="#" data-subsection="resources"><span class="lsf">&#xE082;</span> <?php _e('Resources', 'arteforadomuseu'); ?></a>
				<?php endif; ?>
				<?php /*if($images) : ?>
					<a href="#" data-subsection="images"><span class="lsf">&#xE101;</span> <?php _e('Gallery', 'arteforadomuseu'); ?></a>
				<?php endif; ?>
				<?php if(jeo_is_streetview()) : ?>
					<a href="#" class="toggle-map" data-toggled-text="<?php _e('StreetView', 'arteforadomuseu'); ?>" data-default-text="<?php _e('Map', 'arteforadomuseu'); ?>"><span class="lsf">&#xE08b;</span> <span class="label"><?php _e('Map', 'arteforadomuseu'); ?></span></a>
				<?php endif; */?>
				<a href="#" data-subsection="comments"><span class="lsf">&#xE035;</span> <?php _e('Comments', 'arteforadomuseu'); ?></a>
			</div>
			<?php if($dimensions || $creation_date) : ?>
				<section class="post-data clearfix">
					<?php if($dimensions) : ?>
						<div class="dimensions">
							<h4><?php _e('Dimensions', 'arteforadomuseu'); ?></h4>
							<p>
								<?php echo $dimensions; ?>
							</p>
						</div>
					<?php endif; ?>
					<?php if($creation_date) : ?>
						<div class="dates">
							<h4><?php _e('Dates', 'arteforadomuseu'); ?></h4>
							<p class="creation">
								<strong><?php _e('Creation', 'arteforadomuseu'); ?></strong>
								<?php echo $creation_date; ?>
							</p>
							<?php if($termination_date) : ?>
								<p class="termination">
									<strong><?php _e('Termination', 'arteforadomuseu'); ?></strong>
									<?php echo $termination_date; ?>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>
			<section class="post-content">
				<?php the_content(); ?>
				<div class="post-fields">
					<?php if ($url) : ?><dd><a class="post-fields-url" href="<?php echo $url; ?>"><?php _e('URL', 'arteforadomuseu'); ?></a></dd><? endif; ?>
					<?php if ($contact) : ?><dd><a class="post-fields-contact" href="<?php echo $contact; ?>"><?php _e('Contact', 'arteforadomuseu'); ?></a></dd><? endif; ?>					
					<?php if ($city && $city[qtrans_getLanguage()] != '') : ?><dt><?php _e('City', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_array($city); ?></dd><? endif; ?>
					<?php if ($organization && $organization[qtrans_getLanguage()] != '') : ?><dt><?php _e('Organization', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_array($organization); ?></dd><? endif; ?>
					<?php if ($organizations && $organizations[qtrans_getLanguage()] != '') : ?><dt><?php _e('Organizations/Collaborators', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_array($organizations); ?></dd><? endif; ?>
					<?php if ($funders && $funders[qtrans_getLanguage()] != '') : ?><dt><?php _e('Funders', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_array($funders); ?></dd><? endif; ?>
					<?php if ($interface_languages) : ?><dt><?php _e('Interface languages', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_lang($interface_languages); ?></dd><? endif; ?>
					<?php if ($resource_languages) : ?><dt><?php _e('Resource languages', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_lang($resource_languages); ?></dd><? endif; ?>
					<?php if ($site_license) : ?><dt><?php _e('Site license', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_lic($site_license); ?></dd><? endif; ?>
					<?php if ($resource_license) : ?><dt><?php _e('Resource license', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_lic($resource_license); ?></dd><? endif; ?>
					<?php if ($resource_types) : ?><dt><?php _e('Resource types', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_res($resource_types); ?></dd><? endif; ?>
					<?php if ($academic_level) : ?><dt><?php _e('Academic level', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_acad($academic_level); ?></dd><? endif; ?>
					<?php if ($subject_areas) : ?><dt><?php _e('Subject areas', 'arteforadomuseu'); ?></dt><dd><?php if(is_array($subject_areas)) echo mira_lang_vocab_subj($subject_areas); else echo $subject_areas; ?></dd><? endif; ?>
					<?php if ($output_interfaces) : ?><dt><?php _e('Output interfaces', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_out($output_interfaces); ?></dd><? endif; ?>
					<?php if ($input_by_users) : ?><dt><?php _e('Input by users', 'arteforadomuseu'); ?></dt><dd><?php echo mira_lang_vocab_in($input_by_users); ?></dd><? endif; ?>
					<?php if ($collections) : ?><dt><?php _e('Collections', 'arteforadomuseu'); ?></dt><dd><?php echo $collections; ?></dd><? endif; ?>
					<?php if ($site_accessibility) : ?><dt><?php _e('Site accessibility', 'arteforadomuseu'); ?></dt><dd><?php echo $site_accessibility; ?></dd><? endif; ?>
					<?php if ($site_time_markers) : ?><dt><?php _e('Start year–End year (if ended)', 'arteforadomuseu'); ?></dt><dd><?php echo $site_time_markers; ?></dd><? endif; ?>
				</div>
				</dl>
				<?php the_terms($post->ID, 'style', '<p class="styles"><span class="lsf">&#xE128;</span> ' . __('Styles', 'arteforadomuseu') . ': ', ' ', '</p>'); ?>
			</section>
			<?php /*<aside class="actions clearfix">
				<?php do_action('afdm_loop_artwork_actions'); ?>
			</aside>*/ ?>
		</section>
		<?php if($links) : ?>
			<section id="resources" class="sub-content middle-content">
				<div class="content">
					<div class="sub-content-header">
						<a class="close" href="#"><?php _e('Close', 'arteforadomuseu'); ?> <span class="lsf">&#xE10f;</span></a>
						<h3><?php _e('Resources', 'arteforadomuseu'); ?></h3>
					</div>
					<ul class="video-list clearfix">
						<?php foreach($links as $link) : ?>
							<li><a href="<?php echo $link['url'] ; ?>" rel="external" target="_blank" title="<?php echo mira_lang_array($link['title']); ?>"><?php echo mira_lang_array($link['title']); ?></a><br><?php echo mira_lang_array($link['description']); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
		<?php endif; ?>
		<?php /* if($images) : ?>
			<section id="images" class="sub-content middle-content">
				<div class="content image-gallery">
					<div class="sub-content-header">
						<a class="close" href="#"><?php _e('Close', 'arteforadomuseu'); ?> <span class="lsf">&#xE10f;</span></a>
						<h3><?php _e('Image gallery', 'arteforadomuseu'); ?></h3>
					</div>
					<div class="image-stage-container">
						<div class="image-stage">
							<?php $image = $images[0]; ?>
							<a href="<?php echo $image['full'][0]; ?>" rel="shadowbox"><img src="<?php echo $image['large'][0]; ?>" /></a>
						</div>
					</div>
					<div class="image-list-container clearfix">
						<ul class="image-list">
							<?php foreach($images as $image) : ?>
								<li>
									<a href="<?php echo $image['large'][0]; ?>" data-full="<?php echo $image['full'][0]; ?>"><img src="<?php echo $image['thumb'][0]; ?>" /></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</section>
		<?php endif; */?>
		<section id="comments" class="sub-content middle-content">
			<div class="content">
				<div class="sub-content-header">
					<a class="close" href="#"><?php _e('Close', 'arteforadomuseu'); ?> <span class="lsf">&#xE10f;</span></a>
					<h3><?php _e('Comments', 'arteforadomuseu'); ?></h3>
				</div>
				<div class="clearfix">
					<?php comments_template(); ?>
				</div>
			</div>
		</section>
	</article>

<?php endif; ?>

<?php get_footer(); ?>