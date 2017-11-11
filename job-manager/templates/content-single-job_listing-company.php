<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! get_the_company_name() ) {
	return;
}
?>
<div class="company">
	<?php
	$owner_business = get_post_meta( get_the_ID(), '_wyz_job_listing', true );
	if ( ! empty( $owner_business ) ) {
		$title = sprintf( __( '<span>Belongs to %s:</span> %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, '<a href="' . get_the_permalink( $owner_business ) . '">' . get_the_title( $owner_business ) . '</a>' );?>
		<div class="job-owner-business">
		<?php echo apply_filters( 'wyz_business_in_job_title', $title, get_the_ID() );?>
		</div><div class="clear"></div>
	<?php }
	the_company_tagline( '<strong>', '</strong>' );
	the_company_video(); ?>
</div>