<?php
/*
 * Template Name: Company Single
 * Template Post Type: company
 */

get_header(); ?>

	<div id="primary" class="site-content company-single-template">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">
					<?php
					if(method_exists('RGS_CompanySingle', 'includeTemplate_fn')):
						RGS_CompanySingle::includeTemplate_fn();
					endif;
					?>
				</div><!-- .entry-content -->
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_footer(); ?>