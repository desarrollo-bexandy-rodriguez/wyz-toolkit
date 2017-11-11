<?php
/**
 * Template Name: Business Archive.
 *
 * @package wyz
 */

get_header();?>

<div class="wall-collection-area margin-bottom-100 margin-top-50">
	<div class="container">
		<div class="row">

			<!-- Left sidebar. -->
			<?php if ( 'right-sidebar' !== wyz_get_option( 'sidebar-layout' ) && 'full-width' !== wyz_get_option( 'sidebar-layout' ) ) :?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-categories-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-categories-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>

			<div class="offers-collections<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?> col-lg-12 col-md-12 col-xs-12"<?php } elseif ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-9 col-md-8 col-xs-12<?php } else { ?> col-xs-8<?php } ?>">
				<?php
				global $template_type;
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				}
				if ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				}
				if ( is_tag() ) {
					$tag = single_tag_title( '', false );
					$args = array(
						'tag' => $tag,
						'post_type' => 'wyz_business',
					);
					query_posts( $args );
				}

				if ( have_posts() && class_exists( 'WyzOffer' ) ) :
				 	while ( have_posts() ) :
				 		the_post();
			 			WyzOffer::wyz_the_offer( get_the_ID(), true, $template_type );
					endwhile;
				endif;
				wp_reset_postdata();

				if ( function_exists( 'wyz_pagination' ) ) wyz_pagination(); ?>

			</div>
			<!-- Right sidebar. -->
			<?php if ( 'right-sidebar' === wyz_get_option( 'sidebar-layout' ) ) :?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-categories-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-categories-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>
		</div>
	</div>
</div> 

<?php get_footer();
?>
