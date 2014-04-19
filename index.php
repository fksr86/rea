<?php get_header(); ?>

<?php jeo_featured(); ?>

<section id="content">

	<?php if(!is_paged()) : ?>

		<?php
		query_posts(array(
			'post_type' => 'country',
			'posts_per_page' => 4,
			'not_geo_query' => 1,
			'meta_query' => array(
				array(
					'key' => '_jeo_featured',
					'value' => 1
				)
			)
		));
		if(have_posts() && !get_query_var('city_not_found')) :
			?>
			<div class="featured-section">
				<div class="section-title">
					<h2><?php _e('Countries', 'arteforadomuseu'); ?></h2>
				</div>
				<?php get_template_part('loop', 'carousel'); ?>
			</div>
		<?php
		endif; 
		wp_reset_query();
		?>

		

	<?php endif; ?>

		<?php do_action('afdm_before_content'); ?>

	<div class="child-section">
		<?php get_template_part('loop'); ?>
	</div>
</section>

<?php get_footer(); ?>