<?php
/**
 * Plugin's ajax handlers.
 *
 * @package wyz
 */

/**
 * Add wp ajax url.
 */
function wyz_add_ajaxurl_cdata_to_front() {
?>
	<script type="text/javascript">
	//<![CDATA[
	ajaxurl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
	ajaxnonce = <?php echo wp_json_encode( wp_create_nonce( 'wyz_ajax_custom_nonce' ) ); ?>;
	var currentUserID = <?php echo wp_json_encode( get_current_user_id() ); ?>;
	//]]>
	</script>

<?php }
add_action( 'wp_head', 'wyz_add_ajaxurl_cdata_to_front', 1 );


/**
 * Handles uploading images for business posts.
 */
function wyz_upload_business_gallery_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$bus_id = filter_input( INPUT_POST, 'bus_id' );
	$array = explode( ',', filter_input( INPUT_POST, 'imgs_ids' ) );
	update_post_meta( $bus_id, 'business_gallery_image', $array );

	wp_die( $array[0] );
}
add_action( 'wp_ajax_upattachments', 'wyz_upload_business_gallery_ajax' );
add_action( 'wp_ajax_nopriv_upattachments', 'wyz_upload_business_gallery_ajax' );


/**
 * Handles uploading business cover photo
 */
function wyz_upload_business_cover_photo_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$bus_id = filter_input( INPUT_POST, 'bus_id' );
	$array = explode( ',', filter_input( INPUT_POST, 'imgs_ids' ) );
	update_post_meta( $bus_id, 'wyz_business_header_image', wp_get_attachment_url( $array[0] ) );

	wp_die( $array[0] );
}
add_action( 'wp_ajax_up_business_cover_photo', 'wyz_upload_business_cover_photo_ajax' );
add_action( 'wp_ajax_nopriv_up_business_cover_photo', 'wyz_upload_business_cover_photo_ajax' );


/**
 * Handles uploading business posts.
 */
function wyz_upload_business_post_ajax() {
	if ( ! filter_input( INPUT_POST, 'nonce' ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'wyz-business-post-nonce' ) ) {
		wp_die( 'busted' );
	}

	$cost = 0;
	$points_available = 0;
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
		wp_die( -1 );
	}

	$post_data = array();
	$post_meta_data = array();

	if ( filter_input( INPUT_POST, 'post-txt' ) ) {
		$post_data['post_content'] = filter_input( INPUT_POST, 'post-txt' );
	}

	if ( filter_input( INPUT_POST, 'img' ) ) {
		$post_img = intval( filter_input( INPUT_POST, 'img' ) );
	}

	if ( ! isset( $post_data['post_content'] ) && ! isset( $post_img ) ) {
		wp_die( 'empty post' );
	}

	$business_id = intval( $_POST['id'] );
	if ( '' == $business_id || ! $business_id ) {
		wp_die( 'Error' );
	}

	$post_status = get_post_status( $business_id );

	if ( ! $post_status )
		wp_die( 'Error' );
	if ( 'publish' != $post_status )
		$post_status = 'pending';

	$post_meta_data['wyz_business_post_likes'] = array();
	$post_meta_data['wyz_business_post_likes_count'] = 0;
	$post_meta_data['business_id'] = $business_id;
	$vid = '';

	if ( isset( $post_data['post_content'] ) ) {
		$data = array( $post_data['post_content'], false );
		$data = wyz_split_glue_link( $data );
		$post_data['post_content'] = $data[0][0];
		$post_meta_data['vid'] = $data[1];
	} else {
		$post_data['post_content'] = '';
	}

	if ( ! isset( $post_img ) ) {
		$post_img = '';
	}
	$post_data['post_title'] = 'Post of ' . get_the_title( $business_id ) . ' on ' . date( 'Y M jS' ) . ' at ' . date( 'h:i:s' );
	$post_data['post_status'] = $post_status;
	$post_data['post_type'] = 'wyz_business_post';
	$bus_comm_stat = get_post_meta( $business_id, 'wyz_business_comments', true );
	$post_data['comment_status'] = ( 'off' != $bus_comm_stat ? 'open' : 'closed' );

	$new_post_id = wp_insert_post( $post_data, true );

	if ( '' !== $post_img ) {
		set_post_thumbnail( $new_post_id, $post_img );
	}


	foreach ( $post_meta_data as $key => $value ) {
		update_post_meta( $new_post_id, $key, $value );
	}

	$post = array();
	$post['name'] = get_the_title( $business_id );
	$post['user_likes'] = array();
	$post['business_ID'] = $business_id;
	$post['ID'] = $new_post_id;
	$post['post'] = $post_data['post_content'];
	$post['likes'] = 0;
	$post['time'] = get_the_date( get_option( 'date_format' ), $new_post_id );

	$business_posts = get_post_meta( $business_id, 'wyz_business_posts', true );
	if ( '' === $business_posts || ! $business_posts ) {
		$business_posts = array();
	}
	array_push( $business_posts, $new_post_id );
	update_post_meta( $business_id, 'wyz_business_posts', $business_posts );

	$points_available -= $post_cost;
	update_user_meta( $user_id, 'points_available', $points_available );

	wp_die( WyzBusinessPost::wyz_create_post( $post, true ) );
}
add_action( 'wp_ajax_upbuspost', 'wyz_upload_business_post_ajax' );
add_action( 'wp_ajax_nopriv_upbuspost', 'wyz_upload_business_post_ajax' );


/**
 * Handles updating business posts.
 */
