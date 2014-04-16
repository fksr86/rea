<?php get_header(); ?>

<?php jeo_featured(); ?>

<section id="content">

	<?php do_action('afdm_before_content'); ?>

	<div class="child-section">
		<div class="section-title">
			<div class="navigation advanced-search" ><p><a href=""><?php _e('Change search filters', 'arteforadomuseu'); ?></a></p></div>
			<h2><?php _e('Results', 'arteforadomuseu'); ?></h2>
		</div>
		<?php get_template_part('loop'); ?>

	</div>

</section>


<?php get_footer(); ?>
