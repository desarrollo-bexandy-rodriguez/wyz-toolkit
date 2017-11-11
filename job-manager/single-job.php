<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wp_reset_postdata();
get_header();

global $post;?>
<div class="blog-page-area margin-bottom-100 margin-top-50 page-area section pt-90 pb-90">
	<div class="container">
		<div class="row">
			<div class="single_job_listing">
				<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) :
				WyzHelpers::wyz_warning( __( 'This listing has expired.', 'wyzi-business-finder' ) );
				else : ?>
					<div class="job-header">
						<?php the_company_logo( 'medium' );?>
						<div class="job-meta-header">
							<h2><?php wpjm_the_job_title();?></h2>
							<?php the_company_name( '<h3>', '</h3>' );?>
						</div>
						<?php  if ( candidates_can_apply() ) get_job_manager_template( 'job-application.php' );?>
						<div class="clear fix"></div>
					</div>
					<?php
						do_action( 'single_job_listing_start' );
					?>

					<div class="job_description">
						<?php wpjm_the_job_description($post); ?>
					</div>

					<?php do_action( 'single_job_listing_end' ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>


<?php get_footer();?>