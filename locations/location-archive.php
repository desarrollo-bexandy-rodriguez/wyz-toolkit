<?php
/**
 * Archive page template
 *
 * @package wyz
 */

get_header();

global $template_type;?>

<div class="margin-bottom-100 margin-top-50">
	<div class="container">
		<div class="row">

			<?php get_template_part( 'sidebar/left-sidebar' ); ?>

			<div class="<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?>col-md-12<?php } elseif ( 'off' === wyz_get_option( 'resp' ) ) { ?>col-xs-8<?php } else { ?>col-lg-9 col-md-8 col-xs-12<?php } ?>">

				<?php if ( have_posts() ) :
					the_post();?>

				<!-- Single Blog -->
				<div class="sin-blog sin-loc margin-bottom-50">
				<?php if ( has_post_thumbnail() ) {?>
					<div class="blog-image"><?php the_post_thumbnail( 'large' );?></div>
				<?php }?>
					
					<div class="content">
						<h2 class="title"><?php esc_html( the_title() );?></h2>
						<p><?php the_content();?></p>
					</div>
				</div>
				<?php 
				$count = 0;
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				}
				$args = array(
					'post_type' => 'wyz_business',
					'paged' => $paged, 
					'meta_query' => array(
						array( 'key' => 'wyz_business_country', 'value' => get_the_ID() ),
					),
				);
				$tmp_query;
				global $wp_query;
				$the_query = new WP_Query( $args );
				$post_ids = array();
				if ( $the_query->have_posts() ) :
					if ( $template_type == 1) {
						while ( $the_query->have_posts() ) :
							$the_query->the_post();
							echo WyzBusinessPost::wyz_create_business();
						endwhile;
					} elseif( $template_type == 2) {
						$post_ids = wp_list_pluck( $the_query->posts, 'ID' );
						if ( function_exists( 'wyz_get_option' ) ) {
							$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
							if ( '' != $grid_alias )
								echo do_shortcode( '[ess_grid alias="' . $grid_alias . '" posts='.implode(',',$post_ids).']' );
						}
					}
					$tmp_query = $wp_query;
					$wp_query = $the_query;
					if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();
					$wp_query = $tmp_query;
					wp_reset_postdata();
				endif;
				
				endif; ?>
			</div>

			<?php get_template_part( 'sidebar/right-sidebar' ); ?>
		</div>
	</div>
</div>

<?php get_footer();