function wyz_update_business_post_ajax() {
	if ( ! filter_input( INPUT_POST, 'nonce' ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'wyz-business-post-nonce' ) ) {
		wp_die( 'busted' );
	}

	$user_id = get_current_user_id();

	$business_id = intval( $_POST['id'] );
	if ( '' == $business_id || ! $business_id ) {
		wp_die( false );
	}

	$post_status = get_post_status( $business_id );
	if ( ! $post_status )
		wp_die( false );

	$post_id = intval( $_POST['post-id'] );
	if ( 1 > $post_id ) {
		wp_die( false );
	}

	$post_data = array(
		'ID' => $post_id,
	);

	if ( filter_input( INPUT_POST, 'post-txt' ) ) {
		$post_data['post_content'] = filter_input( INPUT_POST, 'post-txt' );
	}

	$vid = '';
	if ( isset( $post_data['post_content'] ) ) {
		$data = array( $post_data['post_content'], false );
		$data = wyz_split_glue_link( $data );
		$post_data['post_content'] = $data[0][0];
		$vid = $data[1];
	} 

	if ( filter_input( INPUT_POST, 'img' ) ) {
		$post_img = intval( filter_input( INPUT_POST, 'img' ) );
	}

	if ( ! isset( $post_data['post_content'] ) && ! isset( $post_img ) ) {
		wp_die( false );
	}

	wp_update_post( $post_data );

	if ( ! isset( $post_img ) )
		delete_post_thumbnail( $post_id );
	else
		set_post_thumbnail( $post_id, $post_img );

	update_post_meta( $post_id, 'vid', $vid );

	wp_die( true );
}
add_action( 'wp_ajax_updatebuspost', 'wyz_update_business_post_ajax' );
add_action( 'wp_ajax_nopriv_updatebuspost', 'wyz_update_business_post_ajax' );


/**
 * Separate $data by space and newline, detect youtube links then reassemble the string.
 *
 * @param array $data the string to be checkd for links.
 */
function wyz_split_glue_link( $data ) {
	$exp = preg_split( '/[\n]/', $data[0] );
	$l = count( $exp );
	for ( $u = 0; $u < $l; $u++ ) {
		$str = preg_split( '/[ ]/', $exp[ $u ] );
		$ll = count( $str );
		$vid = '';
		for ( $k = 0; $k < $ll; $k++ ) {
			if ( filter_var( $str[ $k ], FILTER_VALIDATE_URL ) ) {
				if ( false !== strpos( strtolower( $str[ $k ] ), 'www.youtube.com/' ) || false !== strpos( strtolower( $str[ $k ] ), '//youtu.be/' ) ) {
					if ( ! $data[1] ) {
						$data[1]  = true;
						$vid = '<div class="youtube-vid">' . wp_oembed_get( $str[ $k ] ) . '</div><br/>';

					} //else {
						$str[ $k ] = '';
					//}
				} else {
					$str[ $k ] = '<a href="' . $str[ $k ] . '" target="_blank">' . $str[ $k ] . '</a>';
				}
			} else {
				$str[ $k ] = preg_replace( '/<a /', '<a target="_blank" ', make_clickable( $str[ $k ] ) );
			}
		}
		$exp[ $u ] = implode( ' ', $str );
	}
	$data[0] = implode( '<br/>', $exp );
	return array( $data, $vid );
}

/**
 * Load posts by scrolling for single business.
 */
function wyz_load_bus_posts_ajax() {
	$bus_id = intval( filter_input( INPUT_POST, 'bus-id' ) );
	$nonce = filter_input( INPUT_POST, 'nonce' );
	$page = intval( $_POST['page'] );

	$logged_in_user = filter_input( INPUT_POST, 'logged-in-user' );
	$is_current_user_author = filter_input( INPUT_POST, 'is-current-user-author' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	if ( ! $page ) {
		$page = 1;
	}

	$all_business_posts = get_post_meta( $bus_id, 'wyz_business_posts', true );
	$args = array(
		'post_type' => 'wyz_business_post',
		'post__in' => $all_business_posts,
		'posts_per_page' => 10,
		'paged' => $page,
	);
	$query = new WP_Query( $args );

	$output = '';
	$first_id = - 1;
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$post = array();
			$post['ID'] = $post_id;
			$post['business_ID'] = $bus_id;
			$post['name'] = get_the_title( $post['business_ID'] );
			$post['post'] = get_the_content();

			$post['likes'] = intval( get_post_meta( $post_id, 'wyz_business_post_likes_count', true ) );
			$post['user_likes'] = get_post_meta( $post_id, 'wyz_business_post_likes', true );
			$post['time'] = get_the_date( get_option( 'date_format' ) );
			$first_id = $post_id;
			$output .= WyzBusinessPost::wyz_create_post( $post, $is_current_user_author );
		}
	}
	if ( -1 !== $first_id ) {
		$output = $first_id . 'wyz_space' . $output;
	} else {
		$output = '';
	}
	wp_die( $output );
}
add_action( 'wp_ajax_bus_inf_scrll', 'wyz_load_bus_posts_ajax' );
add_action( 'wp_ajax_nopriv_bus_inf_scrll', 'wyz_load_bus_posts_ajax' );



/**
 * Load ratings by scrolling for single business.
 */
function wyz_load_bus_ratings_ajax() {
	$bus_id = intval( filter_input( INPUT_POST, 'bus-id' ) );
	$nonce = filter_input( INPUT_POST, 'nonce' );
	$page = intval( $_POST['page'] );

	//$logged_in_user = filter_input( INPUT_POST, 'logged-in-user' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	if ( ! $page ) {
		$page = 1;
	}

	$all_business_rates = get_post_meta( $bus_id, 'wyz_business_ratings', true );
	$args = array(
		'post_type' => 'wyz_business_rating',
		'post__in' => $all_business_rates,
		'posts_per_page' => 10,
		'paged' => $page,
	);
	$query = new WP_Query( $args );

	$output = '';
	$first_id = - 1;
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$first_id = $bus_id;
			$output .= WyzBusinessRating::wyz_create_rating( get_the_ID() );
		}
		wp_reset_postdata();
	}
	if ( -1 !== $first_id ) {
		$output = $first_id . 'wyz_space' . $output;
	} else {
		$output = '';
	}
	wp_die( $output );
}
add_action( 'wp_ajax_bus_inf_rate_scrll', 'wyz_load_bus_ratings_ajax' );
add_action( 'wp_ajax_nopriv_bus_inf_rate_scrll', 'wyz_load_bus_ratings_ajax' );



/**
 * Load posts by scrolling for wall.
 */
