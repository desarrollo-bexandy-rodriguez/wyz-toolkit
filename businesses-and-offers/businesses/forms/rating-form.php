<div id="create-busi-rate" class="create-busi-post">
	<h2><?php esc_html_e( 'Create your rate', 'wyzi-business-finder' );?></h2>
	<form id="business-rate-form" class="create-busi-post-form">
		<textarea id="rate-txt" placeholder="<?php esc_html_e( 'write a post here', 'wyzi-business-finder' );?>..."></textarea>
		<div class="form-footer fix">
			<div id="bus-rate-form" class="ratings"  >
				<span>
					<input class="star star-5" name="rating" id="star-5" type="radio" value="5"/>
					<label class="star star-5 star-hov" for="star-5"></label>
					<input class="star star-4" name="rating" id="star-4" type="radio" value="4"/>
					<label class="star star-4 star-hov" for="star-4"></label>
					<input class="star star-3" name="rating" id="star-3" type="radio" value="3"/>
					<label class="star star-3 star-hov" for="star-3"></label>
					<input class="star star-2" name="rating" id="star-2" type="radio" value="2"/>
					<label class="star star-2 star-hov" for="star-2"></label>
					<input class="star star-1" name="rating" id="star-1" type="radio" value="1"/>
					<label class="star star-1 star-hov" for="star-1"></label>
				</span>
			</div>

			<?php
			$taxonomy = 'wyz_business_rating_category';
			$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
			$length = count( $tax_terms );
			
			echo '<select class="wyz-select" id="rating_category">';
			echo '<option value="">' . esc_html__( 'Main reason for your rating', 'wyzi-business-finder' ) . '</option>';
		
			for ( $i = 0; $i < $length; $i++ ) {
				if ( ! isset( $tax_terms[ $i ] ) ) {
					continue;
				}
				$obj = $tax_terms[ $i ];
				echo '<option value="' . $obj->term_id . '">' . $obj->name . '</option>';
				
				
			}
			echo '</select>';
			?>

			<input type="button" id="<?php echo (is_user_logged_in() ? 'busi_rate_submit' : 'non-logged-in-rate');?>" class="busi_post_submit wyz-primary-color-hover float-right wyz-prim-color-hover action-btn btn-bg-grey btn-hover-blue<?php if( ! is_user_logged_in() )echo ' rate-btn-no-log disabled';?>" value="<?php esc_html_e( 'Rate', 'wyzi-business-finder' );?>" />
			<input type="hidden" id="wyz_business_rate_nonce" value="<?php echo wp_create_nonce( 'wyz-business-rate-nonce' ); ?>"/>
		</div>
	</form>
</div>