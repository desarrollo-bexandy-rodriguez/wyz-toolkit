<?php WyzHelpers::wyz_the_business_subheader( $business_id );?>
<div class="business-tab-area margin-bottom-100">
	<div class="container">
		<div class="row">

			<div class="business-sidebar-content-area margin-top-50">
				<?php WyzHelpers::the_business_sidebar( $business_id );?>
			
				<div class="<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?>col-md-9 <?php } else { ?>col-xs-9 <?php } ?>col-xs-12">
					<!-- Business Tab Content -->
					<div class="tab-content">
						<!-- Business Tab Wall -->
						<div class="tab-pane active row" id="wall">
							<!-- Offer Area -->
							<div id="post-<?php echo $id; ?>" <?php post_class( $post_class ); ?>>
								<div class="our-offer">
									<div class="title fix">
										<h3><?php the_title(); ?></h3>
										<h4 class="wyz-secondary-color-text"><?php echo esc_html( $exrpt );?></h4>
									</div>
									<div class="image"><?php echo $img;?></div>
									<div class="offer-caps">
										<?php WyzPostShare::the_like_button( $id, 1 )?>
										<?php WyzPostShare::the_share_buttons( $id, 1, true );?>
										<div class="clear"></div>
									</div>
									<div class="offer-discount"><p>
									<?php  if ( 0 < $dscnt ) { 
										echo esc_html__( 'DISCOUNT', 'wyzi-business-finder' ) . ' ' . esc_html( $dscnt ) . '%';
									} else {
										 $terms = wp_get_post_terms( $id, 'offer-categories' );
										 if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
										 	$terms = $terms[0];
										 	echo $terms->name;
										 }
									}?>
									</p>
									<div class="clear"></div>
									</div>
									<div class="content">
										<?php echo $desc; ?>
									</div>
								</div>
							</div>
							<div class="sidebar-container col-lg-4 col-md-5 col-xs-12">
							<?php if ( is_active_sidebar( 'wyz-single-business-sb' ) ) : ?>
								<div class="widget-area sidebar-widget-area">
									<?php dynamic_sidebar( 'wyz-single-business-sb' ); ?>
								</div>
							<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