function wyz_load_all_bus_posts_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	$logged_in_user = filter_input( INPUT_POST, 'logged-in-user' );
	$posts_pull = intval( filter_input( INPUT_POST, 'posts_pull' ) );
	$category = filter_input( INPUT_POST, 'category' );

	$page = intval( $_POST['page'] );

	if ( ! $posts_pull || is_nan( $posts_pull ) ) {
		$posts_pull = 10;
	}

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	if ( ! isset( $post_indx ) ) {
		$post_indx = 0;
	}

	if ( ! $page ) {
		$page = 1;
	}

	$args = array(
		'post_type' => 'wyz_business_post',
		'post_status' => 'publish',
		'posts_per_page' => $posts_pull,
		'paged' => $page,
	);

	$business_ids = array();
	if ( ! empty( $category ) ) {
		$category = explode( ',', $category );
		$query = array(
			'post_type' => 'wyz_business',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => 'wyz_business_category',
					'field'    => 'term_id',
					'terms'    => $category,
				)
			)
		);

		$query = new WP_Query($query);

		if ( $query->have_posts() ) {
			$business_ids = $query->posts;
		}
	}

	if ( ! empty( $business_ids ) )
		$args['meta_query'] = array(
			array(
				'key'     => 'business_id',
				'value'   => $business_ids,
				'compare' => 'IN',
			)
		);

	$query = new WP_Query( $args );

	$output = '';
	$first_id = -1;
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$_post = array();
			$_post['ID'] = $post_id;
			$_post['business_ID'] = get_post_meta( $post_id, 'business_id', true );
			$_post['name'] = get_the_title( $_post['business_ID'] );
			$_post['post'] = get_the_content();
			$_post['likes'] = intval( get_post_meta( $post_id, 'wyz_business_post_likes_count', true ) );
			$_post['user_likes'] = get_post_meta( $post_id, 'wyz_business_post_likes', true );
			$_post['time'] = get_the_date( get_option( 'date_format' ) );
			$first_id = $post_id;
			$output .= WyzBusinessPost::wyz_create_post( $_post, false, true );
		}
	}

	if ( -1 !== $first_id ) {
		$output = $first_id . 'wyz_space' . $output;
	} else {
		$output = '';
	}
	wp_die( $output );
}
add_action( 'wp_ajax_all_bus_inf_scrll', 'wyz_load_all_bus_posts_ajax' );
add_action( 'wp_ajax_nopriv_all_bus_inf_scrll', 'wyz_load_all_bus_posts_ajax' );


/**
 * Handles business post like actions.
 */
function wyz_bus_like_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$_post_id = intval( $_POST['post-id'] );
	$_user_id = get_current_user_id();


	if ( ! $_post_id || ! $_user_id || 0 === $_post_id || '' === $_user_id ) {
		wp_die( false );
	}

	$_likes = get_post_meta( $_post_id, 'wyz_business_post_likes', true );
	foreach ( $_likes as $like ) {
		if ( $like == $_user_id ) {
			wp_die( -1 );
		}
	}
	if ( empty( $_likes ) || '' == $_likes ) {
		$_likes = array();
	}
	array_push( $_likes, $_user_id );

	update_post_meta( $_post_id, 'wyz_business_post_likes', $_likes );

	$likes_count = intval( get_post_meta( $_post_id, 'wyz_business_post_likes_count', true ) );
	if ( ! $likes_count  ) {
		$likes_count = 0;
	}
	$likes_count++;
	update_post_meta( $_post_id, 'wyz_business_post_likes_count', $likes_count );

	wp_die( $likes_count );
}
add_action( 'wp_ajax_buslike', 'wyz_bus_like_ajax' );
add_action( 'wp_ajax_nopriv_buslike', 'wyz_bus_like_ajax' );


/**
 * Handles business post like actions.
 */
function wyz_bus_post_load_comments() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$_post_id = intval( $_POST['post-id'] );
	$_offset = intval( $_POST['offset'] );

	if( '' == $_post_id || '' == $_offset ) wp_die( false );

	$args = array(
		'status' => 'approve',
		'post_id' => $_post_id
	);
	$comments = get_comments( $args );
	$output = '';
	$count = 0;
	foreach ( $comments as $comment) {
		if(!$count++ )continue;
		$output .= WyzBusinessPost::get_the_comment( $comment );
	}

	wp_die( $output );
	//wp_die( array( $output, $count ) );
}
add_action( 'wp_ajax_bus_load_comments', 'wyz_bus_post_load_comments' );
add_action( 'wp_ajax_nopriv_bus_load_comments', 'wyz_bus_post_load_comments' );


/**
 * Handles saving the business info 
 */
function wyz_bus_save_draft() {

}

add_action( 'wp_ajax_save_draft_bus', 'wyz_bus_save_draft' );
add_action( 'wp_ajax_nopriv_save_draft_bus', 'wyz_bus_save_draft' );


/**
 * Handles business rate actions.
 */
