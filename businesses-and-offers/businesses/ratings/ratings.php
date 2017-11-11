<?php
/**
 * Ratings posts creator
 *
 * @package wyz
 */

if ( ! post_type_exists( 'wyz_business_rating' ) ) {

	// Create business rating cpt.
	add_action( 'init', 'wyz_create_business_rating', 5 );

	//Create rating taxonomy
	add_action( 'init', 'wyz_create_ratings_taxonomy', 5 );
}

/**
 * Creates the wyz_business_rating cpt
 */
function wyz_create_business_rating() {
	$rating = esc_html__( 'Rating', 'wyzi-business-finder' );
	$ratings = esc_html__( 'Ratings', 'wyzi-business-finder' );
	register_post_type( 'wyz_business_rating',array(
		'public' => true,
		'map_meta_cap' => true,
		'capabilities' => array(
			'publish_posts' => 'publish_businesses',
			'edit_posts' => 'edit_businesses',
			'edit_others_posts' => 'edit_others_businesses',
			'delete_posts' => 'delete_businesses',
			'delete_published_posts' => 'delete_published_businesses',
			'edit_published_posts' => 'edit_published_businesses',
			'delete_others_posts' => 'delete_others_businesses',
			'read_private_posts' => 'read_private_businesses',
			'read_post' => 'read_business',
		),
		'labels' => array(
			'name' => $ratings,
			'singular_name' => $rating,
			'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
			'add_new_item' => esc_html__( 'Add New', 'wyzi-business-finder' ) . ' ' . $rating,
			'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
			'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . $rating,
			'new_item' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . $rating,
			'view' => esc_html__( 'View', 'wyzi-business-finder' ),
			'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . $rating,
			'search_items' => esc_html__( 'Search', 'wyzi-business-finder' ) . ' ' . $ratings,
			'not_found' => sprintf( esc_html__( 'No %s found', 'wyzi-business-finder' ), $ratings ),
			'not_found_in_trash' => sprintf( esc_html__( 'No %s found in trash', 'wyzi-business-finder' ), $ratings ),
			'parent' => esc_html__( 'Parent', 'wyzi-business-finder' ) . " $rating",
		),
		'public' => true,
		'menu_position' => 57.1,
		'supports' => array( 'title', 'thumbnail', 'editor', 'comments' ),
		'taxonomies' => array( '' ),
		'menu_icon' => plugins_url( 'images/ratings-icon.png', __FILE__ ),
		'exclude_from_search' => true,
		'rewrite' => array( 'slug' => 'rating' ),
	) );
}


/**
 * Register ratings taxonomies.
 */
function wyz_create_ratings_taxonomy() {
	$rating = esc_html__( 'Rating', 'wyzi-business-finder' );

	$labels = array(
		'name' => $rating . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'singular_name' => $rating . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'search_items' => sprintf( esc_html__( 'Search %s Categories', 'wyzi-business-finder' ), $rating ),
		'all_items' => sprintf( esc_html__( 'All %s Categories', 'wyzi-business-finder' ), $rating ),
		'edit_item' => sprintf( esc_html__( 'Edit %s Category', 'wyzi-business-finder' ), $rating ),
		'update_item' => sprintf( esc_html__( 'Update %s Category', 'wyzi-business-finder' ), $rating ),
		'add_new_item' => sprintf( esc_html__( 'Add New %s Category', 'wyzi-business-finder' ), $rating ),
		'new_item_name' => sprintf( esc_html__( 'New %s Category Name', 'wyzi-business-finder' ), $rating ),
		'menu_name' => esc_html__( 'Categories', 'wyzi-business-finder' ),
		'view_item' => sprintf( esc_html__( 'View %s Category', 'wyzi-business-finder' ), $rating ),
		'popular_items' => sprintf( esc_html__( 'Popular %s Categories', 'wyzi-business-finder' ), $rating ),
		'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s Categories with commas', 'wyzi-business-finder' ), $rating ),
		'add_or_remove_items' => sprintf( esc_html__( 'Add or Remove %s Categories', 'wyzi-business-finder' ), $rating ),
		'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s Categories', 'wyzi-business-finder' ), $rating ),
		'not_found' => sprintf( esc_html__( 'No %s Categories found', 'wyzi-business-finder' ), $rating ),
	);

	register_taxonomy(
		'wyz_business_rating_category',
		'wyz_business_rating',
		array(
			'label' => esc_html__( 'Rating Category', 'wyzi-business-finder' ),
			'hierarchical' => true,
			'capabilities' => array (
				'manage_terms' => 'manage_options',
				'edit_terms' => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'edit_posts',
			),
			'labels' => $labels,
			'show_ui' => true,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => 'rating_category' ),
		)
	);
}

/**
 * Class WyzBusinessRating.
 */
class WyzBusinessRating{

	public static function wyz_create_rating( $post_id, $rate_type = 1 ) {

		return $rate_type == 2 ? self::the_rating_sidebar( $post_id ) : self::the_rating_wall( $post_id );
	}

	private static function the_rating_wall( $post_id ) {

		$post_object = get_post( $post_id );

		ob_start();
		?>

		<div class="sin-busi-rate">
			<!-- Post Head -->
			<div class="head fix">
				<div class="rating-data">
					<div class="the-stars float-left"><?php self::the_stars( $post_id );?></div>
					<span class="the-rate-cat float-left"><?php self::the_rate_category( $post_id );?></span>
				</div>

				<div class="rating-auth-date float-right"><span class="rating-auth" ><?php self::the_rate_author( $post_id );?></span><span class="rating-date"><?php WyzHelpers::the_publish_date( $post_object->post_date );?></div>
			</div>

			<?php if ( '' != $post_object->post_content ) {?>
				
			<!-- Post Content -->
			<div class="content">
				<p><?php echo $post_object->post_content;?></p>
			</div>

			<?php }?>

		</div>
		<?php
		return ob_get_clean();
	}


