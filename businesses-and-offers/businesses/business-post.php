<?php
/**
 * Business and Business posts creator
 *
 * @package wyz
 */

if ( ! post_type_exists( 'wyz_business_post' ) ) {

	// Create business post cpt.
	add_action( 'init', 'wyz_create_business_post', 5 );
}

/**
 * Creates the wyz_business_post cpt
 */
function wyz_create_business_post() {
	$bus_post_name = esc_html__( wyz_syntax_permalink( get_option( 'wyz_business_post_old_single_permalink' ) ) );
	register_post_type( 'wyz_business_post',array(
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
			'name' => esc_html( $bus_post_name ),
			'singular_name' => esc_html( $bus_post_name ),
			'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
			'add_new_item' => esc_html__( 'Add New', 'wyzi-business-finder' ) . ' ' . $bus_post_name,
			'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
			'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . $bus_post_name,
			'new_item' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . $bus_post_name,
			'view' => esc_html__( 'View', 'wyzi-business-finder' ),
			'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . $bus_post_name,
			'search_items' => esc_html__( 'Search', 'wyzi-business-finder' ) . ' ' . $bus_post_name,

			'not_found' => esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . $bus_post_name . ' ' . esc_html__( 'found', 'wyzi-business-finder' ),
			'not_found_in_trash' => esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . $bus_post_name . ' ' . esc_html__( 'found in trash', 'wyzi-business-finder' ),
			'parent' => esc_html__( 'Parent', 'wyzi-business-finder' ) . ' ' . $bus_post_name,
		),
		'public' => true,
		'menu_position' => 57.1,
		'has_archive' => true,
		'supports' => array( 'title', 'thumbnail', 'editor', 'comments' ),
		'taxonomies' => array( '' ),
		'menu_icon' => plugins_url( 'images/posts.png', __FILE__ ),
		'exclude_from_search' => true,
		'rewrite' => array( 'slug' => esc_html( get_option( 'wyz_business_post_old_single_permalink' ) ) ),
	) );
}

/**
 * Class WyzBusinessPost.
 */

if (class_exists('WyzBusinessPostOverride')) {
	class WyzBusinessPostOverridden extends WyzBusinessPostOverride { }
} else {
	class WyzBusinessPostOverridden { }
}

class WyzBusinessPost extends WyzBusinessPostOverridden {


	private static $comments = array();

	/**
	 * Get Business category data.
	 *
	 * @param int $business_id the business id.
	 */
	private static function wyz_get_category_data( $business_id ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_get_category_data') ) {
			return WyzBusinessPostOverride::wyz_get_category_data( $business_id );
		}

		$cat_id = WyzHelpers::wyz_get_representative_business_category_id( $business_id );
		$parent_cat = get_term( $cat_id, 'wyz_business_category' );
		if ( ! is_wp_error( $parent_cat ) && ! empty( $parent_cat ) ) {
			//$parent_cat = $parent_cat[0];
			$cat_name = $parent_cat->name;
			$cat_link = get_term_link( $parent_cat );
			$cat_icn = wp_get_attachment_url( get_term_meta( $cat_id, 'wyz_business_icon_upload', true ) );
			$color = get_term_meta( $cat_id, 'wyz_business_cat_bg_color', true );
		} else {
			$cat_id = $cat_name = $cat_link = $cat_icn = $color = '';
		}

		$data = array(
			'id' => $cat_id,
			'name' => $cat_name,
			'color' => $color,
			'icon' => $cat_icn,
			'link' => $cat_link,
		);