function wyz_new_bus_rate_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$_bus_id = intval( $_POST['bus-id'] );
	$_user_id = get_current_user_id();
	$_rate = intval( $_POST['rate'] );
	$_rate_cat = intval( $_POST['rate_cat'] );
	$_rate_txt = $_POST['rate_txt'];

	if ( ! $_bus_id || '' === $_bus_id || ! $_user_id || '' === $_user_id || ! $_rate || '' === $_rate ||
		$_rate < 0 || $_rate > 5 || ( $_rate < 3 && '' == $_rate_txt ) ) {
		wp_die( false );
	}

	$taxonomies = array();
	$taxonomy = 'wyz_business_rating_category';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
	$length = count( $tax_terms );

	if ( !$length ){
		wp_die( false );
	}
	
	for ( $i = 0; $i < $length; $i++ ) {
		if ( ! isset( $tax_terms[ $i ] ) ) {
			continue;
		}
		$obj = $tax_terms[ $i ];
		$taxonomies[] = $obj->term_id;
	}

	if ( ! in_array( $_rate_cat, $taxonomies ) ) {
		wp_die( false );
	}

	$is_current_user_author = WyzHelpers::wyz_is_current_user_author( $_bus_id );
	$all_business_ratings = get_post_meta( $_bus_id, 'wyz_business_ratings', true );
	if ( ! $all_business_ratings || '' == $all_business_ratings ) {
		$all_business_ratings = array();
	}
	if ( ! empty( $all_business_ratings ) ) {
		$args = array(
			'post_type' => 'wyz_business_rating',
			'author' => $_user_id,
			'post_status' => 'publish',
			'post__in' => $all_business_ratings,
			'posts_per_page' => 1,
		);
		$query = new WP_Query( $args );
		$user_can_rate = ! $query->have_posts();
		wp_reset_postdata();
	} else {
		$user_can_rate = true;
	}

	if ( ! $user_can_rate || $is_current_user_author ) {
		wp_die( false );
	}

	$post_data = array(
		'post_title' => wp_filter_nohtml_kses( get_the_title( $_bus_id ) . '-' . $_rate . 'stars-' . date( 'd_m_y-H:m:s' )  ),
		'post_type' => 'wyz_business_rating',
		'post_content' => $_rate_txt,
		'post_status' => 'publish',
		'comment_status' => 'closed',
	);
	$new_submission_id = wp_insert_post( $post_data, true );

	// If we hit a snag, update the user.
	if ( is_wp_error( $new_submission_id ) ) {
		wp_die( false );
	}

	update_post_meta( $new_submission_id, 'wyz_business_rate', $_rate );
	wp_set_object_terms( $new_submission_id, $_rate_cat, 'wyz_business_rating_category' );

	$all_business_ratings[] = $new_submission_id;
	update_post_meta( $_bus_id, 'wyz_business_ratings', $all_business_ratings );


	$_rate_count = intval( get_post_meta( $_bus_id, 'wyz_business_rates_count', true ) );
	$_rate_sum = intval( get_post_meta( $_bus_id, 'wyz_business_rates_sum', true ) );

	if ( ! $_rate_count ) {
		$_rate_count = 0;
	}
	if ( ! $_rate_sum ) {
		$_rate_sum = 0;
	}

	$_rate_count++;
	$_rate_sum += intval( $_rate );

	update_post_meta( $_bus_id, 'wyz_business_rates_count', $_rate_count );
	update_post_meta( $_bus_id, 'wyz_business_rates_sum', $_rate_sum );

	wp_die( WyzBusinessRating::wyz_create_rating( $new_submission_id ) );
}
add_action( 'wp_ajax_bus_rate', 'wyz_new_bus_rate_ajax' );
add_action( 'wp_ajax_nopriv_bus_rate', 'wyz_new_bus_rate_ajax' );


/**
 * Handles business post comment actions.
 */
function wyz_new_bus_post_comm_ajax() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	$_id = intval( $_POST['id'] );

	if ( ! wp_verify_nonce( $nonce, "wyz-business-post-comment-nonce-$_id" ) ) {
		wp_die( 'busted' );
	}

	$_comment = $_POST['comment'];

	if ( '' == $_comment || ! is_user_logged_in() || ! comments_open( $_id ) ) {
		wp_die( false );
	}

	$_comment = esc_html( $_comment );
	$current_user = wp_get_current_user();

	$time = current_time('mysql');

	$data = array(
		'comment_post_ID' => $_id,
		'comment_author' => $current_user->user_login,
		'comment_author_email' => $current_user->user_email,
		'comment_author_url' => $current_user->user_url,
		'comment_content' => $_comment,
		'user_id' => $current_user->ID,
		'comment_date' => $time,
		'comment_approved' => 1,
	);
	$comment_id = wp_insert_comment( $data );
	$the_comment = get_comment( $comment_id );

	if ( null == $the_comment ) {
		wp_die( false );
	}

	wp_die( WyzBusinessPost::get_the_comment( $the_comment ) );
}
add_action( 'wp_ajax_bus_post_comm', 'wyz_new_bus_post_comm_ajax' );
add_action( 'wp_ajax_nopriv_bus_post_comm', 'wyz_new_bus_post_comm_ajax' );


/**
 * Handles business post deletion.
 */
function wyz_delete_business_post() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	global $current_user;
	wp_get_current_user();

	$_post_id = intval( $_POST['post-id'] );
	$_bus_id = intval( $_POST['bus-id'] );
	$_user_id = $current_user->ID;
	$post = get_post($_post_id);
	if ( $_user_id != $post->post_author && ! user_can( $_user_id, 'manage_options' ) ) {
		wp_die( false );
	}

	$bus_posts = get_post_meta( $_bus_id, 'wyz_business_posts', true );
	if ( is_array( $bus_posts ) && ! empty( $bus_posts ) ) {
		foreach ( $bus_posts as $key => $value ) {
			if ( $value == $_post_id ) {
				unset( $bus_posts[ $key ] );
				$bus_posts = array_values( $bus_posts );
				update_post_meta( $_bus_id, 'wyz_business_posts', $bus_posts );
				wp_trash_post( $_post_id  );
				wp_die( true );
			}
		}
	}
	wp_die( false );
}
add_action( 'wp_ajax_bus_post_delete', 'wyz_delete_business_post' );
add_action( 'wp_ajax_nopriv_bus_post_delete', 'wyz_delete_business_post' );


/**
 * Handles business post deletion.
 */
function wyz_edit_business_post() {

	if ( 'on' != get_option( 'wyz_allow_business_post_edit' ) )
		wp_die( false );

	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( false );
	}

	global $current_user;
	wp_get_current_user();

	$_post_id = intval( $_POST['post-id'] );
	$_bus_id = intval( $_POST['bus-id'] );
	$_user_id = $current_user->ID;
	$post = get_post($_post_id);
	if ( $_user_id != $post->post_author && ! user_can( $_user_id, 'manage_options' ) ) {
		wp_die( false );
	}

	wp_die( wp_json_encode( array(
		get_post_field('post_content', $_post_id),
		get_post_thumbnail_id( $_post_id ),
	) ) );

	/*$bus_posts = get_post_meta( $_bus_id, 'wyz_business_posts', true );
	if ( is_array( $bus_posts ) && ! empty( $bus_posts ) ) {
		foreach ( $bus_posts as $key => $value ) {
			if ( $value == $_post_id ) {
				unset( $bus_posts[ $key ] );
				$bus_posts = array_values( $bus_posts );
				update_post_meta( $_bus_id, 'wyz_business_posts', $bus_posts );
				wp_trash_post( $_post_id  );
				wp_die( true );
			}
		}
	}*/
}
add_action( 'wp_ajax_bus_post_edit_get', 'wyz_edit_business_post' );
add_action( 'wp_ajax_nopriv_bus_post_edit_get', 'wyz_edit_business_post' );


