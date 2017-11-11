<div id="business-post-edit" class="edit-overlay" style="display:none;">
	<div class="container">
		<div class="row">
			<div class="create-busi-post edit-busi-post">
				<button id="business-edit-post-cancel"><span></span>
							<i class="fa fa-times"></i></button>
				<h2><?php esc_html_e( 'Edit post', 'wyzi-business-finder' );?></h2>
				<form id="business-post-edit-form" class="create-busi-post-form">
					<textarea id="edit-post-txt" placeholder="<?php esc_html_e( 'write a post here', 'wyzi-business-finder' );?>..."></textarea>
					<div class="form-footer fix">
							<button id="business-edit-post-image" class="business-post-image" value=""><span></span>
							<i id="e-tc" class="fa fa-times"></i></button>
							<input type="hidden" id="business_edit_post_image" value="" imgid=""/>
							<input type="hidden" id="business_edit_post_id" value="" postid=""/>
							<input type="button" id="busi_edit_post_submit" class="busi_post_submit wyz-primary-color wyz-prim-color action-btn btn-bg-grey btn-hover-blue" value="<?php esc_html_e('Update', 'wyzi-business-finder');?>" />
							<input type="hidden" id="wyz_business_edit_post_nonce" value="<?php echo wp_create_nonce( 'wyz-business-post-nonce' ); ?>"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>