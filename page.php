<?php get_header(); ?>

<?php if(have_posts()) : the_post(); ?>
	<div class="single-post-container">
		<section id="content" class="single-post">
			<header class="single-post-header">
				<div class="container">
					<div class="twelve columns">
						<h1><?php the_title(); ?></h1>
					</div>
				</div>
			</header>
			<div class="container">
				<div class="eight columns">
					<?php the_content(); ?>
				</div>
				<div class="three columns offset-by-one">
					<aside id="sidebar">
						<ul class="widgets">
							<?php dynamic_sidebar('general'); ?>
						</ul>
					</aside>
				</div>
			</div>
		</section>
	</div>
<?php endif; ?>

<?php get_footer(); ?>