/**
 * Handles business post comments enable/disable.
 */
function wyz_business_post_comments_toggle() {
	$nonce = filter_input( INPUT_POST, 'nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	global $current_user;
	wp_get_current_user();

	$_post_id = intval( $_POST['post-id'] );
	$_user_id = $current_user->ID;
	$_comm_stat = $_POST['comm-stat'];
	$_post = get_post( $_post_id );

	if ( ! $_post || ( $_user_id != $_post->post_author && ! user_can( $_user_id, 'manage_options' ) ) ) {
		wp_die( false );
	}
	
	if ( 'open' == $_comm_stat || 'closed' == $_comm_stat ) { 
		$_post->comment_status = $_comm_stat;
	}
	wp_update_post( $_post );
	wp_die( true );
}
add_action( 'wp_ajax_bus_post_comm_toggle', 'wyz_business_post_comments_toggle' );
add_action( 'wp_ajax_nopriv_bus_post_comm_toggle', 'wyz_business_post_comments_toggle' );


/**
 * Calculates the distance between 2 locations.
 *
 * @param array $p1 first location.
 * @param array $p2 second location.
 */
function wyz_get_distance( $p1, $p2 ) {
	$R = 6378137; // Earth’s mean radius in meter.
	$dLat = wyz_rad( $p2['lat'] - $p1['lat'] );
	$dLong = wyz_rad( $p2['lon'] - $p1['lon'] );
	$a = sin( $dLat / 2 ) * sin( $dLat / 2 ) + cos( wyz_rad( $p1['lat'] ) ) * cos( wyz_rad( $p2['lat'] ) ) * sin( $dLong / 2 ) * sin( $dLong / 2 );
	$c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
	$d = $R * $c;
	$radius_unit = get_option( 'wyz_business_map_radius_unit' );
	if ( 'mile' == $radius_unit ) {
		 return ( $d / 1000.0 )*0.621371; // Returns the distance in Miles.
	} else {
		return $d / 1000.0; // Returns the distance in kilometer.
	}
	
}

function wyz_rad( $x ) {
	return $x * pi() / 180;
}

/**
 * Global map search handler.
 */
function wyz_get_businesses_js_data() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	//$coor = get_post_meta( $l_id, 'wyz_location_coordinates', true );

	$bus_names = filter_input( INPUT_POST, 'bus-name' );
	$cat_id = filter_input( INPUT_POST, 'cat-id' );
	$loc_id = filter_input( INPUT_POST, 'loc-id' );
	$rad = filter_input( INPUT_POST, 'rad' );
	$lat = filter_input( INPUT_POST, 'lat' );
	$lon = filter_input( INPUT_POST, 'lon' );
	$is_listing_page = filter_input( INPUT_POST, 'is-listing' );
	$is_grid_view = filter_input( INPUT_POST, 'is-grid' );
	$posts_per_page = filter_input( INPUT_POST, 'posts-per-page' );
	$page = filter_input( INPUT_POST, 'page' );

	if ( $posts_per_page < 0 || is_nan( $posts_per_page ) )
		$posts_per_page = 10;


	$template_type = '';
	if ( function_exists( 'wyz_get_theme_template' ) )
			$template_type = wyz_get_theme_template();

	if ( $template_type == 1 )
		$template_type = '';

	//$loc_radius_search = false;


	if ( ! $rad || '' == $rad || ! is_numeric( $rad ) ) {
		$rad = 0;
		$lat = $lon = 0;
	}

	$results = WyzHelpers::wyz_handle_business_search( $bus_names, $cat_id, $loc_id, $rad, $lat, $lon, $page );
	$args =  $results['query'] ;
	$lat = $results['lat'];
	$lon = $results['lon'];
	
	
 	$featured_posts_per_page = get_option( 'wyz_featured_posts_perpage', 2 );
 	
 	
 	if ($is_listing_page && empty($bus_names)) {
 		
 		

		$sticky_posts = get_option( 'sticky_posts' );

		$cat_feat = array(
			'post_type' => 'wyz_business',
			'post__in' => $sticky_posts,
			'fields' => 'ids',
		);

 
		$featured_businesses_args = array(
			'post_type' => 'wyz_business',
			//'posts_per_page' => $featured_posts_per_page,
			'post__in' => $sticky_posts,
			'fields' => 'ids',
			'offset' => $page,
		);

		if ( isset( $args['tax_query'] ) ) {
			$featured_businesses_args['tax_query'] = $args['tax_query'];
		}


		$featured_businesses_args = apply_filters( 'wyz_query_featured_businesses_args_search', $featured_businesses_args, $args );


		$query1 = new WP_Query( $featured_businesses_args );

		$sticky_posts = $query1->posts;

		if ( count( $sticky_posts ) > $featured_posts_per_page ) {

			Wyzhelpers::fisherYatesShuffle( $sticky_posts, rand(10,100) );
			$sticky_posts = array_slice( $sticky_posts, 0, $featured_posts_per_page );
		}

		$args['fields'] = 'ids';
		//$args['post__not_in'] = get_option( 'sticky_posts' );
		$args['post_type'] = 'wyz_business';

		$query2 = new WP_Query( $args );

		$all_the_ids = array_merge( $sticky_posts, $query2->posts  );

		if ( empty( $all_the_ids ) ) $all_the_ids = array( 0 );

		$final_query_args = array(
			'post_type' => 'wyz_business',
			'post__in' => $all_the_ids,
			'orderby' => 'post__in',
			'offset' => $page,
			'posts_per_page' => -1,
		);


		 $query =  new WP_Query( $final_query_args );
		
 	}else {
 	
 		$query = new WP_Query($args);
 	
 	}
 	
 	
 	
 	
	// $query = new WP_Query($args);
	
 	 	

	// This query will get all businesses that match the search in their title, slogan, category and excerpt.

	//$query = new WP_Query( $args );

	$posts_for_nxt_loop = array();

	$user_favorites = WyzHelpers::get_user_favorites();

	$favorites = array();

	$locations = array();
	$marker_icons = array();
	$business_names = array();
	$business_after_names = array();
	$business_logoes = array();
	$business_permalinks = array();
	$business_cat_ids = array();
	$business_cat_colors = array();
	$business_list = '';
	$current_b_ids = array();

	$i = 0;
	$posts_count = 0;
	

	while ( $query->have_posts() ) {

		$query->the_post();
		$b_id = get_the_ID();
		$temp_loc = get_post_meta( $b_id, 'wyz_business_location', true );

		$def_marker_coor = array('latitude' => get_option( 'wyz_businesses_default_lat', 0 ), 'longitude' => get_option( 'wyz_businesses_default_lon', 0 ) );

		if ( empty( $temp_loc ) ) {
			$temp_loc = array(
				'latitude' => $def_marker_coor['latitude'],
				'longitude' => $def_marker_coor['longitude'],
			);
		}

		$posts_count++;

		// If the business has map coordinates and is within range (in case search radius was provided),
		// add its id to $posts_for_nxt_loop
		if ( 0 != $lat && 0 != $lon && 0 != $rad ) {
			$pos = array( 'lat' => $temp_loc['latitude'], 'lon' => $temp_loc['longitude'] );
			$my_pos = array( 'lat' => $lat, 'lon' => $lon );
			if ( $rad < wyz_get_distance( $pos, $my_pos ) ) {
				continue;
			}
		}

		array_push( $favorites, in_array( $b_id, $user_favorites ) );
		array_push( $locations, $temp_loc );
		array_push( $business_names, get_the_title() );
		array_push( $business_after_names, apply_filters( 'wyzi_after_business_name_info_bubble', '', $b_id ) );
		array_push( $business_permalinks, esc_url( get_the_permalink() ) );
		array_push( $posts_for_nxt_loop, $b_id );


		if ( $is_listing_page && $i++ < ( $posts_per_page + $featured_posts_per_page ) ) {

			array_push( $current_b_ids, $b_id );
			if ( $is_grid_view ) {
				$business_list .= WyzBusinessPost::wyz_create_business_grid_look();

			} else {
				$business_list .= WyzBusinessPost::wyz_create_business();

			}
		}

		if ( has_post_thumbnail() ) {
			array_push( $business_logoes, get_the_post_thumbnail( $b_id, 'medium', array( 'class' => 'business-logo-marker' ) ) );
		} else {
			array_push( $business_logoes, '' );
		}

		$temp_term = WyzHelpers::wyz_get_representative_business_category_id( $b_id );

		if ( '' != $temp_term ) {

			$col = get_term_meta( $temp_term, 'wyz_business_cat_bg_color', true );
			$holder = wp_get_attachment_url( get_term_meta( $temp_term, "map_icon$template_type", true ) );
				
		} else {
			$col = '';
		}
		if ( ! isset( $holder ) || false == $holder ) {
			$marker = '';
		} else {
			$marker = $holder;
		}

		array_push( $business_cat_ids, intval( $temp_term ) );
		array_push( $business_cat_colors, $col );
				

		if ( false == $marker ) {
			array_push( $marker_icons, '' );
			array_push( $business_cat_ids, -1 );
		} else {
			array_push( $marker_icons, $marker );
		}
	}
	wp_reset_postdata();


	if ( empty( $posts_for_nxt_loop ) ) {
		$posts_for_nxt_loop[] = -1;
	}
	
	if ( $is_listing_page ) {
		$remaining_pages = ceil( ( sizeof( $posts_for_nxt_loop ) / ( float ) $posts_per_page ) -1 );
	} else {
		$remaining_pages = 0;
	}

	wp_reset_postdata();

	if ( ! isset( $locations ) || ! isset( $marker_icons ) ) {
		$locations = array();
		$marker_icons = array();
	}
// Lets pass Essential Grid Shortcode in case needed
	$ess_grid_shortcode ='';

	if ( function_exists( 'wyz_get_theme_template' ) ) {
		$template_type = wyz_get_theme_template();
		
		if ( $template_type == 2 ) {
			$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
			$ess_grid_shortcode = do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$current_b_ids).']' );
		}
	}



	$global_map_java_data = array(
		'defCoor' => array(),
		'radiusUnit' => '',
		'GPSLocations' => $locations,
		'markersWithIcons' => $marker_icons,
		'businessNames' => $business_names,
		'afterBusinessNames' => $business_after_names,
		'businessLogoes' => $business_logoes,
		'businessPermalinks' => $business_permalinks,
		'businessCategories' => $business_cat_ids,
		'businessCategoriesColors' => $business_cat_colors,
		'isListingPage' => $is_listing_page,
		'postsPerPage' =>$posts_per_page,
		'businessIds' => $posts_for_nxt_loop,
		'businessList' => $business_list,
		'hasAfter' => $remaining_pages > 0,
		'favorites' => $favorites,
		'hasBefore' => false,
		'postsCount' => $posts_count,
		'ess_grid_shortcode' => $ess_grid_shortcode,
	);

	wp_die( wp_json_encode( $global_map_java_data ) );
}
add_action( 'wp_ajax_global_map_search', 'wyz_get_businesses_js_data' );
add_action( 'wp_ajax_nopriv_global_map_search', 'wyz_get_businesses_js_data' );


