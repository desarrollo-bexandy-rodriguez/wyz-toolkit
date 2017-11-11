<?php
/**
 * Offer creator.
 *
 * @package wyz
 */

/**
 * Class WyzOffer.
 */
class WyzOffer{

	/**
	 * Creates offer to display in offer archives page and offers slider.
	 *
	 * @param integer $id the offer id.
	 */
	public static function wyz_the_offer( $id, $is_arch, $template_type = '' ) {
		if ( '' == $template_type )
			$template_type = ( function_exists( 'wyz_get_theme_template' ) ? wyz_get_theme_template() : 1 );
		$template_type == 1 ? self::the_offer_1( $id, $is_arch ) : self::the_offer_2( $id, $is_arch );
	}

	private static function the_offer_1( $id, $is_arch ) {
		$ttl = get_the_title( $id );
		$exrpt = get_post_meta( $id, 'wyz_offers_excerpt', true );
		$desc_arr = array_slice( explode( ' ', wp_strip_all_tags( apply_filters('the_content', get_post_field( 'post_content', $id ) ) )/*get_post_meta( $id, 'wyz_offers_description', true )*/ ), 0, 60 );
		$desc = implode( ' ', $desc_arr );
		if ( count( $desc_arr ) > 59 ) {
			$desc .= '...';
		}
		$bus_id = get_post_meta( $id, 'business_id', true );
		$icon = get_the_post_thumbnail( $bus_id, 'medium' );
		$logo_bg = get_post_meta( $bus_id, 'wyz_business_logo_bg', true );
		$image = get_post_meta( $id, 'wyz_offers_image_id', true );
		if ( '' != $image ) {
			$image = wp_get_attachment_image( $image, 'medium' );
		} else {
			$image = '<img src="'.WyzHelpers::get_default_image( 'offer' ).'"/>';
		}
		$dscnt = get_post_meta( $id, 'wyz_offers_discount', true );
		ob_start();?>

		<div id="post-<?php echo $id; ?>" class="wyz_offers type-wyz_offers sin-offer-item row<?php echo $is_arch ? ' the-offer' : '';?>">
			<div class="image <?php if ( 'on' == wyz_get_option( 'resp' ) ) { echo 'col-md-5 col-xs-12'; } else { echo 'col-xs-5'; }?> float-right"><?php echo $image;
				if ( 0 < $dscnt ) { ?>
				<span class="offer-label"><?php esc_html_e( 'DISCOUNT', 'wyzi-business-finder' );?> <?php echo esc_html( $dscnt );?>%</span>
				<?php }?>
			</div>
			<div class="content <?php if ( 'on' == wyz_get_option( 'resp' ) ) { echo 'col-md-7 col-xs-12'; } else { echo 'col-xs-7'; }?>">
				<div class="head fix">
					<?php if ( $icon && '' != $icon ) {?>
					<div class="logo float-right"><?php echo $icon; ?></div>
					<?php }?>
					<div class="text float-left"><h3><?php echo esc_html( $ttl ); ?></h3><h4 class="wyz-secondary-color-text"><?php echo esc_html( $exrpt ); ?></h4></div>
				</div>
				<p><?php echo nl2br( esc_html( $desc ) ); ?></p>
				<a href="<?php echo esc_url( get_post_permalink( $id ) ); ?>" class="wyz-button wyz-secondary-color icon"><?php echo sprintf( esc_html__( 'view %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?> <i class="fa fa-angle-right"></i></a>
				<div class="offer-caps">
					<?php WyzPostShare::the_like_button( $id, 1 )?>
					<?php WyzPostShare::the_share_buttons( $id, 1, true );?>
				</div>
			</div>
		</div>
		<?php echo ob_get_clean();
	}

	private static function the_offer_2( $id, $is_arch ) {
		$ttl = get_the_title( $id );
		$exrpt = get_post_meta( $id, 'wyz_offers_excerpt', true );
		$desc_arr = array_slice( explode( ' ', wp_strip_all_tags( apply_filters('the_content', get_post_field( 'post_content', $id ) ) ) ), 0, 60 );
		$desc = implode( ' ', $desc_arr );
		if ( count( $desc_arr ) > 59 ) {
			$desc .= '...';
		}
		$image = get_post_meta( $id, 'wyz_offers_image_id', true );
		$image = wp_get_attachment_image( $image, 'medium', false, array( 'class' => 'image' ) );

		if ( ! $image || '' == $image ) {
			$image = '<img src="'.WyzHelpers::get_default_image( 'offer' ).'"/>';
			$image_class = '';
		}
		$dscnt = get_post_meta( $id, 'wyz_offers_discount', true );?>
		<div class="offer-wrapper mb-20">
			<div id="post-<?php echo $id; ?>" class="wyz_offers type-wyz_offers">
				<h3><a href="<?php echo get_the_permalink( $id );?>"><?php echo $ttl; ?></a></h3>
				<!-- Offer Banner -->
				<a href="<?php echo get_the_permalink( $id );?>" class="offer-banner mb-20">
				<?php echo $image;?>

				<?php if ( 0 < $dscnt ) { ?>
				<span><?php echo esc_html( $dscnt );?>%</span>
				<?php }?>
				</a>
				<?php if ( $is_arch ) { ?>
				<div class="offer-caps">
					<?php WyzPostShare::the_like_button( $id, 2 )?>
					<?php WyzPostShare::the_share_buttons( $id, 2, true );?>
				</div>
				<?php }?>
				<h5><?php echo esc_html( $exrpt );?></h5>
			 	
				<p><?php echo $desc; ?></p>
				<?php if ( !$is_arch ) { ?>
				<div class="offer-caps not-arch">
					<?php WyzPostShare::the_like_button( $id, 2 )?>
					<?php WyzPostShare::the_share_buttons( $id, 2, true );?>
				</div>
				<?php }?>
				<div></div>
			</div>
		</div>
		<?php
	}



	/**
	 * Display all Offers for a specific Business.
	 *
	 * @param integer $business_id the Business id to get the Offers for.
	 */
	public static function wyz_the_business_all_offers( $business_id ) {
		$offers_ids = self::wyz_get_business_all_offers_IDs( $business_id );
		if ( empty( $offers_ids ) ) {
			WyzHelpers::wyz_info( sprintf( esc_html__( 'This %s has no %s to display.', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, WYZ_OFFERS_CPT ) );
			return;
		}

		foreach ( $offers_ids as $id ) {
			self::wyz_the_offer( $id, true );
		}
	}



	/**
	 * Gets all Offers related to Business with id: $business_id.
	 *
	 * @param integer $business_id the Business id to get the Offers for.
	 * @return array the offers ids.
	 */
	public static function wyz_get_business_all_offers_IDs( $business_id ) {

		if ( ! $business_id || 0 > $business_id ) {
			return array();
		}

		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => '-1',
			'post_status' => 'publish',
			'meta_key' => 'business_id',
			'meta_value' => $business_id
		) );

		$offer_ids = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$offer_ids[] = get_the_ID();
		}

		wp_reset_postdata();

		return $offer_ids;
	}

	/**
	 * Get image id from url.
	 *
	 * @param string $image_url the imsge url.
	 */
	private static function wyz_get_img_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		return isset( $attachment[0] ) ? $attachment[0] : '';
	}
}
?>
