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
					if(method_exists('RGS_CompanyForm', 'includeTemplate_fn')):
						RGS_CompanyForm::includeTemplate_fn();
					endif;
					?>
				</div><!-- .entry-content -->
			</article><!-- #post -->
			
				<?php 
				#get_template_part( 'content', 'page' ); 
				?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_footer(); ?>