/*
 * Paginate the business list below global map.
 */
function wyz_paginate_business_list() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$offset = filter_input( INPUT_POST, 'offset' );
	$posts_per_page = filter_input( INPUT_POST, 'posts-per-page' );
	$business_ids = json_decode(stripslashes($_POST['business_ids']));
	$is_grid_view = filter_input( INPUT_POST, 'is-grid' );

	if ( empty( $business_ids ) || '' == $offset || 0 > $offset ) {
		wp_die( '' );
	}

	$business_list = '';

	$featured_posts_per_page = get_option( 'wyz_featured_posts_perpage', 2 );
	
	$args = array(
		'post_type' => 'wyz_business',
		'posts_per_page' => $posts_per_page + $featured_posts_per_page ,
		'post__in' => $business_ids,
		'paged' => $offset,
		'orderby' => 'post__in',
	);


	/* $args = array(
		'post_type' => 'wyz_business',
		'posts_per_page' => $posts_per_page ,
		//'post__in' => $business_ids,
		'paged' => $offset,
		'post_status' => 'publish',
	);
	

	
	$results = WyzHelpers::wyz_handle_business_search( $bus_names, $cat_id, $loc_id, $rad, $lat, $lon, 1 );
	$args =  $results['query'] ;
	
	
	
		$featured_posts_per_page = get_option( 'wyz_featured_posts_perpage', 2 );

		$sticky_posts = get_option( 'sticky_posts' );

		$cat_feat = array(
			'post_type' => 'wyz_business',
			'post__in' => $sticky_posts,
			'fields' => 'ids',
		);

 
		$featured_businesses_args = array(
			'post_type' => 'wyz_business',
			//'posts_per_page' => $featured_posts_per_page,
			'post__in' => $sticky_posts,
			'fields' => 'ids',
		);

		if ( isset( $args['tax_query'] ) ) {
			$featured_businesses_args['tax_query'] = $args['tax_query'];
		}

		$args2 = array(
			'post_type' => 'wyz_business',
			'posts_per_page' => -1,
			'post__in' => $business_ids,
			'post_status' => 'publish',
			//'paged' => $offset,
		);
		if ( isset( $args2['paged'] ) && 1 < $args2['paged'] ) {
			$featured_businesses_args['paged'] = $args2['paged'];
		}
		
		
		
	
		$featured_businesses_args = apply_filters( 'wyz_query_featured_businesses_args_search', $featured_businesses_args, $args2 );


		$query1 = new WP_Query( $featured_businesses_args );

		$sticky_posts = $query1->posts;

		$total_number_of_sticky_posts = count($sticky_posts);
		
		$args['fields'] = 'ids';
		$args['post__not_in'] = get_option( 'sticky_posts' );;

		$args['post_type'] = 'wyz_business';

		$query2 = new WP_Query( $args );
		
		if ( count( $sticky_posts ) > $featured_posts_per_page ) {

			Wyzhelpers::fisherYatesShuffle( $sticky_posts, rand(10,100) );
			$sticky_posts = array_slice( $sticky_posts, 0, $featured_posts_per_page );
		}

		
		

		$all_the_ids = array_merge( $sticky_posts, $query2->posts );

		if ( empty( $all_the_ids ) ) $all_the_ids = array( 0 );

		$final_query_args = array(
			'post_type' => 'wyz_business',
			'post__in' => $all_the_ids,
			'orderby' => 'post__in',
			
			//'posts_per_page' => $posts_per_page,
			//'post__in' => $business_ids,
			//'offset' => $offset,
			//'paged' => $offset ,
		);


		 $query =  new WP_Query( $final_query_args ); */


	$query = new WP_Query( $args );

	$current_b_ids = array();

	while ( $query->have_posts() ) {

		$query->the_post();
		$b_id = get_the_ID();
		array_push( $current_b_ids, $b_id );
		if ( $is_grid_view ) {
			$business_list .= WyzBusinessPost::wyz_create_business_grid_look();
		} else {
			$business_list .= WyzBusinessPost::wyz_create_business();
		}
	}
	$remaining_pages = ceil( ( (sizeof( $business_ids ) ) / ( float ) ($posts_per_page + + $featured_posts_per_page) )  ) - $offset;
	wp_reset_postdata();

