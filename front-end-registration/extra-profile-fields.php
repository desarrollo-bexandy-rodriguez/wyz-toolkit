<?php
add_action( 'personal_options_update', 'wyz_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'wyz_save_extra_profile_fields' );

function wyz_save_extra_profile_fields( $user_id ) {
	if ( ! is_admin() || ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$new_sub = '';
	if ( isset( $_POST['subscribtion'] ) ) {
		$new_sub = $_POST['subscribtion'];
	}
	if ( isset( $_POST['role'] ) ) {
		$new_sub = $_POST['role'];
	}
	update_user_meta( $user_id, 'subscribtion', $new_sub );
}

function wyz_map_meta_cap( $caps, $cap, $user_id, $args ) {
	/* If editing, deleting, or reading an offer, get the post and post type object. */
	if ( 'edit_offer' == $cap || 'delete_offer' == $cap || 'read_offer' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

		/* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing an offer, assign the required capability. */
	if ( 'edit_offer' == $cap ) {
		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->edit_posts;
		} else {
			$caps[] = $post_type->cap->edit_others_posts;
		}
	} elseif ( 'delete_offer' == $cap ) { /* If deleting an offer, assign the required capability. */
		if ( $post->post_author == $user_id  ) {
			$caps[] = $post_type->cap->delete_posts;
		} else {
			$caps[] = $post_type->cap->delete_others_posts;
		}
	} elseif ( 'read_offer' == $cap ) { /* If reading a private offer, assign the required capability. */
		if ( 'private' != $post->post_status ) {
			$caps[] = 'read';
		} elseif ( $user_id == $post->post_author ) {
			$caps[] = 'read';
		} else {
			$caps[] = $post_type->cap->read_private_posts;
		}
	}
	/* Return the capabilities required by the user. */
	return $caps;
}
add_filter( 'map_meta_cap', 'wyz_map_meta_cap', 10, 4 );

