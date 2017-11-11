<?php
/**
 * Assistant functions
 *
 * @package wyz
 */

/**
 * Class WyzHelpers.
 */

if (class_exists('WyzHelpersOverride')) {
	class WyzHelpersOverridden extends WyzHelpersOverride { }
} else {
	class WyzHelpersOverridden { }
}

class WyzHelpers extends WyzHelpersOverridden
{

	/**
	 * Create the business sidebar.
	 *
	 * @param integer $id the business id.
	 */	
	public static function the_business_sidebar( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'the_business_sidebar') ) {
			return WyzHelpersOverride::the_business_sidebar( $id );
		}
		global $current_user;
		global $template_type;

		wp_get_current_user();
		$about = self::get_about( $id );

		/* Opening/Closing times. */
		$days = self::get_days( $id );
		$days_names = $days[0];
		$days_arr = $days[1];

		$author_id = self::wyz_the_business_author_id();

		$address = self::get_address( $id );
		$phone = self::get_phone( $id, $author_id );
		$email = self::get_email( $id, $author_id );
		$website = get_post_meta( $id, 'wyz_business_website', true );

		$no_days_data = true;

		for ( $i=0; $i<7; $i++)
			if ( ! empty( $days_arr[ $i ] ) ){
				$no_days_data = false;
				break;
			}

		if ( $template_type == 2 ) {
			self::the_business_sidebar_2( $id, $days_names, $days_arr, $author_id, $address, $phone, $email, $website, $no_days_data );
		} else {
			self::the_business_sidebar_1( $id, $days, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data );
		}
	}


	private static function the_business_sidebar_1 ( $id, $days, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data ) {
		ob_start();
		?>
		<!-- Business Sidebar -->
		<div class="business-sidebar <?php echo ( 'on' === wyz_get_option( 'resp' ) ? 'col-md-3 col-xs-12' : 'col-xs-3');?>">
		<?php
		if ( is_sticky() ) {
			echo '<div class="sticky-notice"><span class="wyz-primary-color">' . esc_html__( 'featured', 'wyzi-business-finder' ) . '</span></div>';
		}
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_description') ) {
		?>
			<!-- About Business Sidebar -->
			<div class="sin-busi-sidebar">
				<div class="about-business-sidebar fix">
					<div class="desc-see-more"><p><?php echo $about;?> </p></div>
				</div>
			</div>
			<?php }
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_opening_hours') ) {
			if ( ! $no_days_data ) { ?>
			<!-- Opening Hours Business Sidebar -->
			<div class="sin-busi-sidebar">
				<h4 class="sidebar-title"><?php echo esc_html( get_option( 'wyz_businesses_open_hrs' ) );?></h4>
				<div class="opening-hours-sidebar fix">
				<?php
				for( $i=0; $i<7; $i++)
					self::wyz_display_time( $days_arr[ $i ], $days_names[ $i ] );
				?>
				</div>
			</div>
			<?php } } 
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_contact_information_tab') &&
			( '' != $phone || '' != $address || '<a href="mailto:" target="_blank"></a>' != $email || '' != $website) ) {
			?>
			<!-- Contact Business Sidebar -->
			<div class="sin-busi-sidebar">
				<h4 class="sidebar-title"><?php esc_html_e( 'contact information', 'wyzi-business-finder' );?></h4>
				<div class="contact-info-sidebar fix">
		<?php
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_1') ) { 
					if ( '' != $phone ) {?>
					<p class="phone"><?php echo esc_html( $phone ); ?></p>
		<?php } }
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_address') ) {
					if ( '' != $address ) {
		?>
					<p class="address"><?php echo esc_html( $address );
					}
		?>
					</p>
		<?php } 
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_1') ) {
					if ( '' != $email ) {
		?>
					<p class="email"><?php echo $email; ?></p>
		<?php }} ?>
					<?php 
		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_website_url') ) {
					if ( '' !== $website ) { ?>
						<p class="website"><a target="_blank" href="<?php  echo esc_url( $website ) ?>"><?php echo esc_html( $website ) ?></a></p>
					<?php 
					}
		} 
					?>
				</div>
			</div>

		<?php }?>

		<?php if ( 'image' == get_option( 'wyz_business_header_content' ) ) { ?>
			<div class="sin-busi-sidebar">
				<?php WyzMap::wyz_the_business_map( $id, true ); ?>
			</div>
		<?php }

		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_social_media') ) {
			self::social_links( $id );
		}
		?>
			<div id="sticky-sidebar">
			<?php 
			if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_business_tags') ) {
				if ( $tags = get_the_term_list( $id, 'wyz_business_tag', '', ', ' ) ) {?>
					<div class="sin-busi-sidebar">
						<h4 class="sidebar-title"><?php esc_html_e( 'tags', 'wyzi-business-finder' );?></h4>
						<div class="tags-sidebar">
							<?php echo $tags;?>
						</div>
					</div>
			<?php }
			}
			/*if ( is_active_sidebar( 'wyz-single-business-sb' ) ) :
				dynamic_sidebar( 'wyz-single-business-sb' );
			endif;*/
			?>
			</div>
			<?php if ( 'off' != get_option( 'wyz_business_claiming' ) ) {
				echo '<a href="' . home_url( '/claim/?id=' ) . $id .'" class="light-blue-link wyz-primary-color-text">' . sprintf( esc_html__( 'Claim this %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ) . '</a>';
			}?>
		</div>

		<?php echo ob_get_clean();
	}

	private static function the_business_sidebar_2 ( $id, $days_names, $days_arr, $author_id, $address, $phone, $email, $website, $no_days_data ) {
		$can_address = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_address') && ! empty( $address );
		$can_email = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_1') && ! empty( $email ) && '<a href="mailto:" target="_blank"></a>' != $email;
		$can_phone = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_1') && ! empty( $phone );
		$can_website = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_website_url') && ! empty( $website );
		$can_info_tab = $can_address || $can_email || $can_phone || $can_website ;
		ob_start();?>

		<!-- Business Sidebar -->
		<div class="sidebar-wrapper<?php if ( 'off' === wyz_get_option( 'resp' ) ) { ?> col-xs-4 <?php } else { ?> col-md-4 col-xs-12<?php } ?>">
		<?php 
		if ( is_sticky() ) {
			echo '<div class="sticky-notice"><span class=" wyz-prim-color">' . esc_html__( 'featured', 'wyzi-business-finder' ) . '</span></div>';
		}

		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_contact_information_tab') && $can_info_tab ) { ?>
				<!-- Contact Business Sidebar -->
			<div class="widget widget_text">
				<h4 class="widget-title"><?php esc_html_e( 'contact info', 'wyzi-business-finder' );?></h4>

				<div class="contact-info-widget">
					<?php if ( $can_address ) { ?>
					<div class="single-info fix">
						<h5><?php esc_html_e( 'Address', 'wyzi-business-finder' );?></h5>
						<p><?php echo esc_html( $address );?></p>
					</div>
					<?php } 
					if ( $can_email ) { ?>
					<div class="single-info fix">
						<h5><?php esc_html_e( 'E-mail', 'wyzi-business-finder' );?></h5>
						<p><?php echo $email; ?></p>
					</div>
					<?php }
					if ( $can_phone ) { ?>
					<div class="single-info fix">
						<h5><?php esc_html_e( 'Phone', 'wyzi-business-finder' );?></h5>
						<p><?php echo esc_html( $phone ); ?></p>
					</div>
					<?php }
					if ( $can_website ) {?>
					<div class="single-info fix">
						<h5><?php esc_html_e( 'Website', 'wyzi-business-finder' );?></h5>
						<p class="website"><a target="_blank" href="<?php  echo esc_url( $website ); ?>"><?php echo esc_html( $website ); ?></a></p>
					</div>
					<?php } ?>
				</div>
			</div>

		<?php }

		if ( 'image' == get_option( 'wyz_business_header_content' ) ) { ?>
			<div class="widget">
				<?php WyzMap::wyz_the_business_map( $id, true ); ?>
			</div>
		<?php }

		$all_business_rates = get_post_meta( $id, 'wyz_business_ratings', true );
		if ( empty($all_business_rates))$all_business_rates = array(-1);
		$args = array(
			'post_type' => 'wyz_business_rating',
			'post__in' => $all_business_rates,
			'posts_per_page' => 3,
			//'paged' => $page,
		);
		$query = new WP_Query( $args );

		$first_id = - 1;
		if ( $query->have_posts() ) {?>
		<!-- Sidebar Widget -->
		<div class="widget">
			<!--Widget Title-->
			<h4 class="widget-title"><?php esc_html_e( 'Recent Ratings', 'wyzi-business-finder' );?></h4>
			<!-- Rating Widget -->
			<div class="rating-widget">

			<?php while ( $query->have_posts() ) {
				$query->the_post();
				$rate_id = get_the_ID();
				$first_id = $rate_id;
				echo WyzBusinessRating::wyz_create_rating( $rate_id, 2 );
			}
			wp_reset_postdata(); ?>
			</div>
		</div>

		<div class="widget">
			<!--Widget Title-->
			<?php $rate_stats = WyzBusinessRating::get_business_rates_stats( $id );?>
			<h4 class="widget-title"><?php esc_html_e( 'All Ratings', 'wyzi-business-finder' );?></h4>
			<!-- Rating Widget -->
			<div class="rating-widget">
				<div class="single-rating fix">
					<div class="head fix">
						<?php echo WyzBusinessRating::get_business_rates_stars( $id, $display_count = true, $rate_stats );?>
					</div>
				</div>

				<?php echo WyzBusinessRating::get_business_rates_cats_perc( $id, $all_business_rates, $rate_stats['rate_nb'] );?>

			</div>
		</div>


			<?php
		}
		global $business_data;
		if ( '' != $business_data && property_exists( $business_data, 'rate_form' ) )
			$business_data->rate_form();


		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_opening_hours') && ! $no_days_data ) {?>
		<div class="widget">
			<!--Widget Title-->
			<h4 class="widget-title"><?php echo esc_html( get_option( 'wyz_businesses_open_hrs' ) );?></h4>
			<!-- Opening Time Widget -->
			<div class="open-time-widget">
				<ul>
					<?php
					for( $i=0; $i<7; $i++)

						if ( ! empty( $days_arr[ $i ] ) ) {?>
							<div class="clearfix">
								<li><span class="day"><?php esc_html_e( $days_names[ $i ], 'wyzi-business-finder');?></span>
								<span class="dates">
								<?php foreach ( $days_arr[ $i ] as $key => $value ) {?>
									<?php  echo '<span class="date wyz-prim-color-txt-hover">' . ( isset( $value['open'] ) ? '<span class="open">' . esc_html( $value['open'] ) . '</span>': '' ) . ' - ' . ( isset( $value['close'] ) ? '<span class="closed">' . esc_html( $value['close']   ). '</span>' : '' ) . '</span>'; ?>
								<?php }?>
								</span>
								</li>
							</div>
							
						<?php }
					?>
				</ul>
			</div>
		</div>
		<?php
		}

		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_business_tags') ) {
			if ( $tags = get_the_term_list( $id, 'wyz_business_tag', '', '' ) ) {?>

			<div class="widget">
				<!--Widget Title-->
				<h4 class="widget-title"><?php esc_html_e( 'tags', 'wyzi-business-finder' );?></h4>
					<!-- Tags Widget -->
				<div class="tag-widget fix">
					<?php echo $tags;?>
				</div>
			</div>
		<?php }
		}


		if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_social_media') ) {
			self::social_links( $id );
		
		if ( 'off' != get_option( 'wyz_business_claiming' ) ) {
			echo '<a href="' . home_url( '/claim/?id=' ) . $id .'" class="light-blue-link wyz-prim-color-txt">' . sprintf( esc_html__( 'Claim this %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ) . '</a>';
		}

		if ( is_active_sidebar( 'wyz-single-business-sb' ) ) :
			dynamic_sidebar( 'wyz-single-business-sb' );
		endif;
		}?>
		</div>
		<?php echo ob_get_clean();
	}


	private static function get_sidebar_business_map() {

	}

	private static function get_about( $id ) {
		$logged_in_user = is_user_logged_in();
		$about = get_post_meta( $id, 'wyz_business_description', true );
		$about = preg_replace("/<img[^>]+\>/i", " ", $about);
		$about = preg_replace("/<div[^>]+>/", "", $about);
		$about = preg_replace("/<\/div[^>]+>/", "", $about);
		$about = wp_strip_all_tags( $about );
		if ( is_singular( 'wyz_offers' ) ) 
			$about_link = get_permalink( $id ) . '#about';
		else
			$about_link = '#about';
		$about = self::substring_excerpt($about, 150 ) . '<a href="' . $about_link . '" class="read-more wyz-secondary-color-text">' . esc_html__( 'show more', 'wyzi-business-finder' ) . '</a>';
		//substr( $about, 0, 150 ) . '<a href="#about" class="read-more" data-toggle="tab">' . esc_html__( 'show more', 'wyzi-business-finder' ) . '</a>';
		return $about;
	}


	public static function get_days( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_days') ) {
			return WyzHelpersOverride::get_days( $id );
		}
		$days_arr = array();
		$days_names = array(
			esc_html__( 'Mon', 'wyzi-business-finder' ),
			esc_html__( 'Tue', 'wyzi-business-finder' ),
			esc_html__( 'Wed', 'wyzi-business-finder' ),
			esc_html__( 'Thu', 'wyzi-business-finder' ),
			esc_html__( 'Fri', 'wyzi-business-finder' ),
			esc_html__( 'Sat', 'wyzi-business-finder' ),
			esc_html__( 'Sun', 'wyzi-business-finder' ),
		);
		$days_ids = array( 'open_close_monday', 'open_close_tuesday', 'open_close_wednesday',
					'open_close_thursday', 'open_close_friday', 'open_close_saturday', 'open_close_sunday' );
		for( $i=0; $i<7; $i++)
			$days_arr[] = self::wyz_set_time( get_post_meta( $id, 'wyz_' . $days_ids[ $i ], true ) );

		return array( $days_names, $days_arr );
	}


	private static function get_address( $id ) {
		$prefix = 'wyz_';

		$bldg = get_post_meta( $id, $prefix . 'business_bldg', true );
		$street = get_post_meta( $id, $prefix . 'business_street', true );
		$city = get_post_meta( $id, $prefix . 'business_city', true );
		$country = get_post_meta( $id, $prefix . 'business_country', true );
		if ( '' != $country )
			$country = get_the_title( $country );
		else $country = '';
		$additional_address = get_post_meta( $id, $prefix . 'business_addition_address_line', true );
		$address = '';
		if ( '' !== $bldg ) {
			$address .= $bldg . ', ';
		}
		if ( '' !== $street ) {
			$address .=  $street . ', ';
		}
		if ( '' !== $city ) {
			$address .= $city . ', ';
		}
		if ( '' !== $country ) {
			$address .= $country . ', ';
		}
		if ( '' !== $additional_address ) {
			$address .= $additional_address . ', ';
		}
		if ( '' != $address ) {
			$address = substr( $address, 0, strlen( $address ) - 2 );
		}
		return $address;
	}

	private static function get_phone( $id, $author_id ) {
		$phone1 = get_post_meta( $id, 'wyz_business_phone1', true );
		$phone2 = get_post_meta( $id, 'wyz_business_phone2', true );
		

		if ( ! self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_2') ) { 
			$phone2 = '';
		}

		$final_phone = '';
		if ( '' === $phone2 ) {
		    $final_phone = $phone1;
		} elseif ( '' === $phone1 ) {
		    $final_phone = $phone2;
		} else {
		    $final_phone = $phone1 . ' / ' . $phone2;
		}

		return $final_phone;
	}

	private static function get_email( $id, $author_id ) {
		$email1 = get_post_meta( $id, 'wyz_business_email1', true );
		$email2 = get_post_meta( $id, 'wyz_business_email2', true );

		if ( ! self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_2') ) { 
			$email2 = '';
		}

		$final_email = '';
		if ( '' === $email2 ) {
		    $final_email = '<a href="mailto:' . esc_attr( $email1 ) . '" target="_blank">' . esc_html( $email1 ) . '</a>';
		} elseif ( '' === $email1 ) {
		    $final_email = '<a href="mailto:' . esc_attr( $email2 ) . '" target="_blank">' . esc_html( $email2 ) . '</a>';
		} else {
		    $final_email = '<a href="mailto:' . esc_attr( $email1 ) . '" target="_blank">' . esc_html( $email1 ) . '</a> / <a href="mailto:' . esc_attr( $email2 ) . '" target="_blank">' . esc_html( $email2 ) . '</a>';
		}

		return $final_email;
	}

	private static function social_links( $id ) {
		$social = array();
		$ids = array( 'wyz_business_facebook','wyz_business_twitter','wyz_business_linkedin','wyz_business_google_plus','wyz_business_youtube',
			'wyz_business_flicker','wyz_business_pinterest','wyz_business_instagram' );

		foreach ($ids as $d) {
			$social[] = get_post_meta( $id, $d, true );
		}

		$has_social_links = false;
		foreach ( $social as $s ) {
			if ( '' != $s ) {
				$has_social_links = true;
				break;
			}
		}

		if ( $has_social_links ) {?>
			<!-- Social Business Sidebar -->
			<div class="sin-busi-sidebar widget social-widget">
				<h4 class="sidebar-title widget-title"><?php esc_html_e( 'social media', 'wyzi-business-finder' );?></h4>
				<div class="sidebar-social fix">
			<?php if ( isset( $social[0] ) && ! empty( $social[0] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[0] ); ?>" class="facebook wyz-prim-color-hover" target="_blank"><i class="fa fa-facebook"></i></a>
			<?php }

			if ( isset( $social[1] ) && ! empty( $social[1] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[1] ); ?>" class="twitter wyz-prim-color-hover" target="_blank"><i class="fa fa-twitter"></i></a>
			<?php }

			if ( isset( $social[2] ) && ! empty( $social[2] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[2] ); ?>" class="linkedin wyz-prim-color-hover" target="_blank"><i class="fa fa-linkedin"></i></a>
			<?php }

			if ( isset( $social[3] ) && ! empty( $social[3] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[3] ); ?>" class="google-plus wyz-prim-color-hover" target="_blank"><i class="fa fa-google-plus"></i></a>
			<?php }

			if ( isset( $social[4] ) && ! empty( $social[4] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[4] ); ?>" class="youtube-play wyz-prim-color-hover" target="_blank"><i class="fa fa-youtube-play"></i></a>
			<?php }

			if ( isset( $social[5] ) && ! empty( $social[5] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[5] ); ?>" class="flickr wyz-prim-color-hover" target="_blank"><i class="fa fa-flickr"></i></a>
			<?php }

			if ( isset( $social[6] ) && ! empty( $social[6] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[6] ); ?>" class="pinterest-p wyz-prim-color-hover" target="_blank"><i class="fa fa-pinterest-p"></i></a>
			<?php }

			if ( isset( $social[7] ) && ! empty( $social[7] ) ) { ?>

					<a href="<?php echo WyzHelpers::wyz_link_auth( $social[7] ); ?>" class="instagram wyz-prim-color-hover" target="_blank"><i class="fa fa-instagram"></i></a>
			<?php } ?>
				</div>
			</div>
		<?php } 
	}


	public static function wyz_set_time( $data ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_set_time') ) {
			return WyzHelpersOverride::wyz_set_time( $data );
		}
		$open_close = array();
		if ( '' != $data && ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ( isset( $value['open'] ) && '' != $value['open'] ) ||
					( isset( $value['close'] ) && '' != $value['close'] ) ) {
					$open_close[] = $value;
				}

			}
		}
		return $open_close;
	}

	private static function wyz_display_time( $arr, $day ) {
		if ( ! empty( $arr ) ) {?>
			<div class="clearfix">
				<p class="day wyz-secondary-color-text"><?php esc_html_e( $day, 'wyzi-business-finder');?></p>
				<div class="time-container">
				<?php foreach ( $arr as $key => $value ) {?>
					<div><p class="time"><?php  echo ( isset( $value['open'] ) ?  esc_html( $value['open'] ) : '' ) . ' - ' . ( isset( $value['close'] ) ?  esc_html( $value['close'] ) : '' ); ?></p></div>
				<?php }?>
				</div>
			</div>
			
		<?php }
	}

	/**
	 * Get the business subheader below the map.
	 *
	 * @param integer $id business id.
	 */
	public static function wyz_the_business_subheader( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_the_business_subheader') ) {
			return WyzHelpersOverride::wyz_the_business_subheader( $id );
		}
		ob_start();
		$prefix = 'wyz_';
		$name = get_the_title( $id );
		if ( has_post_thumbnail( $id ) ) {
			$logo = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
		} else {
			$logo = self::get_default_image( 'business' );
		}
		$description = get_post_meta( $id, $prefix . 'business_excerpt', true );
		$slogan = get_post_meta( $id, $prefix . 'business_slogan', true );
		?>
		<div class="business-data-area">
			<div class="container">
				<div class="row">
					<?php WyzPostShare::the_favorite_button( $id );?>
					<div class="business-data-wrapper col-xs-12">
						<?php
						if ( self::wyz_sub_can_bus_owner_do(self::wyz_the_business_author_id(),'wyzi_sub_show_business_logo') ) {
							if ( is_singular( 'wyz_offers' ) ) {
								echo get_the_post_thumbnail( $id, 'medium', array( 'class' => 'logo float-left' ) );
							} else {
								the_post_thumbnail( 'medium', array( 'class' => 'logo float-left' ) );
							}
						} 
						?>
						<div class="content fix">
							<?php echo WyzHelpers::verified_icon( $id );?>
							<h1><?php echo esc_html( $name );
								if ( '' != $slogan ) {
									echo ' - ' . $slogan;
								}?></h1>
							<h2><?php echo esc_html( $description );?></h2>
						</div>
						<?php 
						if ( self::wyz_sub_can_bus_owner_do(self::wyz_the_business_author_id(),'wyzi_sub_business_show_social_shares') ) {
								echo self::wyz_get_social_links( $id ); 
						} 
						if ( function_exists( 'wyz_breadcrumbs' ) ) {
							echo '<div>' . wyz_breadcrumbs() . '</div>';
						}?>
					</div>
				</div>
			</div>
		</div>

		<?php echo ob_get_clean();
	}


	public static function get_default_image( $image ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_default_image') ) {
			return WyzHelpersOverride::get_default_image( $image );
		}
		$def = '';
		$img = '';
		if ( 'business' == $image ) {
			$def = WYZI_PLUGIN_URL . 'businesses-and-offers\businesses\images\default-business.png';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-business-logo' );
			}
		} elseif ( 'offer' == $image ) {
			$def = WYZI_PLUGIN_URL . 'businesses-and-offers\offers\images\offer-default-icon.jpg';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-offer-logo' );
			}
		} elseif ( 'location' == $image ) {
			$def = WYZI_PLUGIN_URL . 'locations\images\location_default_image.png';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-location-logo' );
			}
		}
		return ! empty( $img ) ? $img : $def;
	}

	public static function fisherYatesShuffle(&$items, $seed) {
		@mt_srand($seed);
		for ($i = count($items) - 1; $i > 0; $i--) {
			$j = @mt_rand(0, $i);
			$tmp = $items[$i];
			$items[$i] = $items[$j];
			$items[$j] = $tmp;
		}
	}

	/**
	 * Get the business verified icon.
	 *
	 * @param integer $id business id.
	 */
	private static $verified_icon;
	public static function verified_icon( $id=-1 ) {
		if ( method_exists( 'WyzHelpersOverride', 'verified_icon') ) {
			return WyzHelpersOverride::verified_icon( $id );
		}
		if ( $id<1)$id=get_the_ID();
		if('yes'!==get_post_meta($id,'wyz_business_verified',true))return '';
		if ( '' == self::$verified_icon )
			self::$verified_icon = '<img alt="'.esc_html__( 'verified', 'wyzi-business-finder' ).'" src="'.plugin_dir_url( __FILE__ ) . 'img/verified.png' .'" class="verified-icon"/>';
		return self::$verified_icon;
	}


	public static function wyz_info( $msg, $return = false, $attr='' ) {
		 $info = '<div ' . $attr . ' class="wyz-info"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $info;
		 echo $info;
	}

	public static function wyz_success( $msg, $return = false ) {
		 $success = '<div class="wyz-success"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $success;
		 echo $success;
	}

	public static function wyz_warning( $msg, $return = false ) {
		 $warning = '<div class="wyz-warning"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $warning;
		 echo $warning;
	}

	public static function wyz_error( $msg, $return = false ) {
		 $error = '<div class="wyz-error"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $error;
		 echo $error;
	}


	/**
	 * Query businesses with featured in mind
	 *
	 * @param integer $business_id business id.
	 */
	public static function query_businesses( $args = array(), $shortcode = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'query_businesses') ) {
			return WyzHelpersOverride::query_businesses( $args, $shortcode );
		}

		$featured_posts_per_page = get_option( 'wyz_featured_posts_perpage', 2 );

		if ( 0 == $featured_posts_per_page )
			return new WP_Query( $args );

		$sticky_posts = get_option( 'sticky_posts' );

		if ( is_tax( 'wyz_business_category' ) || $shortcode ) {
			$cat_feat = array(
				'post_type' => 'wyz_business',
				'posts_per_page' => -1,
				'post__in' => $sticky_posts,
				'fields' => 'ids',
			);
			if ( ! $shortcode ) {
				$cat_feat['tax_query'] = array(
					array(
						'taxonomy' => 'wyz_business_category',
						'field'    => 'term_id',
						'terms'    => get_queried_object()->term_id,
					),
				);
			}
			$sticky_posts = ( new WP_Query( $cat_feat ) )->posts;
		}

		if ( empty( $sticky_posts ) || ( isset( $args['paged'] ) && 1 < $args['paged'] ) ) {
			$args['post_type'] = 'wyz_business';
			return new WP_Query( $args );
		}

 
		$featured_businesses_args = array(
			'post_type' => 'wyz_business',
			//'posts_per_page' => $featured_posts_per_page,
			'post__in' => $sticky_posts,
			'fields' => 'ids',
		);

		if ( isset( $args['tax_query'] ) ) {
			$featured_businesses_args['tax_query'] = $args['tax_query'];
		}

		if ( isset( $args['paged'] ) && 1 < $args['paged'] ) {
			$featured_businesses_args['paged'] = $args['paged'];
		}

		$featured_businesses_args = apply_filters( 'wyz_query_featured_businesses_args_search', $featured_businesses_args, $args );


		$query1 = new WP_Query( $featured_businesses_args );

		$sticky_posts = $query1->posts;

		if ( count( $sticky_posts ) > $featured_posts_per_page ) {

			self::fisherYatesShuffle( $sticky_posts, rand(10,100) );
			$sticky_posts = array_slice( $sticky_posts, 0, $featured_posts_per_page );
		}


		$args['fields'] = 'ids';
		$args['post__not_in'] = $sticky_posts;
		$args['post_type'] = 'wyz_business';

		$query2 = new WP_Query( $args );

		$all_the_ids = array_merge( $sticky_posts, $query2->posts );

		if ( empty( $all_the_ids ) ) $all_the_ids = array( 0 );

		$final_query_args = array(
			'post_type' => 'wyz_business',
			'post__in' => $all_the_ids,
			'orderby' => 'post__in',
		);

		if ( isset( $args['paged'] ) ) {
			$final_query_args['paged'] = $args['paged'];
		}

		return new WP_Query( $final_query_args );
	}


	public static function substring_excerpt ( $string, $length ) {
		if ( method_exists( 'WyzHelpersOverride', 'substring_excerpt') ) {
			return WyzHelpersOverride::substring_excerpt( $string, $length );
		}
		$substring = substr( $string , 0, $length );
		$sub_len = strlen( $substring );
		if ( $sub_len < $length )
			return $substring;
		$temp_substr = $substring;

		for ( $i = $sub_len - 1; $i >= 0; $i-- ) {
			if ( substr( $temp_substr, $i, 1)  == " " )
				break;
			$temp_substr = substr( $temp_substr, 0, $i );
		}

		if ( strlen( $temp_substr ) == 0 )
			return $substring;
		return $temp_substr;
	}


	/**
	 * Get the business social links.
	 *
	 * @param integer $business_id business id.
	 */
	public static function wyz_get_social_links( $business_id ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_get_social_links') ) {
			return WyzHelpersOverride::wyz_get_social_links( $business_id );
		}

		$fbid = wyz_get_option( 'businesses_fb_app_ID' );

		//WyzPostShare::the_js_scripts();

		ob_start();?>	
		<div class="business-social">
			<?php if ( true ) {?>
			<script>
				window.fbAsyncInit = function(){
				FB.init({
				    appId: "<?php echo esc_js( $fbid );?>", status: true, cookie: true, xfbml: true }); 
				};
				(function(d, debug){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
				    if(d.getElementById(id)) {return;}
				    js = d.createElement('script'); js.id = id; 
				    js.async = true;js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
				    ref.parentNode.insertBefore(js, ref);}(document, true));
				function postToFeed(title, desc, url, image){
					var obj = {method: 'feed',link: url, picture: image,name: title,description: desc};
					function callback(response){/*alert('done ' + JSON.stringify(response));*/}
					FB.ui(obj, callback);
				}
			</script>


			<div class="social social-facebook">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-facebook"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<div class="fb-like" data-href="<?php echo get_permalink(); ?>" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
					
					<div id="fb-root"></div>
					<script>
					//<![CDATA[
					(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=<?php echo esc_js( $fbid );?>";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
					//]]>
					</script>
				 </div>
			</div>
			<?php }?>

			<div class="social social-twitter">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-twitter"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<iframe allowtransparency="true" scrolling="no" src="//platform.twitter.com/widgets/tweet_button.html" style="width:60px; height:20px;"></iframe>
				</div>
			</div>
			<div class="social social-googleplus">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-google-plus"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<div class="g-plusone" data-size="medium"></div>
				</div>
			</div>
			<script type="text/javascript">
			//<![CDATA[
			(function() {
				var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
				po.src = "https://apis.google.com/js/plusone.js";
				var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
			})();
			//]]>
			</script>

			<div class="social social-linkedin">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-linkedin"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
					<script type="IN/Share" data-url="<?php the_permalink(); ?>" data-counter="right"></script>
				</div>
			</div>

		</div>
		<?php return ob_get_clean();
	}


	/**
	 * Check if current user is the post author.
	 *
	 * @param integer $post_id the post id.
	 */
	public static function wyz_is_current_user_author( $post_id ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_is_current_user_author') ) {
			return WyzHelpersOverride::wyz_is_current_user_author( $post_id );
		}

		$post = get_post( $post_id );
		if ( null === $post ) {
			return false;
		}
		return get_current_user_id() == $post->post_author;
	}


	public static function add_business_to_user( $user_id, $business_id, $status ) {
		$user_businesses = self::get_user_businesses( $user_id );

		if ( ! isset( $user_businesses['pending'][ $business_id ] ) && ! isset( $user_businesses['published'][ $business_id ] ) ) {
			$count = get_user_meta( $user_id, 'wyz_user_businesses_count', true );
			if ( empty( $count ) ) $count = 0;
			else $count = intval( $count );
			$count++;
			update_user_meta( $user_id, 'wyz_user_businesses_count', $count );
		}
		if ( 'publish' == $status )$status = 'published';
		$user_businesses[ $status ][ $business_id ] = $business_id;
		update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );
	}


	private static $is_calendar_page;

	public static function is_calendar_page() {
		if ( ! isset( self::$is_calendar_page ) || empty( self::$is_calendar_page ) )
			self::$is_calendar_page = isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) &&  current_user_can( 'edit_businesses', $_GET[ WyzQueryVars::BusinessCalendar ] );
		return self::$is_calendar_page;
	}



	public static function remove_business_from_user( $user_id, $business_id ) {

		$user_businesses = self::get_user_businesses( $user_id );
		$deleted = false;
		if ( isset( $user_businesses['pending'][ $business_id ] ) ){
			unset( $user_businesses['pending'][ $business_id ] );
			$deleted = true;
		}
		if ( isset( $user_businesses['published'][ $business_id ] ) ){
			unset( $user_businesses['published'][ $business_id ] );
			$deleted = true;
		}
		if ( $deleted ) {
			$count = intval( get_user_meta( $user_id, 'wyz_user_businesses_count', true ) );
			$count--;
			update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );
			if ( $count < 0 ) $count = 0;
			update_user_meta( $user_id, 'wyz_user_businesses_count', $count );
		}
	}

	public static function get_user_businesses( $user_id = false ) {
		if ( ! $user_id ) $user_id = get_current_user_id();

		$user_businesses = get_user_meta( $user_id, 'wyz_user_businesses', true );
		if ( empty( $user_businesses ) ) {
			$user_businesses = array();
			$user_businesses['pending'] = array();
			$user_businesses['published'] = array();
		}
		return $user_businesses;
	}


	/**
	 * Get the user's calendar for the provided business id, or the business id set in $_GET
	 *
	 * @param int  $user_id current user id.
	 * @param int $business_id user's business id.
	 *
	 * @return int the calendar corresponding to the user's business
	 */
	public static function get_user_calendar( $user_id = false, $business_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		$business_id = $business_id ? $business_id : ( isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) ? $_GET[ WyzQueryVars::BusinessCalendar ] : '' );

		if ( empty( $user_id ) || empty( $business_id ) )
			return false;
		$calendars = get_user_meta( $user_id, 'wyz_business_calendars', true );
		if ( ! isset( $calendars[ $business_id ] ) || empty( $calendars[ $business_id ] ) )
			return false;
		if ( term_exists( $calendars[ $business_id ], 'booked_custom_calendars' ) )
			return $calendars[ $business_id ];
		
		return false;
	}

	/**
	 * Get the user's favorite businesses.
	 *
	 * @param int  $user_id current user id.
	 *
	 * @return array the ids of the favorite businesses
	 */
	public static function get_user_favorites( $user_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		$favorites = get_user_meta( $user_id, 'wyz_user_favorites', true );
		if ( empty( $favorites ) || '' == $favorites ) return array();
		return $favorites;
	}


	/**
	 * Set the user's calendar for the provided business id
	 *
	 * @param int $business_id user's business id.
	 * @param int $calendar_id calendar's id.
	 * @param int  $user_id current user id.
	 */
	public static function set_user_calendar( $business_id, $calendar_id, $user_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		if ( ( $calendar_id = intval( $calendar_id ) ) < 1 ) return false;
		$calendars = get_user_meta( $user_id, 'wyz_business_calendars', true );
		if ( empty( $calendars ) ) $calendars = array();
		$calendars[ $business_id ] = $calendar_id;
		update_user_meta( $user_id, 'wyz_business_calendars', $calendars );
		return true;
	}

	/**
	 * Authenticate a link to make wp compatible.
	 *
	 * @param string  $link the link to authenticate.
	 * @param boolean $isfb is a facebook link.
	 */
	public static function wyz_link_auth( $link, $isfb = false ) {

		if ( ! isset( $link ) || '' == $link ) {
			return '';
		}
		$hd = substr( $link, 0, 4 );
		if ( 'http' == $hd ) {
			return esc_url( $link );
		}
		if ( $isfb ) {
			return esc_url( 'http://' . $link );
		}
		return  esc_url( '//' . $link );
	}


	/**
	 * Clear all query arguments from current url,
	 * and add query argument to it.
	 *
	 * @param string  $query_arg the query argument to add.
	 */
	public static function add_clear_query_arg( $query_arg, $hash = '' ) {
		$url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );
		if ( '' != $hash )
			$url[0] .= "#$hash";
		return add_query_arg( $query_arg, $url[0] );
	}

	/**
	 * Authenticate date.
	 *
	 * @param string $time the time to authenticate.
	 */
	public static function wyz_date_auth( $time ) {
		$pattern = "/^(?:0[1-9]|1[0-2]):[0-5][0-9] (am|pm|AM|PM)$/";
		if ( preg_match( $pattern, $time ) ) {
			return true;
		}
		return false;
	}
	

	public static function the_publish_date( $publish_time, $full = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'the_publish_date') ) {
			return WyzHelpersOverride::the_publish_date( $publish_time, $full );
		}

		$now = new DateTime;
		$ago = new DateTime( $publish_time );
		$diff = $now->diff( $ago );

		$diff->w = floor( $diff->d / 7 );
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => esc_html__( '%d year', 'wyzi-business-finder' ),
			'm' => esc_html__( '%d month', 'wyzi-business-finder' ),
			'w' => esc_html__( '%d week', 'wyzi-business-finder' ),
			'd' => esc_html__( '%d day', 'wyzi-business-finder' ),
			'h' => esc_html__( '%d hour', 'wyzi-business-finder' ),
			'i' => esc_html__( '%d minute', 'wyzi-business-finder' ),
			's' => esc_html__( '%d second', 'wyzi-business-finder' ),
		);
		$strings = array(
			'y' => esc_html__( '%d years', 'wyzi-business-finder' ),
			'm' => esc_html__( '%d months', 'wyzi-business-finder' ),
			'w' => esc_html__( '%d weeks', 'wyzi-business-finder' ),
			'd' => esc_html__( '%d days', 'wyzi-business-finder' ),
			'h' => esc_html__( '%d hours', 'wyzi-business-finder' ),
			'i' => esc_html__( '%d minutes', 'wyzi-business-finder' ),
			's' => esc_html__( '%d seconds', 'wyzi-business-finder' ),
		);
		foreach ( $string as $k => &$v ) {
			if ( $diff->$k ) {
				$v = sprintf( _n( $v, $strings[ $k ], $diff->$k, 'wyzi-business-finder' ), $diff->$k );
			} else {
				unset( $string[$k] );
			}
		}

		if ( ! $full ) {
			$string = array_slice( $string, 0, 1 );
		}

		echo $string ? sprintf( esc_html__( '%s ago', 'wyzi-business-finder' ), implode( ', ', $string ) ) : esc_html__( 'just now', 'wyzi-business-finder' );
	}


	/**
	 * Fix image alpha mask blending.
	 */
	public static function wyz_imagealphamask( &$picture, $mask ) {
		// Get sizes and set up new picture.
		$xSize = imagesx( $picture );
		$ySize = imagesy( $picture );
		$newPicture = imagecreatetruecolor( $xSize, $ySize );
		imagesavealpha( $newPicture, true );
		imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

		// Resize mask if necessary.
		if ( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
			$tempPic = imagecreatetruecolor( $xSize, $ySize );
			imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
			imagedestroy( $mask );
			$mask = $tempPic;
		}

		// Perform pixel-based alpha map application.
		for ( $x = 0; $x < $xSize; $x++ ) {
			for ( $y = 0; $y < $ySize; $y++ ) {
				$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
				$alpha = $alpha['alpha'];
				$color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
				// Preserve alpha by comparing the two values.
				if ( $color['alpha'] > $alpha ) {
					$alpha = $color['alpha'];
				}
				// Kill data for fully transparent pixels.
				if ( 127 == $alpha ) {
					$color['red'] = 0;
					$color['blue'] = 0;
					$color['green'] = 0;
				}
				imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color['red'], $color['green'], $color['blue'], $alpha ) );
			}
		}

		// Copy back to original picture.
		imagedestroy( $picture );
		$picture = $newPicture;
	}



	/**
	 * Get all business categories in terms of id => title.
	 */
	public static function get_business_categories_dropdown_format( $empty_init = false, $order=1 ) {

		$taxonomies = array();

		if ( $empty_init )
			$taxonomies[''] = '';

		$taxonomy = 'wyz_business_category';
		$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
		$length = count( $tax_terms );

		if($order==1)
			foreach ($tax_terms as $tax) {
				$taxonomies[ $tax->name ] = ''.$tax->term_id;
			}
		elseif($order==2){
			foreach ($tax_terms as $tax) {
				$taxonomies[ ''.$tax->term_id ] = $tax->name;
			}
		}

		return $taxonomies;
	}



	/**
	 * Get all locations in terms of id => title.
	 */
	public static function get_business_locations_dropdown_format( $empty_init = false ) {

		$locations = array();
		if ( $empty_init )
			$locations[''] = '';

		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);
		$posts_array = get_posts( $qry_args );
		foreach ($posts_array as $post) {
			$locations[ $post->post_title ] = ''.$post->ID;
		}

		return $locations;
	}




	/**
	 * Get all business categories.
	 */
	public static function get_business_categories() {
		if ( method_exists( 'WyzHelpersOverride', 'get_business_categories') ) {
			return WyzHelpersOverride::get_business_categories();
		}

		$taxonomies = array();
		$taxonomy = 'wyz_business_category';
		$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
		$length = count( $tax_terms );
		for ( $i = 0; $i < $length; $i++ ) {
			if ( ! isset( $tax_terms[ $i ] ) ) {
				continue;
			}
			$temp_tax = array();
			$obj = $tax_terms[ $i ];
			if ( 0 == $obj->parent ) {
				$temp_tax['id'] = $obj->term_id;
				$temp_tax['name'] = $obj->name;
				$temp_tax['children'] = array();
				$temp_child = array();
				for ( $j = 0; $j < $length; $j++ ) {
					if ( ! isset( $tax_terms[ $j ] ) ) {
						continue;
					}
					$tmp = $tax_terms[ $j ];
					if ( $tmp->parent == $obj->term_id ) {
						$temp_child['id'] = $tmp->term_id;
						$temp_child['name'] = $tmp->name;
						$temp_tax['children'][] = $temp_child;
						unset( $tax_terms[ $j ] );
					}
				}
				$taxonomies[] = $temp_tax;
				unset( $tax_terms[ $i ] );
			}
		}
		return $taxonomies;
	}

	/**
	 * Display business category dropdown filter.
	 */
	public static function wyz_business_category_filter() {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_business_category_filter') ) {
			return WyzHelpersOverride::wyz_business_category_filter();
		}

		ob_start();

		$taxonomies = self::get_business_categories();
		$sector = get_queried_object()->name;

		$len = count( $taxonomies );?>
		<div id="cat-filter-mobile-trigger" class="filter-mobile-trigger wyz-primary-color wyz-prim-color">
			<i class="fa fa-search"></i>
		</div>
		<select id="wyz-cat-filter" class="wyz-input wyz-select">
			<option value=""><?php esc_html_e( 'categories', 'wyzi-business-finder' );?>....</option>
			<?php for ( $i = 0; $i < $len; $i++ ) {
				$img = wp_get_attachment_url( get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_icon_upload', true ) );
				$url = get_term_link( $taxonomies[ $i ]['id'], 'wyz_business_category' );
				$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
				echo '<option '. ( $taxonomies[ $i ]['name'] == $sector ? 'selected ' : '' ) . 'value="' . esc_url( $url ) . '" ' . ( false != $img ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:' . esc_attr( $bgc ) . ';\' ' : '' ) .'><img src=\'' . $img . '\'/></div>"' : '') .' data-right=\'' . esc_url( $url ) . '\' >&nbsp;' . $taxonomies[ $i ]['name'] . '</option>';
				if ( isset( $taxonomies[ $i ]['children'] ) && ! empty( $taxonomies[ $i ]['children'] ) ) {
					foreach ( $taxonomies[ $i ]['children'] as $chld ) {
						$url = get_term_link( $chld['id'], 'wyz_business_category' );
						echo '<option ' . ( $chld['name'] == $sector ? 'selected ' : '' ) . 'value="' . esc_url( $url ) . '" data-right=\'' . esc_url( $url ) . '\'>&nbsp;&nbsp;&nbsp;' . $chld['name'] . '</option>';
					}
				}
			}?>

		</select>
		<?php  echo ob_get_clean();
	}
	

	/**
	 * Display locations dropdown filter.
	 */
	public static function wyz_locations_filter( $is_map, $last = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_locations_filter') ) {
			return WyzHelpersOverride::wyz_locations_filter( $is_map );
		}

		$filter_type = '';
		if ( $is_map )
			$filter_type = get_post_meta( get_the_ID(), 'wyz_map_location_filter_type', true );
		/*else
			$filter_type = get_post_meta( get_the_ID(), 'wyz_image_location_filter_type', true );*/


		if ( '' == $filter_type )
			$filter_type = 'dropdown';

		if( 'text' == $filter_type ) {
			?>
			<div class="bus-filter input-box input-location<?php echo ( $last ? ' last' : '' );?>">
				<input type="text" name="wyz-loc-filter-txt" id="wyz-loc-filter-txt" placeholder="<?php echo esc_html__( 'location', 'wyzi-business-finder' ) . '...';?>"/>
				<input type="hidden" id="loc-filter-txt" name="loc-filter-txt" />
				<input type="hidden" id="loc-filter-lat" name="loc-filter-lat" />
				<input type="hidden" id="loc-filter-lon" name="loc-filter-lng" />
			</div>
			<?php
			return;
		}


		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);

		$location = isset( $_GET['location'] ) ? intval( $_GET['location'] ) : 0;

		$def_image = plugins_url( 'img/default-location.png', __FILE__ );

		$all_posts = new WP_Query( $qry_args );?>

		<div class="bus-filter input-box input-location map-locations<?php echo ( $last ? ' last' : '' );?>">

		<select id="wyz-loc-filter" name="location" class="wyz-input wyz-select">
			<option value=""><?php esc_html_e( 'location', 'wyzi-business-finder' );?>...</option>

		<?php 
		$def_loc_id = get_post_meta( get_the_ID(), 'wyz_def_image_location', true );
		if ( $def_loc_id == '' || $def_loc_id < 1 )
			$def_loc_id = -1;

		$selected =  get_post_meta( get_the_ID(), 'wyz_def_map_location', true );
		$def_loc_id = ( -1 == $selected ) ? $def_loc_id : $selected;

		while ( $all_posts->have_posts() ) {
			$all_posts->the_post();
			$l_id = get_the_ID();
			if ( has_post_thumbnail() ) {
				$img = get_the_post_thumbnail();
			} else {
				$img = '<img src="' . $def_image . '"/>';
			}
			if ( $is_map ) {

				$coor = get_post_meta( $l_id, 'wyz_location_coordinates', true );
				if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
				echo '<option value=\'{"id":"'.$l_id.'","lat":"'. $coor['latitude'] .'","lon":"'. $coor['longitude'] .'"}\' ' . ( ( $location == $l_id || $def_loc_id == $l_id ) ? 'selected' : '' ) . ' data-left=\'' . $img . '\'>' . get_the_title() . '</option>';
			} else {
				echo '<option value="'.$l_id.'" ' . ( ( $location == $l_id || $def_loc_id == $l_id ) ? 'selected' : '' ) . ' data-left=\'' . $img . '\'>' . get_the_title() . '</option>';
			}
		}?>
		</select>

		</div>

		<?php

		wp_reset_postdata();
	}
	


	/**
	 * Display Business Categories dropdown filter.
	 */
	public static function wyz_categories_filter( $taxonomies, $last = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_categories_filter') ) {
			return WyzHelpersOverride::wyz_categories_filter( $taxonomies );
		}

		$len = count( $taxonomies );
		$category = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
		$selected =  get_post_meta( get_the_ID(), 'wyz_default_map_category', true );
		$category = empty( $selected ) ? $category : $selected;
		?>
		<div class="bus-filter input-location input-box<?php echo ( $last ? ' last' : '' );?>">
		<select id="wyz-cat-filter" name="category" class="wyz-input wyz-select">
			<option value=""><?php esc_html_e( 'category', 'wyzi-business-finder' );?>...</option>
			<?php for ( $i = 0; $i < $len; $i++ ) {
				$url = wp_get_attachment_image_src(  get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_icon_upload', true ), 'thumbnail', true )[0];
				//wp_get_attachment_url( get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_icon_upload', true ) );
				$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
				echo '<option value="'.$taxonomies[ $i ]['id'].'" ' . ( $category == $taxonomies[ $i ]['id'] ? 'selected ' : '' ) . ( false != $url ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:'.$bgc.';\' ' : '' ) .'><img src=\''.$url.'\'/></div>"' : '') . ' >&nbsp;'.$taxonomies[$i]['name'].'</option>';
				if ( isset( $taxonomies[ $i ]['children'] ) && ! empty( $taxonomies[ $i ]['children'] ) ) {
					foreach ( $taxonomies[ $i ]['children'] as $chld ) {
						echo '<option ' . ( $category == $chld['id'] ? 'selected ' : '' ) . 'value="' . $chld['id'] . '">&nbsp;&nbsp;&nbsp;' . $chld['name'] . '</option>';
					}
				}
			}?>
		</select>
		</div>
		<?php
	}
	


	public static function wyz_get_business_filters( $inputs = array(), $echo = true ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_get_business_filters') ) {
			return WyzHelpersOverride::wyz_get_business_filters( $inputs );
		}
		
		if ( empty( $inputs) ) $inputs = array('1','2','3','4');
		$keyword = '';
		$days_get = array();
		if ( isset( $_GET['keyword'] ) ) {
			$keyword = $_GET['keyword'];
		}
		if ( isset( $_GET['open_days'] ) ) {
			$days_get = $_GET['open_days'];
		}

		add_action( 'wp_footer', function(){
			$url = plugin_dir_url( __FILE__ );
			wp_enqueue_script( 'jQuery_tags_select', $url . 'js/selectize.min.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'business_archives_js', $url . 'js/archives.js', array( 'jQuery_tags_select' ), false, true );
			wp_localize_script( 'business_archives_js', 'WyzLocFilter', array( 'filterType' => ( 'text' == get_post_meta( get_the_ID(), 'wyz_image_location_filter_type', true ) ? 'text' : 'dropdown' ) ) );
			wp_enqueue_style( 'jQuery_tags_select_style', $url . 'css/selectize.default.css' );
		}, 10 );

		$filter_count = count( $inputs );

		if ( ! $echo )
			ob_start();
		?>

		<div class="location-search<?php echo " filter-count-$filter_count";?> filter-location-search">
			<form method="GET" action="<?php echo get_post_type_archive_link( 'wyz_business' );?>">
				
				<?php 
				$i =0;
				foreach( $inputs as $input ) {
					$i++;
					switch ($input) {
						case 1:
							self::keyword_filter( $keyword, $i == $filter_count );
							break;
						case 2:
							self::wyz_locations_filter( false, $i == $filter_count );
							break;
						case 3:
							self::wyz_categories_filter( self::get_business_categories(), $i == $filter_count );
							break;
						case 4:
							self::days_filter( $days_get, $i == $filter_count );
							break;
					}
				} ?>
				
				<div class="input-submit">
					<input type="submit" class="wyz-primary-color wyz-secon-color wyz-prim-color-hover wyz-secon-color" id="map-search-submit" value="<?php esc_html_e( 'Search', 'wyzi-business-finder' );?>"/>
				</div>
			</form>
		</div>
		<?php
		if ( ! $echo )
			return ob_get_clean();
	}
	


	private static function keyword_filter( $keyword, $last = false ) {
		?>
		<div class="bus-filter input-box input-keyword input-location<?php echo ( $last ? ' last' : '' );?>"><input name="keyword" type="text" id="search-keyword" placeholder="<?php esc_html_e( 'Keyword', 'wyzi-business-finder' );?>" value="<?php echo $keyword;?>"></div>
		<?php
	}


	private static function days_filter( $days_get, $last = false ) {
		?>
		<div class="bus-filter input-keyword input-days input-box input-location<?php echo ( $last ? ' last' : '' );?>">
			<?php $days = array( 
				'mon' => esc_html__( 'Monday', 'wyzi-business-finder' ),
				'tue' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
				'wed' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
				'thur' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
				'fri' => esc_html__( 'Friday', 'wyzi-business-finder' ),
				'sat' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
				'sun' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
			); ?>
			<select multiple name="open_days[]" id="wyz-day-filter" data-selectator-keep-open="true" placeholder="<?php esc_html_e( 'Open Days', 'wyzi-business-finder' );?>">
				<?php
				foreach ( $days as $key => $value ) {
					echo '<option value="' . $key . '"';
					if ( ! empty( $days_get ) && in_array( $key, $days_get ) ) {
						echo ' selected="selected"';
					}
					echo  '>'. $value . '</option>';
				}
				?>
			</select>
			<div class="tagchecklist hide-if-no-js"></div>
		</div>
		<?php
	}


	public static function wyz_get_representative_business_category_id( $business_id ) {
		$cat_icon_id = get_post_meta( $business_id, 'wyz_business_category_icon', true );
		
		if ( '' != $cat_icon_id && wp_get_attachment_url( get_term_meta( $cat_icon_id, 'wyz_business_icon_upload', true ) ) ) {
			return $cat_icon_id;
		}

		$tmp_cats = get_the_terms( $business_id, 'wyz_business_category' );
		if ( ! $tmp_cats || is_wp_error( $tmp_cats ) ) {
			return false;
		}

		foreach ($tmp_cats as $tmp_cat) {
			if ( 0 == $tmp_cat->parent ) {
				return $tmp_cat->term_id;
			}
			$parent_cat = get_term( $tmp_cat->parent, 'wyz_business_category' );
			if ( ! is_wp_error( $parent_cat ) ) {
				$icon = get_term_meta( $parent_cat->term_id, 'wyz_business_icon_upload', true );
				if ( '' != $icon ) {
					return $parent_cat->term_id;
				}
			}
		}
	}


	public static function get_image( $id ) {
		
		$attachments = get_post_meta( $id, 'business_gallery_image', true );
		$temp = '';
		if ( $attachments && ! empty( $attachments ) ) {
			if ( ! is_array( $attachments ) ) {
				$temp = wp_get_attachment_image_src( $this->attachments, 'full' );
				$temp = $temp[0];
			} else {
				foreach ( $attachments as $attachment ) {
					$temp = wp_get_attachment_image_src( $attachment, 'full' );
					if ( '' != $temp ) {
						$temp = $temp[0];
						break;
					}
				}
			}
		}
		if( empty( $temp ) ){
				$temp = apply_filters( 'wyz_default_business_logo_path', plugin_dir_url( __FILE__ ) . 'img/featured_default_image.png', $id );
		}
		return $temp;
	}


	/**
	 * Check if a user has a draft business
	 *
	 * @return boolean If the user has a business, draft
	 * @param integer $user_id user id.
	 */
	public static function wyz_user_has_draft_business( $user_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_business',
			'posts_per_page' => '1',
			'author' => $user_id,
			'post_status' => array( 'draft' ),
		) );
		$id = false;
		if ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
		}
		wp_reset_postdata();
		return $id;
	}


	/**
	 * Check if a user has a draft offer
	 *
	 * @return boolean If the user has an offer, draft
	 * @param integer $user_id user id.
	 */
	public static function wyz_user_has_draft_offer( $user_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => '1',
			'author' => $user_id,
			'post_status' => array( 'draft' ),
		) );
		$id = false;
		if ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
		}
		wp_reset_postdata();
		return $id;
	}


	/**
	 * Gets all available locations as an array ( ID => Name).
	 */
	public static function get_businesses_locations_options() {
		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'posts_per_page' => - 1,
		);

		$all_posts = new WP_Query( $qry_args );
		$locs = array();
		$locs[''] = '';
		while ( $all_posts->have_posts() ) {
			$all_posts->the_post();
			$locs[ get_the_ID() ] = get_the_title();
		}
		wp_reset_postdata();
		return $locs;
	}


	/**
	 * Map/Image search handler
	 */
	public static function wyz_handle_business_search( $keywords, $cat_id, $loc_id, $rad, $lat, $lon, $page ) {

		$loc_radius_search = false;
		$bus_names = $keywords;

		if ( ! $rad || '' == $rad || ! is_numeric( $rad ) ) {
			$rad = 0;
			$lat = $lon = 0;
		}
		elseif ( ! $lat || ! $lon || '' == $lat || '' == $lon || ( ! is_float( $lat ) && ! is_float( $lon ) && ! is_numeric( $lat ) && ! is_numeric( $lon ) ) ) {
			$lat = $lon = 0;
		}

		//if we have radius search,and country search, search by the radius with respect to location
		if ( 0 != $rad && '' != $loc_id && '0' < $loc_id ) {
			$loc_coor = get_post_meta( $loc_id, 'wyz_location_coordinates', true );
			if ( ! empty( $loc_coor ) && ! empty( $loc_coor['latitude'] )  && ! empty( $loc_coor['longitude'] ) ) {
				$lat = $loc_coor['latitude'];
				$lon = $loc_coor['longitude'];
				$loc_radius_search = true;
			}
		}

		if ( '' != $bus_names )
			$bus_names_arr = explode( ' ', $bus_names );
		else
			$bus_names_arr = array();

		$meta_query = '';
		

		if ( '' != $loc_id && '0' < $loc_id && ! $loc_radius_search ) {
			$meta_query = array( // Include excerpt and slogan in global map search.
				'relation' => 'AND',
				array( 'key' => 'wyz_business_country', 'value' => $loc_id ),
				array( 
					'relation' => 'OR',
					array( 'key' => 'wyz_business_excerpt', 'value' => $bus_names, 'compare' => 'LIKE' ),
					array( 'key' => 'wyz_business_slogan', 'value' => $bus_names, 'compare' => 'LIKE' ),
				),
			);
		} elseif( ! empty( $bus_names ) ) {
			$meta_query = array( // Include excerpt and slogan in global map search.
				'relation' => 'OR',
				array( 'key' => 'wyz_business_excerpt', 'value' => $bus_names, 'compare' => 'LIKE' ),
				array( 'key' => 'wyz_business_slogan', 'value' => $bus_names, 'compare' => 'LIKE' ),
			);
		}

		$args = array(
			'post_type' => 'wyz_business',
			'posts_per_page' => '400',
			'offset' => $page,
			'post_status' => array( 'publish' ),
		);

		if ( ! empty( $bus_names_arr ) ) {
			$tax_query = array(
				array(
					'taxonomy' => 'wyz_business_tag',
					'field'    => 'name',
					'terms' => $bus_names_arr,
				),
			);
			if ( '' !== $cat_id && 0 < $cat_id ){
				$args['cat_query'] = $cat_id;
			}
		} elseif ( '' !== $cat_id && 0 < $cat_id ) {
			$tax_query = array(
				array(
					'taxonomy' => 'wyz_business_category',
					'field'    => 'term_id',
					'terms' => $cat_id,
				),
			);
		}
		


		if ( '' != $meta_query ) {
			$args['meta_query'] = $meta_query;
		}

		if ( ! empty( $bus_names_arr ) ) {

			$args['_meta_or_title'] = $bus_names_arr;
			$args['my_tax_query'] = $tax_query;
			$args['_meta_or_tax'] = true;
		} elseif ( isset( $tax_query ) && ! empty( $tax_query) ) {
			$args['tax_query'] = $tax_query;
		}

		//$the_query = WyzHelpers::query_businesses( $args );

		return array(
			'query' => $args,
			'lat' => $lat,
			'lon' => $lon,
		);
	}


	/**
	 * Check if a user has a business
	 *
	 * @return boolean If the user has a business or not.
	 * @param integer $user_id user id.
	 */
	public static function wyz_has_business( $user_id ) {

		$user_businesses = get_user_meta( $user_id, 'wyz_user_businesses', true );

		if ( empty( $user_businesses ) || ( ! isset( $user_businesses['pending'] ) && ! isset( $user_businesses['published'] ) ) || ( empty( $user_businesses['pending'] ) && empty( $user_businesses['published'] ) ) )
			return false;
		return true;
	}



	public static function user_owns_business( $business_id, $user_id ) {
		$business = get_post( $business_id );
		return $user_id == $business->post_author;
	}

	public static function add_extra_points( $user_id ) {
		if ( user_can( $user_id, 'publish_businesses' ) ) {
			if ( true == get_user_meta( $user_id, 'extra_points_added', true ) )return;
			$points = intval( get_option( 'wyz_add_points_registration', 0 ) );
			if ( $points ) {
				$available = get_user_meta( $user_id, 'points_available', true );
				if ( '' == $available ) $available = 0;
				$available = intval( $available );
				$available += $points;
				update_user_meta( $user_id, 'points_available', $available );
				update_user_meta( $user_id, 'extra_points_added', true );
			}
		}
	}


	/**
	 * Get count of all user's businesses
	 *
	 * @return int total number of user businesses
	 * @param integer $user_id user id.
	 */
	public static function get_user_businesses_count( $user_id  ) {
		$user_businesses = self::get_user_businesses( $user_id );
		$count = 0;
		foreach ( $user_businesses['pending'] as $business ) {
			$count++;
		}
		foreach ( $user_businesses['published'] as $business ) {
			$count++;
		}
		return $count;
	}


	/**
	 * Check if a user has enough points to register a business.
	 *
	 * @return boolean If the user has enough points to register a business.
	 * @param integer $user_id user id.
	 */
	public static function wyz_current_user_affords_business_registry() {

		$user_id = get_current_user_id();
		$points_available = get_user_meta( $user_id, 'points_available', true );
		if ( '' == $points_available ) {
			$points_available = 0;
		} else {
			$points_available = intval( $points_available );
		}
		$registery_price = get_option( 'wyz_businesses_registery_price' );
		if ( '' == $registery_price ) {
			$registery_price = 0;
		} else {
			$registery_price = intval( $registery_price );
			if ( $registery_price < 0 ){
				$registery_price = 0;
			}
		}
		return $points_available >= $registery_price;
	}


	/**
	 * Check if a user has enough points to register a job.
	 *
	 * @return boolean If the user has enough points to register a job.
	 * @param integer $user_id user id.
	 */
	public static function current_user_affords_job_registry( $user_id = 0 ) {

		if ( ! $user_id ) $user_id = get_current_user_id();
		$points_available = get_user_meta( $user_id, 'points_available', true );
		if ( '' == $points_available ) {
			$points_available = 0;
		} else {
			$points_available = intval( $points_available );
		}
		$registery_price = intval( get_option( 'wyz_job_submit_cost', 0 ) );

		return $points_available >= $registery_price;
	}


	/**
	 * Check if current user can rate.
	 * A user can rate if he is logged in, hasn't rated current business yet and is not the business owner.
	 *
	 * @return boolean If the user can rate or not.
	 */
	 public static function wyz_can_user_rate() {
		$can_rate = true;

		global $current_user;
		wp_get_current_user();
		$logged_in_user = is_user_logged_in();
		$id = get_the_ID();

		if ( $logged_in_user ) {
			$rates = get_post_meta( $id, 'wyz_business_rates', true );
			if ( WyzHelpers::wyz_is_current_user_author( $id ) ) {
				return false;
			}
			if ( is_array( $rates )  ) {
				foreach ( $rates as $key => $value ) {
					if ( $key == $current_user->ID ) {
						return false;
					}
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Get image id from image url
	 *
	 * @param string $image_url the image url.
	 */
	public static function wyz_get_image_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		return $attachment[0];
	}


	/**
	 * Send email.
	 *
	 * @param string $to email addresses to send message to.
	 * @param string $subject email subject.
	 * @param string $message message content.
	  */
	public static function wyz_mail( $to, $subject, $message, $type = '' ) {
		$allowed_html = array(
			'a' => array(
				'href' => true,
				'title' => true,
			),
			'br' => array(),
			'abbr' => array(
				'title' => true,
			),
			'acronym' => array(
				'title' => true,
			),
			'b' => array(),
				'blockquote' => array(
				'cite' => true,
			),
			'cite' => array(),
			'code' => array(),
			'del' => array(
				'datetime' => true,
			),
			'em' => array(),
			'i' => array(),
			'q' => array(
				'cite' => true,
			),
			'strike' => array(),
			'strong' => array(),
		);
		$message = wp_kses( $message, $allowed_html );
		$from = get_option( 'wyz_businesses_from_email' );
		$subject  = esc_html( get_bloginfo( 'name' ) ) . ' ' . $subject;
		$semi_rand = md5( time() );
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers = 'From: ' . esc_html( get_bloginfo( 'name' ) ) . ' <' . $from . '>';
		$headers .= "\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=UTF-8;\n" . " boundary=\"{$mime_boundary}\"";

		if ( '' != $type ) {
			$subject = apply_filters( "wyzi-email-$type-subject", $subject, $to );
		}
		return wp_mail( $to, $subject, $message, $headers );
	}


	/**
	 * Get current business author id.
	 */
	public static function wyz_the_business_author_id( $post_id = false ) {
		if ( ! $post_id ) 
			global $post;
		else
			$post = get_post( $post_id );

		if ( null == $post || ! isset( $post ) ) {
			return 0;
		}
		return $post->post_author;
	}

	/**
	 * Check Subscription Capability.
	 *
	 * @param string $user_id to check his capbabilities.
	 * @param string $extra_option is the option to check.
	  */
	public static function wyz_sub_can_bus_owner_do( $user_id, $extra_option ) {

		if ( 'off' == get_option( 'wyz_sub_mode_on_off', 'off') ||
				user_can( $user_id, 'manage_options' ) ||
				! function_exists( 'pmpro_getMembershipLevelForUser' ) ) {

			return true;
		}
		
		$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');

		if ( 'not_found' == $wyzi_subscription_options ) {

			return true;
		}

		$membership_level = pmpro_getMembershipLevelForUser( $user_id );

		if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
			|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ $extra_option ] )
			|| empty( $wyzi_subscription_options[ $membership_level->id ][ $extra_option ] ) ) {

			return false;
		}

		return $wyzi_subscription_options[$membership_level->id][$extra_option];
		
	}


	/**
	 * Check if subscriber can create a business
	 *
	 * @param string $user_id to check his capbabilities.
	 */
	public static function user_can_create_business( $user_id ) {

		if ( user_can( $user_id, 'manage_options' ) )
			return true;

		if ( ! self::wyz_current_user_affords_business_registry() )
			return false;

		$count = self::get_user_businesses_count( $user_id );

		if ( 'on' == get_option( 'wyz_sub_mode_on_off', 'off' ) ) {


			$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');
			$membership_level = pmpro_getMembershipLevelForUser( $user_id );

			if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
				|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] )
				|| empty( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] ) ) {

				return false;
			}

			if ( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] )

			$max = isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_businesses' ] ) ? $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_businesses' ] : 1;

			return $count < $max;
		}

		$max = intval( get_option( 'wyz_max_allowed_businesses', 1) );

		
		return $count < $max;
	}


	/**
	 * Check if subscriber can create a Job
	 *
	 * @param string $user_id to check his capbabilities.
	 */
	public static function user_can_create_job( $user_id ) {

		if ( 'on' != get_option( 'wyz_users_can_job' ) ) return false;

		if ( user_can( $user_id, 'manage_options' ) )
			return true;

		if ( 'on' == get_option( 'wyz_sub_mode_on_off', 'off' ) ) {

			$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');
			$membership_level = pmpro_getMembershipLevelForUser( $user_id );

			if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
				|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] )
				|| empty( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] ) ) {

				return false;
			}

			$cost = intval( get_option( 'wyz_job_submit_cost', 0 ) );

			if ( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] ) {
				if ( self::user_exceeded_max_jobs( $user_id, $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_jobs' ] ) )
					return false;
			}
			return self::current_user_affords_job_registry( $user_id );
		}

		if ( self::user_exceeded_max_jobs( $user_id ) ) return false;

		return self::current_user_affords_job_registry( $user_id );
	}


	public static function user_exceeded_max_jobs( $user_id, $max_per_business = -1 ) {
		if ( -1 == $max_per_business )
			$max_per_business = intval( get_option( 'wyz_jobs_max', 0 ) );

		if ( $max_per_business < 1 ) return false;

		$args = array(
			'post_type' => 'job_listing',
			'posts_per_page' => -1,
			'post_status' => array( 'publish', 'pending' ),
			'fields' => 'ids',
			'author' => $user_id
		);
		$query = new WP_Query( $args );

		return $max_per_business <= intval( $query->post_count );
	}
}