// Let prepare Essential Grid Shortcode
	$ess_grid_shortcode ='';

	if ( function_exists( 'wyz_get_theme_template' ) ) {
		$template_type = wyz_get_theme_template();
		
		if ( $template_type == 2 ) {
			$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
			$ess_grid_shortcode = do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$current_b_ids).']' );
		}
	}

	$data = array(
		'businessList' => $business_list,
		'hasAfter' => ( $remaining_pages > 0 ),
		'hasBefore' => ( 1 < $offset ),
		'ess_grid_shortcode' => $ess_grid_shortcode,
	);

	wp_die( wp_json_encode( $data ) );

}
add_action( 'wp_ajax_business_listing_paginate', 'wyz_paginate_business_list' );
add_action( 'wp_ajax_nopriv_business_listing_paginate', 'wyz_paginate_business_list' );


/*
 * Generate map sidebar business gallery
 */
function wyz_get_map_sidebar_data() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	$id = filter_input( INPUT_POST, 'bus_id' );
	$author_id = WyzHelpers::wyz_the_business_author_id( $id );

	$can_booking = true;
	if ( 'off' == get_option( 'wyz_users_can_booking' ) || ! WyzHelpers::wyz_sub_can_bus_owner_do($author_id,'wyzi_sub_business_can_create_bookings') || ! WyzHelpers::get_user_calendar( $author_id, $id ) )
		$can_booking = false;
	$logo = '';

	if(has_post_thumbnail($id))
		$logo = get_the_post_thumbnail_url( $id, 'medium' );

	if ( ! WyzHelpers::wyz_sub_can_bus_owner_do($author_id,'wyzi_sub_business_show_photo_tab') ) {
		$data = array(
			'gallery' => array( 'length'=>0 ),
			'ratings' =>  WyzBusinessRating::get_business_rates_stars( $id, true ),
			'banner_image' => WyzHelpers::get_image( $id ),
			'canBooking' => $can_booking,
			'slogan' => get_post_meta( $id, 'wyz_business_slogan', true ),
			'logo' => $logo,
			'verified' => WyzHelpers::verified_icon( $id ),
		);
		wp_die( wp_json_encode( $data ) );
	}
	
	$count = 0;

	$gallery_data = array();
	$attachments = get_post_meta( $id, 'business_gallery_image', true );
	$c = count( $attachments );
	if ( $attachments && ! empty( $attachments ) ) {
		$current_image_attached_thumb = array();
		$current_image_attached_full = array();
		if ( ! is_array( $attachments ) ) {
			$count = 1;
			$temp_thumb = wp_get_attachment_image_src( $attachments, 'thumbnail' );
			$temp_full = wp_get_attachment_image_src( $attachments, 'full' );
			array_push( $current_image_attached_thumb,  $temp_thumb[0] );
			array_push( $current_image_attached_full,  $temp_full[0] );
		} else {
			for( $i=1; $i<=4; $i++ ) {
				$temp_thumb = wp_get_attachment_image_src( $attachments[ $i ], 'thumbnail' );
				$temp_full = wp_get_attachment_image_src( $attachments[ $i ], 'full' );
				if ( '' != $temp_thumb && ''!= $temp_full ) {
					array_push( $current_image_attached_thumb,  $temp_thumb[0] );
					array_push( $current_image_attached_full,  $temp_full[0] );
					$count++;
				}
			}
		}
		$gallery_data = array(
			'length' => $count,
			'full'  => $current_image_attached_full,
			'thumb' => $current_image_attached_thumb,
		);
		$data = array(
			'gallery' => $gallery_data,
			'ratings' =>  WyzBusinessRating::get_business_rates_stars( $id, true ),
			'banner_image' => WyzHelpers::get_image( $id ),
			'slogan' => get_post_meta( $id, 'wyz_business_slogan', true ),
			'canBooking' => $can_booking,
			'share' => WyzPostShare::the_share_buttons( $id, 1, false ),
			'logo' => $logo,
			'verified' => WyzHelpers::verified_icon( $id ),
		);
		wp_die(wp_json_encode($data));
	}
	$data = array(
		'gallery' => array( 'length'=>0 ),
		'ratings' =>  WyzBusinessRating::get_business_rates_stars( $id, true ),
		'banner_image' => WyzHelpers::get_image( $id ),
		'slogan' => get_post_meta( $id, 'wyz_business_slogan', true ),
		'canBooking' => $can_booking,
		'share' => WyzPostShare::the_share_buttons( $id, 1, false ),
		'logo' => $logo,
		'verified' => WyzHelpers::verified_icon( $id ),
	);
	wp_die(wp_json_encode($data));
}
add_action( 'wp_ajax_business_map_sidebar_data', 'wyz_get_map_sidebar_data' );
add_action( 'wp_ajax_nopriv_business_map_sidebar_data', 'wyz_get_map_sidebar_data' );



