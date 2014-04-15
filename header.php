<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php
	global $page, $paged;

	wp_title( '|', true, 'right' );

	bloginfo( 'name' );

	$site_description = get_bloginfo('description', 'display');
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	if ( $paged >= 2 || $page >= 2 )
		echo ' | Página ' . max($paged, $page);

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/img/favicon.ico" type="image/x-icon" />
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="bhásia, Subodh Gupta, Tatzu Nishi, Jennifer Wen Ma, Zhang Huan, belo horizonte, arte, arte pública, espaço público, exposição, marcello dantas, artes plásticas, art, public space, expo">
<?php wp_head(); ?>
</head>
<body <?php body_class(get_bloginfo('language')); ?>>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=450923021667564";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<header id="masthead">
		<div class="container">
			<div class="logo">
				<div class="site-meta">
					<h1>
						<?php if ( get_theme_mod( 'bh_logo' ) ) : ?>
							<a href="<?php echo home_url('/'); ?>" title="<?php bloginfo('name'); ?>">
								<img src="<?php echo get_theme_mod( 'bh_logo' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" class="scale-with-grid" />
							</a>
						<?php else : ?>
							<a href="<?php echo home_url('/'); ?>" title="<?php bloginfo('name'); ?>">
								<?php bloginfo('name'); ?>
							</a>
						<?php endif; ?>
					</h1>
				</div>
			</div>
			<div class="two columns">
				<?php afdm_city_selector(); ?>
			&nbsp;</div> 
			<div class="main-menu">
			<?php if(function_exists('qtrans_getLanguage')) : ?>
					<nav id="langnav">
						<ul>
							<?php
							global $q_config;
							if(is_404()) $url = get_option('home'); else $url = '';
							$current = qtrans_getLanguage();
							foreach($q_config['enabled_languages'] as $language) {
								$attrs = '';
								if($language == $current)
									$attrs = 'class="active"';
								echo '<li><a href="' . qtrans_convertURL($url, $language) . '" ' . $attrs . '>' . $language . '</a></li>';
							}
							?>
						</ul>
					</nav>
				<?php endif; ?>
				<div id="masthead-nav">
					<div class="clearfix">
						<nav id="main-nav">
							<ul>
								<!-- <li><a href="<?php echo afdm_artists_get_archive_link(); ?>"><?php _e('Artists', 'arteforadomuseu'); ?></a></li>
								<li><a href="<?php echo afdm_artguides_get_archive_link(); ?>"><?php _e('Art guides', 'arteforadomuseu'); ?></a></li> -->
								<?php
								$categories = get_categories();
								if($categories) :
									?>
									<li class="categories">
										<?php
										if(is_singular('country')) : 
											$current_country = get_the_ID(); ?>
											<a href="<?php echo get_permalink(); ?>" class="current-menu-item"><?php the_title(); ?> <span class="lsf">&#xE03e;</span></a>
										<?php else : ?>
											<a href="#"><?php _e('Countries', 'arteforadomuseu'); ?> <span class="lsf">&#xE03e;</span></a>
										<?php endif; ?>
										<ul class="category-list">
											<?php if($current_country) : ?>
												<?php /* <li class="tip"><?php _e('<!--:en-->Choose another category:<!--:--><!--:es-->Selecciona otra categoría<!--:--><!--:pt-->Escolha outra categoria:<!--:-->', 'arteforadomuseu'); ?></li> */ ?>
											<?php endif; ?>
											<?php $countries = get_posts(array(
												'post_type' => 'country',
												'post_status' => array('publish', 'private', 'pending', 'draft', 'future'),
												'posts_per_page' => -1,
												'not_geo_query' => 1,
												'orderby' => 'title',
												'order' => 'ASC'
											));
											?>
											<?php foreach($countries as $country) : ?>
												<?php if($current_country && $country->ID == $current_country) continue; ?>
												<li>
													<a href="<?php echo get_permalink($country->ID); ?>"><?php echo get_the_title($country->ID); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									</li>
								<?php endif; ?>
							</ul>

							<?php wp_nav_menu(array('theme_location' => 'header_menu')); ?>
						</nav>
						<?php get_search_form(); ?>
						<?php afdm_get_user_menu(); ?>
					</div>
				</div>

			</div>
		</div>
	</header>

	