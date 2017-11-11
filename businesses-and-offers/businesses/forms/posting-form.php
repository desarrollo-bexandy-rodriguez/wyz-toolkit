<?php 
$can_post = true;

$user_id = get_current_user_id();
$post_cost = intval( get_option( 'wyz_business_post_cost' ) );
$points_available = get_user_meta( $user_id, 'points_available', true );
if ( '' == $points_available ) {
	$points_available = 0;
} else {
	$points_available = intval( $points_available );
	if ( $points_available < 0 ){
		$points_available = 0;
	}
}
if ( '' == $post_cost ) {
	$post_cost = 0;
} else {
	$post_cost = intval( $post_cost );
	if ( $post_cost < 0 ){
		$post_cost = 0;
	}
}
if ( $post_cost > $points_available ) {
	$can_post = false;
}
global $template_type;
if ( $can_post ) {?>
<div class="create-busi-post">
	<h4><?php esc_html_e( 'Create your post', 'wyzi-business-finder' );?></h4>
	<form id="business-post-form" class="create-busi-post-form">
		<textarea id="post-txt" class="wyz-input" placeholder="<?php esc_html_e( 'write a post here', 'wyzi-business-finder' );?>..."></textarea>
		<div class="form-footer fix">
				<button id="business-post-image" class="business-post-image" value=""><span></span>
				<i id="tc" class="fa fa-times"></i></button>
				<input type="hidden" id="business_post_image" value="" imgid=""/>
				<input type="button" id="busi_post_submit" class="busi_post_submit wyz-primary-color wyz-prim-color-hover action-btn btn-bg-grey btn-hover-blue" value="<?php esc_html_e('Post', 'wyzi-business-finder');?>" />
				<input type="hidden" id="wyz_business_post_nonce" value="<?php echo wp_create_nonce( 'wyz-business-post-nonce' ); ?>"/>
		</div>
	</form>
</div>
<?php } else {
	WyzHelpers::wyz_info( esc_html__( 'You don\'t have enough points to publish posts.', 'wyzi-business-finder' ), false, 'style="float: none;"' );
}?>