/*
 * favorite business
 */
function wyz_favorite_unfavorite_business() {
	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
		wp_die( 'busted' );
	}

	if ( ! is_user_logged_in() ) {
		wp_die( false );
	}

	$bus_id = filter_input( INPUT_POST, 'business_id' );
	$fav_type = filter_input( INPUT_POST, 'fav_type' );
	$user_id = get_current_user_id();
	$favorites = WyzHelpers::get_user_favorites( $user_id );

	if ( 'fav' != $fav_type && 'unfav' != $fav_type ) wp_die( false );

	switch( $fav_type ) {
		case 'fav':
			if ( ! in_array( $bus_id, $favorites ) ) {
				$favorites[] = $bus_id;
				update_user_meta( $user_id, 'wyz_user_favorites', $favorites );
			}
		break;
		case 'unfav':
			update_user_meta( $user_id, 'wyz_user_favorites', array_diff( $favorites, [ $bus_id ] ) );
		break;
	}
	wp_die( true );
	
}
add_action( 'wp_ajax_business_favorite', 'wyz_favorite_unfavorite_business' );
add_action( 'wp_ajax_nopriv_business_favorite', 'wyz_favorite_unfavorite_business' );


/*
 * Save built claim form into wp options
 */
function wyz_ajax_claim_save_form() {

	$form_data = json_decode( stripslashes_deep( $_REQUEST['form_data'] ),true );
	if ( ! empty( $form_data ) && is_array( $form_data ) ) {
		foreach ( $form_data as $key => $value ) {
			$form_data[ $key ]['hidden'] = true;
		}
	}

	update_option( 'wyz_claim_registration_form_data', $form_data );
	wp_die();
}
add_action( 'wp_ajax_wyzi_claim_save_form', 'wyz_ajax_claim_save_form' );

/*
 * Save built business custom form fields into wp options
 */
function wyz_ajax_custom_business_fields_save_form() {

	$form_data = json_decode( stripslashes_deep( $_REQUEST['form_data'] ),true );
	if ( ! empty( $form_data ) && is_array( $form_data ) ) {
		foreach ( $form_data as $key => $value ) {
			$form_data[ $key ]['hidden'] = true;
		}
	}

	update_option( 'wyz_business_custom_form_data', $form_data );
	wp_die();
}
add_action( 'wp_ajax_wyzi_business_custom_fields_save_form', 'wyz_ajax_custom_business_fields_save_form' );


/*
 * Save business tab layout into wp options
 */
function wyz_ajax_business_tabs_save_form() {

	$form_data = json_decode( stripslashes_deep( $_REQUEST['form_data'] ),true );
	if ( ! empty( $form_data ) && is_array( $form_data ) ) {
		foreach ( $form_data as $key => $value ) {
			$form_data[ $key ]['hidden'] = true;
		}
	}
	update_option( 'wyz_business_tabs_order_data', $form_data );
	wp_die();
}
add_action( 'wp_ajax_wyzi_business_tabs_save_form', 'wyz_ajax_business_tabs_save_form' );

/*
 * Save business form builder layout into wp options
 */
function wyz_ajax_business_form_builder_save_form() {

	$form_data = json_decode( stripslashes_deep( $_REQUEST['form_data'] ),true );
	if ( ! empty( $form_data ) && is_array( $form_data ) ) {
		foreach ( $form_data as $key => $value ) {
			$form_data[ $key ]['hidden'] = true;
		}
	}
	update_option( 'wyz_business_form_builder_data', $form_data );
	wp_die();
}
add_action( 'wp_ajax_wyzi_business_form_builder_save_form', 'wyz_ajax_business_form_builder_save_form' );

?>
