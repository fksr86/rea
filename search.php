<?php get_header(); ?>

<?php jeo_featured(); ?>

<section id="content">

	<?php do_action('afdm_before_content'); ?>

	<div class="child-section">
		<div class="section-title">
			<h2><?php _e('Results for: ', 'arteforadomuseu'); ?> "<?php echo $_GET['s']; ?>"</h2>
		</div>

<?php

$fields = array(
	'keyword', 
	'categories', 
	'country', 
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

        if (
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
            $meta_query[] = array(
                'key' => $field,
                'value' => $_REQUEST[$field],
                'compare' => 'IN',
            );

        } elseif ($field == 'max_age')
        {
            $meta_query[] = array(
                'key' => $field,
                'value' => $_REQUEST[$field], // This is OK, WP_Query will sanitize input!
                'compare' => '<=',
            );
        }
        else {
        // We have something to match, otherwise ignore the field...
            $meta_query[] = array(
                'key' => $field,
                'value' => $_REQUEST[$field], // This is OK, WP_Query will sanitize input!
                'compare' => 'IN',
            );
    }
}
$args = array(
    'post_type' => 'post',
    'posts_per_page' => -1, // -1 to display all results at once
    'order' => 'ASC',
    'meta_query' => $meta_query,
);
$query = new WP_Query($args);
?>
<div id="primary">
    <div id="content" role="main">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
                ?>
                <div class="post">
                    <?php the_title(); ?>
                </div>
                <?php
            endwhile; //end of the loop
        endif;
        ?>
    </div><!-- #content -->
</div><!-- #primary -->
 
<div id="secondary" class="widget-area" role="complementary">
    <form method="get">
 
        <label for="country">Country:</label>
        <input type="text" name="country" value="" />
 
        <label for="state">State: </label>
        <input type="text" name="state" value="" />
 
        <label for="zip">Zip: </label>
        <input type="text" name="zip" value="" />
 
        <label for="min_age">Min Age:</label>
        <input type="text" name="min_age" value="" />
 
 
        <label for="max_age">Max Age: </label>
        <input type="text" name="max_age" value="" />
 
        <label for="category">Category:</label><br />
        Category 1<input type="checkbox" value="5" name="category[]" /><br />
        Category 2<input type="checkbox" value="6" name="category[]" /><br />
        Category 3<input type="checkbox" value="7" name="category[]" /><br />
        Category 4<input type="checkbox" value="8" name="category[]" /><br />
        <input type="submit" value="Search" />
    </form>
</div>

<?php get_footer(); ?>
<?php } ?>