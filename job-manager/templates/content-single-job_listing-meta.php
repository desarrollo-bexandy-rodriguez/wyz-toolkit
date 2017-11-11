<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

do_action( 'single_job_listing_meta_before' ); ?>

<ul class="job-listing-meta meta company">
	<?php do_action( 'single_job_listing_meta_start' ); ?>

	<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
		<?php $types = wpjm_get_the_job_types(); ?>
		<?php if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>

			<li class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></li>

		<?php endforeach; endif; ?>
	<?php } ?>

	<li class="location"><?php the_job_location(); ?></li>

	<li class="date-posted"><?php the_job_publish_date(); ?></li>

	<?php if ( is_position_filled() ) : ?>
		<li class="position-filled"><?php _e( 'This position has been filled', 'wp-job-manager' ); ?></li>
	<?php elseif ( ! candidates_can_apply() && 'preview' !== $post->post_status ) : ?>
		<li class="listing-expired"><?php _e( 'Applications have closed', 'wp-job-manager' ); ?></li>
	<?php endif; ?>

	<p class="name">
		<?php if ( $website = get_the_company_website() ) : ?>
			<a class="website" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow"><?php _e( 'Website', 'wp-job-manager' ); ?></a>
		<?php endif; ?>
		<?php the_company_twitter(); ?>
	</p>

	<?php do_action( 'single_job_listing_meta_end' ); ?>
</ul>

<?php do_action( 'single_job_listing_meta_after' ); ?>