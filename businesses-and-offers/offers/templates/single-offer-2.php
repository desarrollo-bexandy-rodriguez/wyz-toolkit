<?php //WyzHelpers::wyz_the_business_subheader( $business_id );?>
<div class="page-area section pt-90 pb-90">
	<div class="container">
		<div class="row">
			<div class="wall-offer-wrapper">
				<div id="post-<?php echo $id; ?>" <?php post_class( $post_class ); ?>>
					<h3><?php the_title(); ?></h3>

					<div class="offer-banner-text mb-30">
					<h2><?php if ( 0 < $dscnt ) {
						echo sprintf( esc_html__( 'DISCOUNT %d%%', 'wyzi-business-finder' ), esc_html( $dscnt ) );
					} else {
						 $terms = wp_get_post_terms( $id, 'offer-categories' );
						 if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						 	$terms = $terms[0];
						 	echo $terms->name;
						 }
					}?></h2>
					</div>

					<!-- Offer Banner -->
					<a href="#" class="offer-banner mb-20">
					<?php echo $img;
					if ( 0 < $dscnt ) {?>
					<span><?php echo esc_html( $dscnt );?>%</span>
					<?php }?>
					</a>

					<div class="offer-caps">
						<?php WyzPostShare::the_like_button( $id, 2 )?>
						<?php WyzPostShare::the_share_buttons( $id, 2, true );?>
						<div class="clear"></div>
					</div>
					<h5><?php echo esc_html( $exrpt );?></h5>
			 		<?php //WyzPostShare::the_share_buttons( $id, 2, true );?>
					<p><?php echo $desc; ?></p>
				</div>
			</div>

			<?php WyzHelpers::the_business_sidebar( $business_id );?>
		</div>
	</div>
</div>