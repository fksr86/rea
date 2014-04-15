<?php
/*
 * Mousehover bubble content
 */
?>
<?php the_post_thumbnail('thumbnail'); ?>
<h4><?php the_title(); ?></h4>
<?php if(afdm_has_artist()) : ?>
	<div class="meta">
		<p class="artists"> <?php afdm_the_artist(); ?></p>
	</div>
<?php endif; ?>