	private static function the_rating_sidebar( $post_id ) {

		$post_object = get_post( $post_id );

		ob_start();
		?>

		<div class="single-rating fix">
			<div class="head fix">
				<h5><?php self::the_rate_author( $post_id, 2 );?></h5>
				<?php self::the_stars( $post_id, 2 );?>
				<?php self::the_rate_category( $post_id, 2 );?>
			</div>
			<?php if ( '' != $post_object->post_content ) {?>
			<p><?php echo $post_object->post_content;?></p>
			<?php }?>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function the_stars( $post_id, $type = 1 ) {

		$rate = get_post_meta( $post_id, 'wyz_business_rate', true );

		if ( $type == 1 ) {
		$rate = round( $rate );?>

		<div class="ratings">
			<span>
			<?php for ( $i = 5;  $i > 0 ;  $i-- ) {?>
				<input class="star" id="<?php echo $post_id;?>-star-<?php echo $i;?>" type="radio" <?php echo ( $rate == $i ? 'checked' : '');?> disabled="disabled" /><label class="star star-hov" for="<?php echo $post_id;?>-star-<?php echo $i;?>"></label>
			<?php }?>
			</span>
		</div>
		<?php
		} else { 
			$rate *= 2;
			$rate = floor($rate );
			$rate /= 2.0;
			?>
			<div class="rating wyz-prim-color-txt">
			<?php for ( $i = 1;  $i <= 5 ;  $i++ ) {?>
				<i class="fa fa-star<?php echo $rate >= $i ? '' : ( $rate < $i && $rate > $i-1 ? '-half-o' : '-o' );?>"></i>
			<?php }?>
			</div>
		<?php }
	}

	private static function the_rate_category( $post_id, $type = 1 ) {

		$term_list = wp_get_post_terms( $post_id, 'wyz_business_rating_category', array( 'fields' => 'names' ) );
		if ( is_array( $term_list ) ){
			$term_list = $term_list[0];
		}
		echo $type == 1 ? sprintf( __( 'for <b>%s</b>', 'wyzi-business-finder' ), $term_list ) : '<p>' . $term_list . '</p>';
	}

	private static function the_rate_author( $post_id, $type = 1 ) {

		$author_id = get_post_field( 'post_author', $post_id );
		$author_name = get_the_author_meta( 'nicename', $author_id );
		echo $type == 1 ? sprintf( __( 'by <span class="auth wyz-primary-color-text wyz-prim-color-txt">%s</span>', 'wyzi-business-finder' ), $author_name ) : $author_name ;
	}


	public static function get_business_rates_stats( $post_id ) {
		$rate_nb = get_post_meta( $post_id, 'wyz_business_rates_count', true );
		$rate_sum = get_post_meta( $post_id, 'wyz_business_rates_sum', true );
		if ( 0 == $rate_nb ) {
			$rating = 0;
		} else {
			$rating = number_format( ( $rate_sum ) / $rate_nb, 1 );
		}

		return array( 'rate_nb' => $rate_nb, 'rating' => $rating );
	}

	public static function get_business_rates_stars( $post_id, $display_count = false, $rate_stats = array() ) {
		$data = '';
		if ( empty( $rate_stats ) )
			$rate_stats = self::get_business_rates_stats( $post_id );

		$rate_stats['rating'] *= 2;
		$rate_stats['rating'] = floor( $rate_stats['rating'] );
		$rate_stats['rating'] /= 2.0;
		
		$data .= '<div class="rating wyz-prim-color-txt"><span>';

		for ( $i = 1;  $i <= 5 ;  $i++ ) {
			$data .= '<i class="fa fa-star' . ( $rate_stats['rating'] >= $i ? '' : ( $rate_stats['rating'] < $i && $rate_stats['rating'] > $i-1 ? '-half-o' : '-o' ) ) . '"></i>';
		}
		$data .= '</span></div>';

		if ( $display_count && '' != $rate_stats['rate_nb'] ) {
			 $data .= '<span class="rates-count">(' . $rate_stats['rate_nb'] . ')</span>';
		}
		return $data;
	}

	public static function get_business_rates_cats_perc( $post_id, $in_rates, $rate_nb = -1 ) {
		if ( $rate_nb == -1 )
			$rate_nb = intval( get_post_meta( $post_id, 'wyz_business_rates_count', true ) );
		$terms = get_terms( array(
			'taxonomy' => 'wyz_business_rating_category',
			'hide_empty' => false,
		) );
		$output = '';
		foreach ( $terms as $term ) {
			
			$args = array(
				'post_type' => 'wyz_business_rating',
				'posts_per_page' => -1,
				'post__in' => $in_rates,
				'tax_query' => array(
					array(
						'taxonomy' => 'wyz_business_rating_category',
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					),
				),
			);
			$query = new WP_Query( $args );

			$cat_rates_sum = 0;
			$cat_rates_count = 0;

			while ( $query->have_posts() ) {
				$query->the_post();
				$rate = get_post_meta( get_the_ID(), 'wyz_business_rate', true );
				if ( '' == $rate )$rate = 0;
				$cat_rates_sum += $rate;
				$cat_rates_count++;
			}

			$output .= '<div class="head fix">';
			$output .= '<h5>' . $term->name .'</h5>';
				$output .= self::get_business_rates_stars( -1, true, array( 'rating' => $cat_rates_sum,'rate_nb' => $cat_rates_count ) );
			$output .= '</div>';
		}
		return $output;
	}
}
?>