		return $data;
	}
	


	/**
	 * Get Business data.
	 *
	 * @param int $id the business id.
	 */
	private static function wyz_get_business_data( $id ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_get_business_data') ) {
			return WyzBusinessPostOverride::wyz_get_business_data( $id );
		}

		$slgn = get_post_meta( $id, 'wyz_business_slogan', true );
		$dsc = get_post_meta( $id, 'wyz_business_description', true );
		$dsc = preg_replace("/<img[^>]+\>/i", " ", $dsc);
		$dsc = preg_replace("/<div[^>]+>/", "", $dsc);
		$dsc = preg_replace("/<\/div[^>]+>/", "", $dsc);
		$dsc = wp_strip_all_tags( $dsc );
		$cntr = get_post_meta( $id, 'wyz_business_country', true );
		$cntr_link = '';
		if ( '' != $cntr && ! empty( $cntr ) ) {
			$cntr_link = get_post_type_archive_link( 'wyz_business' ) . '?location=' . $cntr;
			$cntr = get_the_title( $cntr );
		}
		$wbst = get_post_meta( $id, 'wyz_business_website', true );

		$category = self::wyz_get_category_data( $id );

		$rate_nb = get_post_meta( $id, 'wyz_business_rates_count', true );
		$rate_sum = get_post_meta( $id, 'wyz_business_rates_sum', true );
		if ( 0 == $rate_nb ) {
			$rate = 0;
		} else {
			$rate = number_format( ( $rate_sum ) / $rate_nb, 1 );
		}

		if ( ! isset( $slgn ) || empty( $slgn ) ) {
			$slgn = '';
		}
		if ( ! isset( $dsc ) || empty( $dsc ) ) {
			$dsc = '';
		}
		if ( ! isset( $cntr ) || empty( $cntr ) ) {
			$cntr = '';
		}
		if ( ! isset( $wbst ) || empty( $wbst ) ) {
			$wbst = '';
		}

		$data = array(
			'slogan' => $slgn,
			'id' => $id,
			'description' => $dsc,
			'country_name' => $cntr,
			'country_link' => $cntr_link,
			'website' => $wbst,
			'category' => $category,
			'rate_number' => $rate_nb,
			'rate' => $rate,
		);

		return $data;
	}
	

	/**
	 * Creates business posts.
	 *
	 * @param array   $value the business data.
	 * @param boolean $is_current_user_author wheather current user is the business owner or not.
	 * @param boolean $is_wall is current page the businesses wall.
	 */
	public static function wyz_create_post( $value, $is_current_user_author = false, $is_wall = false ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_create_post') ) {
			return WyzBusinessPostOverride::wyz_create_post( $value, $is_current_user_author, $is_wall );
		}

		$template_type = 1;

		if ( function_exists( 'wyz_get_theme_template' ) )
			$template_type = wyz_get_theme_template();

		return $template_type == 1 ? self::wyz_create_post_1( $value, $is_current_user_author, $is_wall ) : self::wyz_create_post_2( $value, $is_current_user_author, $is_wall );
	}
	


	private static function wyz_create_post_1( $value, $is_current_user_author, $is_wall ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_create_post_1') ) {
			return WyzBusinessPostOverride::wyz_create_post_1( $value, $is_current_user_author, $is_wall );
		}

		$categories = self::wyz_get_category_data( $value['business_ID'] );

		$comm_count = get_comments_number( $value['ID'] );
		if ( 0 == $comm_count ) {
			$comm_stat = esc_html__( 'no comments', 'wyzi-business-finder' );
		} else
			$comm_stat = sprintf( _n( '%d<span> comment</span>', '%d<span> comments</span>', $comm_count, 'wyzi-business-finder' ), $comm_count );

		ob_start();
		?>

		<div class="animated sin-busi-post">
			<!-- Post Head -->
			<div class="head fix">
				<?php
				if ( $is_wall ) {
					echo '<a href="' . esc_url( get_the_permalink( $value['business_ID'] ) ) . '" class="post-logo">';
				}
				if ( has_post_thumbnail( $value['business_ID'] ) ) {
					echo get_the_post_thumbnail( $value['business_ID'], 'medium', array( 'class' => 'wyz-post-thumb' ) );
				}
				if ( $is_wall ) { echo '</a>'; }
				echo '<h3>';
				if ( $is_wall ) { echo '<a href="' . esc_url( get_the_permalink( $value['business_ID'] ) ) . '">'; }
				echo  esc_html( $value['name'] );
				if ( $is_wall ) { echo '</a>'; }
				?>
				</h3>
				<?php
				if ( ! $is_wall && ( $is_current_user_author || current_user_can( 'manage_options' ) ) ) { ?>
				<i class="bus-post-x fa fa-angle-down" data-id=<?php echo json_encode( $value['ID'] ); ?> data-comm_enabled=<?php echo ( comments_open( $value['ID'] ) ? 1 : 0 );?> data-title=<?php echo json_encode( $value['name'] );?>></i>
				<?php } ?>
			</div>
			<!-- Post Content -->
			<div class="content">
				<?php 
				$meta_custom = '';
				$meta_custom = get_post_meta( $business_data['id'] );
				var_dump($meta_custom);
				 ?>
								Estoy aca	

				<?php if ( isset( $value['post'] ) && ! empty( $value['post'] ) ) {
					echo '<p>' . $value['post'] . '</p>';
				} else {
					$value['post'] = '';
				}
				$vid = get_post_meta( $value['ID'], 'vid', true );
				if ( ! empty( $vid ) ) {
					echo $vid;
				}
				$post_image = '';
				if ( has_post_thumbnail( $value['ID'] ) ) {
					echo get_the_post_thumbnail( $value['ID'], 'large' );
					$post_image = get_the_post_thumbnail_url(  $value['ID'], 'large' );
				}
				if ( ! $post_image )$post_image = '';
				if ( '' !== $categories['icon'] ) { ?>
				<a class="busi-post-label wyz-secondary-color-text" style="background-color:<?php echo esc_attr( $categories['color'] );?>;" href="<?php echo esc_url( $categories['link'] );?>">
					<img src="<?php echo esc_url( $categories['icon'] );?>" alt="<?php echo esc_attr( $categories['name'] );?>" />
				</a>
				<?php } ?>
			</div>
			<!-- Post Footer -->
			<div class="footer fix">
				<?php
				$liked = ( is_array( $value ) && in_array( get_current_user_id(), $value['user_likes'] ) ) || $value ==  get_current_user_id();
				WyzPostShare::the_like_button( $value['ID'], 1, $value['likes'], $liked );
				if ( ! comments_open( $value['ID'] ) && 0 < get_comments_number( $value['ID'] ) ) {?>
				<div class="post-comment">
					<a href="<?php echo get_the_permalink( $value['ID'] );?>"><?php esc_html_e( 'comments closed', 'wyzi-business-finder' )?></a>
					<span><?php echo $comm_stat;?></span>
				</div>
				<?php } elseif ( comments_open( $value['ID'] ) ) {?>
				<div class="post-comment">
					<a href="<?php echo get_the_permalink( $value['ID'] );?>"><?php esc_html_e( 'comments', 'wyzi-business-finder' )?></a>
					<span><?php echo $comm_stat;?></span>
				</div>
				<?php }
				WyzPostShare::the_share_buttons( $value['ID'] );
				?>
				<span class="date"><?php echo esc_html( $value['time'] ); ?></span>


			</div>
			<?php self::get_post_comments();?>
			<div class="post-footer-comments">
				<?php if ( comments_open( $value['ID'] ) ) {?>
				<div class="post-footer-comment-form">
					<input type="text" class="wyz-input post_footer_comment_content" placeholder="<?php esc_html_e( 'post a comment','wyzi-business-finder' );?>..."/>
					<button class="btn-square wyz-primary-color wyz-prim-color post_footer_comment_btn" data-id="<?php echo $value['ID'];?>"><?php esc_html_e( 'Comment', 'wyzi-business-finder' );?></button>
					<input type="hidden" class="wyz_business_post_comment_nonce" value="<?php echo wp_create_nonce( 'wyz-business-post-comment-nonce-' . $value['ID'] ); ?>"/>
				</div>
				<?php }?>
				<div class="the-post-comments">
				<?php self::display_post_comments( $value['ID'] );?>
				</div>
			</div>
			
		</div>
		<?php
		return ob_get_clean();
	}
	


	private static function wyz_create_post_2( $value, $is_current_user_author, $is_wall ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_create_post_2') ) {
			return WyzBusinessPostOverride::wyz_create_post_2( $value, $is_current_user_author, $is_wall );
		}

		$categories = self::wyz_get_category_data( $value['business_ID'] );

		$comm_count = get_comments_number( $value['ID'] );
		if ( 0 == $comm_count ) {
			$comm_stat = esc_html__( 'no comments', 'wyzi-business-finder' );
		} else
			$comm_stat = sprintf( _n( '%d<span> comment</span>', '%d<span> comments</span>', $comm_count, 'wyzi-business-finder' ), $comm_count );

		ob_start();
		?>

		<div class="wall-post mb-20">
			<!-- Wall Post Head -->
			<div class="head">
				<?php
				if ( $is_wall ) {
					echo '<a href="' . esc_url( get_the_permalink( $value['business_ID'] ) ) . '" class="company-logo">';
				} else {
					echo '<span class="company-logo">';
				}
				if ( has_post_thumbnail( $value['business_ID'] ) ) {
					echo get_the_post_thumbnail( $value['business_ID'], 'medium', array( 'class' => 'wyz-post-thumb' ) );
				}
				if ( $is_wall ) { echo '</a>'; }
				else { echo '</span>'; }?>
				<div class="head-content">
				<h3 class="title">
					<?php if ( $is_wall ) echo '<a href="' . esc_url( get_the_permalink( $value['business_ID'] ) ) . '">';
					echo  esc_html( $value['name'] );
					if ( $is_wall ) { echo '</a>'; } ?>
				</h3>
				<?php echo '<p class="author">' . esc_html__( 'Post by', 'wyzi-business-finder' ) . ': <a href="' . esc_url( get_the_permalink( $value['business_ID'] ) ) . '">' . get_the_author_meta( 'display_name', get_post_field( 'post_author', $value['ID'] ) ) . '</a></p>';
				echo '<p class="category"><a href="'.esc_url( $categories['link'] ).'">'.esc_attr( $categories['name'] ).'</a></p>';?>
				</div>
				<?php
				if ( ! $is_wall && ( $is_current_user_author || current_user_can( 'manage_options' ) ) ) { ?>
				<i class="bus-post-x fa fa-angle-down" data-id=<?php echo json_encode( $value['ID'] ); ?> data-comm_enabled=<?php echo ( comments_open( $value['ID'] ) ? 1 : 0 );?> data-title=<?php echo json_encode( $value['name'] );?>></i>
				<?php } ?>
			</div>

			<?php
			if ( has_post_thumbnail( $value['ID'] ) ) {
				echo '<span class="image">' . get_the_post_thumbnail( $value['ID'], 'large' ) . '</span>';
			}?>

			<!-- Wall Post Content -->
			<div class="content">
								<?php 
				$meta_custom = '';
				$meta_custom = get_post_meta( $business_data['id'] );
				var_dump($meta_custom);
				 ?>
				<?php if ( isset( $value['post'] ) && ! empty( $value['post'] ) ) {
					echo '<p>' . $value['post'] . '</p>';
				} else {
					$value['post'] = '';
				}
				$vid = get_post_meta( $value['ID'], 'vid', true );
				if ( ! empty( $vid ) ) {
					echo $vid;
				}?>
			</div>
			<!-- Wall Post Footer -->
			<div class="footer fix">
				<?php
				$liked = ( is_array( $value ) && in_array( get_current_user_id(), $value['user_likes'] ) ) || $value ==  get_current_user_id();
				WyzPostShare::the_like_button( $value['ID'], 2 , $value['likes'], $liked);
					
				if ( ! comments_open( $value['ID'] ) && 0 < get_comments_number( $value['ID'] ) ) {?>
				<a class="wyz-prim-color-txt" href="<?php echo get_the_permalink( $value['ID'] );?>" title="<?php esc_html_e( 'comments closed', 'wyzi-business-finder' )?>" class="wall-no-comments"><i class="fa fa-reply"></i><span><?php echo $comm_stat;?></span></a>
				<?php } elseif ( comments_open( $value['ID'] ) ) {?>
				<a class="wyz-prim-color-txt" href="<?php echo get_the_permalink( $value['ID'] );?>"><i class="fa fa-reply"></i><span><?php echo $comm_stat;?></span></a>
				<?php } ?>
				<?php WyzPostShare::the_share_buttons( $value['ID'], 2 );?>
			</div>

			<?php self::get_post_comments();?>
			<div class="post-footer-comments">
				<?php if ( comments_open( $value['ID'] ) ) {?>
				<div class="post-footer-comment-form">
					<input type="text" class="wyz-input post_footer_comment_content" placeholder="<?php esc_html_e( 'post a comment','wyzi-business-finder' );?>..."/>
					<button class="action-btn bg-grey wyz-prim-color-hover post_footer_comment_btn" data-id="<?php echo $value['ID'];?>"><?php esc_html_e( 'Comment', 'wyzi-business-finder' );?></button>
					<input type="hidden" class="wyz_business_post_comment_nonce" value="<?php echo wp_create_nonce( 'wyz-business-post-comment-nonce-' . $value['ID'] ); ?>"/>
				</div>
				<?php }?>
				<div class="the-post-comments">
				<?php self::display_post_comments( $value['ID'] );?>
				</div>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}
	


	private static function display_post_comments( $post_id ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'display_post_comments') ) {
			return WyzBusinessPostOverride::display_post_comments( $post_id );
		}

		foreach ( self::$comments as $comment) {
			echo self::get_the_comment( $comment );
		}
		if ( 1 < get_comments_number( $post_id ) ) {?>
			<div class="the-comment the-comment-more">
				<div class="com-header">
					<span class="com-name wyz-primary-color-text wyz-prim-color-txt"><a class="com-name com-view-more wyz-primary-color-text wyz-prim-color-txt" data-id="<?php echo $post_id;?>" data-offset="1" href="#"><?php esc_html_e( 'View All', 'wyzi-business-finder' );?></a></span>
				</div>
			</div>
		<?php }
	}
	


	private static function comments_empty() {
		return empty( $this->comments );
	}

	private static function get_post_comments() {
		$args = array(
			'status' => 'approve',
			'number' => '1',
			'post_id' => get_the_ID(),
		);
		self::$comments = get_comments( $args );
	}


	public static function get_the_comment( $comment ) {

		if ( method_exists( 'WyzBusinessPostOverride', 'get_the_comment') ) {
			return WyzBusinessPostOverride::get_the_comment( $comment );
		}

		ob_start();?>
		<div class="the-comment">
			<div class="com-header">
				<?php $user = get_user_by( 'login', $comment->comment_author ); 
				$avatar = '';
				if ( $user ) {
					$username = $user->display_name;
					$avatar = get_avatar( $user->ID, 30, false, $username );
					echo $avatar;
				}
				else {
					$username = '';
				}?>
				<span class="com-name wyz-primary-color-text wyz-prim-color-txt"><?php echo $username;?></span>
				<span class="com-date"><?php WyzHelpers::the_publish_date( $comment->comment_date_gmt );?></span>
			</div>
			<div class="com-content<?php echo !empty($avatar)?' has-avatar':'';?>"><p><?php echo $comment->comment_content;?></p></div>
		</div>
		<?php
		return ob_get_clean();
	}
	


	private static function wyz_get_business_header( $is_grid ){

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_get_business_header') ) {
			return WyzBusinessPostOverride::wyz_get_business_header( $is_grid );
		}

		$sticky = is_sticky();
		ob_start();?>
		<div class="sin-busi-post<?php echo $is_grid ? ' bus-post-grid' : '';
									   echo $sticky ? ' bus-sticky' : '';?> sin-busi-item">
			<div class="head fix">
			<?php if ( $sticky ) {?>
					<div class="sticky-notice featured-banner"><span class="wyz-primary-color wyz-prim-color"><?php esc_html_e( 'FEATURED', 'wyzi-business-finder' );?></span></div>
			<?php }?>
			<?php if ( has_post_thumbnail() ) {?>
				<a href="<?php echo get_the_permalink();?>" class="post-logo"><?php the_post_thumbnail( 'medium' );?></a>
			<?php } ?>
				
				<h3><a href="<?php echo get_the_permalink();?>"><?php the_title();?></a></h3>
			</div>
		<?php
		return ob_get_clean();
	}
	


	private static function wyz_get_business_content( $business_data, $is_grid ){

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_get_business_content') ) {
			return WyzBusinessPostOverride::wyz_get_business_content( $business_data, $is_grid );
		}

		ob_start();
		$excerpt_len = $is_grid ? 150 : 230;?>
		<div class="content">
			<?php if ( '' != $business_data['description'] ) { ?>
				<p><?php echo WyzHelpers::substring_excerpt( $business_data['description'], $excerpt_len );//substr( $business_data['description'] , 0, $excerpt_len );?></p>
				<a class="read-more wyz-secondary-color-text" href="<?php echo esc_attr( get_permalink() );?>"><?php esc_html_e( 'read more', 'wyzi-business-finder' )?></a>
			<?php }?>
			<?php if ( '' !== $business_data['category']['icon'] ) { ?>
				<a class="busi-post-label" style="background-color:<?php echo esc_attr( $business_data['category']['color'] );?>;" href="<?php echo esc_url( $business_data['category']['link'] );?>">
					<img src="<?php echo esc_url( $business_data['category']['icon'] );?>" alt="<?php echo esc_attr( $business_data['category']['name'] );?>" />
				</a>
			<?php }?>
			</div>
		<?php
		return ob_get_clean();
	}
	


	private static function wyz_get_business_footer( $business_data ){

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_get_business_footer') ) {
			return WyzBusinessPostOverride::wyz_get_business_footer( $business_data );
		}

		ob_start();?>
			<div class="footer fix">
			<?php if ( '' !== $business_data['country_name'] ) { ?>
				<a href="<?php echo esc_url( $business_data['country_link'] );?>" class="post-like link">
					<i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html( $business_data['country_name'] ); ?>
				</a>
			<?php }
			if ( '' !== $business_data['website'] ) {?>
				<div class="post-like">
					<a target="_blank" class="link" href="<?php echo esc_url( $business_data['website'] );?>"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo esc_html( $business_data['website'] );?></a>
				</div>
			<?php }
				?>
				<div class="rate float-right" >
					<span>
					<?php if ( 0 != $business_data['rate_number'] ) {
						$rate = $business_data['rate'];
						for ( $i = 0; $i < 5; $i++ ) {

							if ( $rate > 0 ) {
								echo '<i class="fa fa-star star-checked" aria-hidden="true"></i>';
								$rate--;
							} else {
								echo '<i class="fa fa-star star-unchecked" aria-hidden="true"></i>';
							}
						}
					}?>
					</span>
				</div>
				<?php WyzPostShare::the_favorite_button( $business_data['id'] );?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	



	/**
	 * Creates business to display in a business archives page.
	 */
	public static function wyz_create_business() {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_create_business') ) {
			return WyzBusinessPostOverride::wyz_create_business();
		}

		$business_data = self::wyz_get_business_data( get_the_ID() );
		return self::wyz_get_business_header( false ) . self::wyz_get_business_content( $business_data, false ) . self::wyz_get_business_footer( $business_data );
	}
	


	public static function wyz_create_business_grid_look() {

		if ( method_exists( 'WyzBusinessPostOverride', 'wyz_create_business_grid_look') ) {
			return WyzBusinessPostOverride::wyz_create_business_grid_look();
		}

		$business_data = self::wyz_get_business_data( get_the_ID() );

		return self::wyz_get_business_header( true ) . self::wyz_get_business_content( $business_data, true ) . self::wyz_get_business_footer( $business_data );
	}

	/*public static function is_business_open( $business_id ) {
		//D	A textual representation of a day, three letters
		$days = WyzHelpers::get_days( $business_id );
		$days = $days[1];
		$key_arr = array(
			'Mon' => 0,
			'Tue' => 1,
			'Wed' => 2,
			'Thu' => 3,
			'Fri' => 4,
			'Sat' => 5,
			'Sun' => 6,
		);
		$now_day = $days[ date( 'D' ) ];
		if ( ( ! isset( $now_day[ 'open' ] ) || empty( $now_day[ 'open' ] ) ) && ( ! isset( $now_day[ 'close' ] ) || empty( $now_day['close'] ) ) ) return false;
		$_12_format = ( false !== strpos( $now_day['open'], 'AM' ) || false !== strpos( $now_day['open'], 'PM' ) || false !== strpos( $now_day['close'], 'PM' ) || false !== strpos( $now_day['close'], 'PM' ) );

		$start_time = '';
		$end_time = '';
		
		if ( $_12_format ) {
			
		}
	}*/
}
?>
