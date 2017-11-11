<?php
/**
 * Template Name: Business Template.
 *
 * Used to display single business page.
 *
 * @package wyz
 */

$business_path = plugin_dir_path( __FILE__ );

require_once( plugin_dir_path( __FILE__ ) ) . 'class-business-data.php';

global $business_data;

$template_type = 1;

if ( function_exists( 'wyz_get_theme_template' ) )
	$template_type = wyz_get_theme_template();

$business_data = new WyzSingleBusinessData( $template_type, $business_path, plugin_dir_url( __FILE__ ) );

/**
 * Load styles used by single business template.
 */
function wyz_load_styles() {
	wp_enqueue_media();
}
add_action( 'wp_head', 'wyz_load_styles' );

/**
 * Load styles used by single business template.
 */
function wyz_load_scripts() {
	wp_enqueue_script( 'wyz-nanogallery-js' );
}
add_action( 'wp_footer', 'wyz_load_scripts' );

/**
 * The data used by business scripts passed fom php to javascript.
 */
$id = get_the_ID();
$temp_id = $id;
function wyz_localize_scripts() {
	global $wpdb;
	global $temp_id;
	global $business_data;
	global $template_type;
	if ( has_post_thumbnail( $temp_id ) ) {
		$logo = wp_get_attachment_url( get_post_thumbnail_id( $temp_id ) );
	} else {
		$logo = WyzHelpers::get_default_image( 'business' );
	}
	$name = get_the_title( $temp_id );
	$logged_in_user = is_user_logged_in();

	$single_business_data = array(
		'userId' => get_current_user_ID(),
		'businessId' => $temp_id,
		'logo' => wp_json_encode( $logo ),
		'businessName' => $name,
		'isCurrentUserAuthor' => WyzHelpers::wyz_is_current_user_author( $temp_id ),
		'loggedInUser' => wp_json_encode( $logged_in_user ),
		'canRate' => ( $business_data->can_rate  ? 1 : 0 ),
		'postIndx' => -1,
		'template_type' =>$template_type,
		'ratingIndex' => -1,
		'menuSticky' => 'on' == wyz_get_option( 'sticky-menu' ),
		'havePosts' => $business_data->have_posts,
		'haveRatings' => $business_data->have_ratings,
		'attachments' => $business_data->attachments,
		'showMore' => esc_html__( 'Show More', 'wyzi-business-finder' ),
		'showLess' => esc_html__( 'Show Less','wyzi-business-finder' ),
		'noPostsMsg' => esc_html__( 'No more posts to show.', 'wyzi-business-finder' ),
		'insufPoints' => esc_html__( 'You don\'t have enough points to publish a post', 'wyzi-business-finder' ),
		'posting' => esc_html__( 'posting', 'wyzi-business-finder' ),
		'post' => esc_html__( 'Post', 'wyzi-business-finder' ),
		'emptyPost' => esc_html__( 'Cannot publish an empty post', 'wyzi-business-finder' ),
		'emptyComment' => esc_html__( 'Cannot publish an empty comment', 'wyzi-business-finder' ),
		'commentFailed' => esc_html__( 'Post comment failed', 'wyzi-business-finder' ),
		'chooseRating' => esc_html__( 'Please choose a rating.', 'wyzi-business-finder' ),
		'ratingInfo' => esc_html__( 'Please provide some info on why you gave a low raring.', 'wyzi-business-finder' ),
		'ratingReason' => esc_html__( 'Please select the main reason for your rating.', 'wyzi-business-finder' ),
		'ratingError' => esc_html__( 'An error occured while rating', 'wyzi-business-finder' ),
		'postDeleteError' => esc_html__( 'There was an error deleting the post', 'wyzi-business-finder' ),
		'emptyPostError' => esc_html__( 'Cannot submit an empty post', 'wyzi-business-finder' ),
		'unhandledError' => esc_html__( 'Unhandled error', 'wyzi-business-finder' ),
		'updateComplete' => esc_html__( 'Update Complete', 'wyzi-business-finder' ),
		'noMoreRatings' => esc_html__( 'No more ratings to show', 'wyzi-business-finder' ),
		'editPost' => esc_html__( 'Edit Post', 'wyzi-business-finder' ),
		'likePostq' => esc_html__( 'Like this post?', 'wyzi-business-finder' ),
		'loginLike' => esc_html__( 'Login to make your opinion count', 'wyzi-business-finder' ),
		'deletePost' => esc_html__( 'Delete Post', 'wyzi-business-finder' ),
		'disableComments' => esc_html__( 'Disable Comments', 'wyzi-business-finder' ),
		'enableComments' => esc_html__( 'Enable Comments', 'wyzi-business-finder' ),
		'signin' => esc_html__( 'Sign in', 'wyzi-business-finder' ),
		'uploadImage' => esc_html__( 'Upload Image', 'wyzi-business-finder' ),
		'isBusiness' => true,
		'loginPermalink' => home_url( '/signup/?action=login' ),
	);
	wp_localize_script( 'wyz_single_business_js', 'business', $single_business_data );
	wp_enqueue_script( 'wyz_single_business_js' );
	wp_enqueue_script( 'jQuery-inview' );
	wp_enqueue_script( 'wyz_gallery_data_js' );
}
add_action( 'wp_footer','wyz_localize_scripts' );

get_header();

require_once( plugin_dir_path( __FILE__ ) . "templates/single-business-$template_type.php" );

get_footer(); ?>
