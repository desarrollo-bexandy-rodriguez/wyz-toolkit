<?php
/**
 * Offers table display in 'user account' page.
 *
 * @package wyz
 */

/**
 * Display the offers in table frmat.
 */
function wyz_display_offers() {
	global $template_type;
	$btn_class = ( 2 == $template_type ? 'action-btn btn-bg-blue' : 'btn') . ' wyz-secondary-color-hover';
	ob_start();

	// Get pending offers.
	$query = new WP_Query( array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => '-1',
		'author' => get_current_user_id(),
		'post_status' => 'pending',
		'meta_query' =>array(
			array (
				'key' => 'business_id',
				'value' => $_GET[ WyzQueryVars::ManageBusiness ],
			)
		),
	) );
	$pending = false;
	if ( $query->have_posts() ) : ?>
		
	<div class="section-title col-xs-12 margin-bottom-50"><div class="row">
		<h1><?php echo ( sprintf( esc_html__( 'PENDING %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) );?></h1>
	</div></div>
	<!-- pending offers -->
	<div class="publish-offers col-xs-12">
	<div class="row">
	
		<?php while ( $query->have_posts() ) :
			$query->the_post();
			$curr_id = get_the_ID();
			$curr_post = get_post( $curr_id );
			$icon_id = get_post_meta( $curr_id, 'wyz_offers_image_id', true );
			if ( current_user_can( 'manage_options' ) || get_current_user_id() == $curr_post->post_author ) :
				if ( ! $pending ) {
					$pending = true; ?>
				<?php } ?>
		<div class="sin-pub-offer">
			<div class="logo"><?php echo  wp_get_attachment_image( $icon_id, 'thumbnail', true );?></div>
			<div class="title"><h4><?php the_title(); ?></h4></div>
			<div class="buttons">
				<a href="<?php echo esc_url( get_post_permalink() ); ?>" class="<?php echo $btn_class;?>"><?php esc_html_e( 'view', 'wyzi-business-finder' );?></a>
				<?php 
				if ( 'on' === get_option( 'wyz_offer_editable' ) || current_user_can( 'manage_options' ) ) { ?>
				<a href="<?php echo WyzHelpers::add_clear_query_arg( array( 'edit-offer' => $curr_id ) ); ?>" class="<?php echo $btn_class;?>" ><?php esc_html_e( 'edit', 'wyzi-business-finder' );?></a>
				<?php }?>
				<?php if ( ! ( 'trash' == get_post_status() ) ) {?>
				<a href="<?php echo esc_url( get_delete_post_link( $curr_id ) ); ?>" class="<?php echo $btn_class;?>" onclick="return confirm('<?php esc_html_e( 'Are you sure you wish to delete', 'wyzi-business-finder' );?> : <?php echo esc_js( get_the_title() ); ?>?')"><?php esc_html_e( 'delete', 'wyzi-business-finder' );?></a>
				<?php }?>
			</div>
		</div>
		<?php endif;

		endwhile;
		wp_reset_postdata();?>
	</div>
	</div>
			
	<?php endif;

	// Get published offers.
	$query = new WP_Query( array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => '-1',
		'author' => get_current_user_id(),
		'post_status' => 'publish',
		'meta_query' =>array(
			array (
				'key' => 'business_id',
				'value' => $_GET[ WyzQueryVars::ManageBusiness ],
			)
		),
	) );

	if ( $query->have_posts() ) : ?>
		
	<div class="section-title col-xs-12 margin-bottom-50"><div class="row">
		<h1><?php esc_html_e( 'PUBLISHED OFFERS', 'wyzi-business-finder' );?></h1>
	</div></div>
	<!-- published offers -->
	<div class="publish-offers col-xs-12">
	<div class="row">
		<?php while ( $query->have_posts() ) :

			$query->the_post();
			$curr_id = get_the_ID();
			$curr_post = get_post( $curr_id );
			$icon_id = get_post_meta( $curr_id, 'wyz_offers_image_id', true );
			if ( current_user_can( 'manage_options' ) || get_current_user_id() == $curr_post->post_author ) :
				$edit_post = '?edit-offer=' . $curr_id;?>
				
			<div class="sin-pub-offer">
				<div class="logo"><?php echo wp_get_attachment_image( $icon_id, 'thumbnail', true );?></div>
				<div class="title"><h4><?php the_title(); ?></h4></div>
				<div class="buttons">
					<a href="<?php echo esc_url( get_post_permalink() ); ?>" class="<?php echo $btn_class;?>"><?php esc_html_e( 'view', 'wyzi-business-finder' );?></a>
					<?php 
					if ( 'on' === get_option( 'wyz_offer_editable' ) || current_user_can( 'manage_options' ) ) { ?>
					<a href="<?php echo WyzHelpers::add_clear_query_arg( array( 'edit-offer' => $curr_id ) ); ?>" class="<?php echo $btn_class;?>" ><?php esc_html_e( 'edit', 'wyzi-business-finder' );?></a>
					<?php }
					if ( ! ( 'trash' == get_post_status() ) ) {?>
					<a href="<?php echo esc_url( get_delete_post_link( $curr_id ) ); ?>" class="<?php echo $btn_class;?>" onclick="return confirm('<?php esc_html_e( 'Are you sure you wish to delete', 'wyzi-business-finder' );?> : <?php echo esc_js( get_the_title() );?>?')"><?php esc_html_e( 'delete', 'wyzi-business-finder' );?></a>
					<?php }?>
				</div>
			</div>
			<?php  endif;

		endwhile;
		wp_reset_postdata();?>

	</div>
	</div>
	
	<?php else :
		WyzHelpers::wyz_info( esc_html( get_option( 'wyz_offer_no_offers' ) ) );
	endif; ?>

	<?php if ( $pending ) : 
		WyzHelpers::wyz_info( sprintf( esc_html__( 'You can\'t add a new %s while having ones pending for review', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) );
	else : 

		if (WyzHelpers::wyz_sub_can_bus_owner_do(get_current_user_id(),'wyzi_sub_business_can_create_offers')) {		?>

		<a class="wyz-primary-color wyz-prim-color btn-square" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::AddNewOffer => true, WyzQueryVars::BusinessId => $_GET[ WyzQueryVars::ManageBusiness ] ) );?>"><?php echo sprintf( esc_html__( 'Add New %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?></a>
	<?php } endif; ?>
	
	<?php return ob_get_clean();
} ?>
