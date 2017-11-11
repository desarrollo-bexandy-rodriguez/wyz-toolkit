<?php
class WyzSingleBusinessData{

	//business info
	private $author_id;
	private $business_id;
	private $is_user_author;
	private $responsive;
	private $sidebar_responsive;

	/*permissions*/
	private $can_map;
	private $header_content;
	private $can_wall;
	private $can_photo;
	private $can_about;
	private $can_offers;
	private $can_message;
	private $can_products;
	public $can_bookings;
	private $can_jobs;
	private $can_ratings;
	private $can_additional;

	//data
	private $has_custom;
	private $gallery_data;
	public $attachments;
	private $custom_form_data;
	private $custom_field_title;
	private $products_query;
	private $tab_data;

	//queries
	private $jobs_query;

	private $template_type;
	private $business_path;
	private $business_url;

	//flags
	public $have_posts;
	public $have_images;
	public $have_ratings;
	public $can_rate;

	public function __construct( $template_type, $business_path, $business_url ) {
		if ( filter_input( INPUT_POST, 'delete-bus-gal' ) ) {
			unset( $_POST['delete-bus-gal'] );
			delete_post_meta( get_the_ID(), 'business_gallery_image' );
		}
		$this->author_id = WyzHelpers::wyz_the_business_author_id();
		$this->business_id = get_the_ID();
		$this->template_type = $template_type;
		$this->responsive = 1 == $this->template_type ? ( 'on' === wyz_get_option( 'resp' ) ? 'col-lg-8 col-md-7 col-xs-12' : 'col-xs-8') : '';
		$this->sidebar_responsive = ( 'on' === wyz_get_option( 'resp' ) ? 'col-lg-4 col-md-5 col-xs-12' : 'col-xs-4');
		$this->is_user_author = WyzHelpers::wyz_is_current_user_author( $this->business_id );
		$this->tab_data = get_option( 'wyz_business_tabs_order_data' );
		$this->business_path = $business_path;
		$this->business_url = $business_url;
		$this->initialize_permissions();
		$this->evaluate_have_posts();
		$this->evaluate_ratings();
		$this->evaluate_attachments();
		$this->evaluate_have_images();

		$count = count( $this->tab_data );
		for ( $i=0; $i<$count; $i++ ) {
			if ( ! isset( $this->tab_data[ $i ]['urlid'] ) || '' == $this->tab_data[ $i ]['urlid'] )
				$this->tab_data[ $i ]['urlid'] = urlencode( $this->tab_data[ $i ]['type'] );
		}
		
		$this->check_for_no_active();
	}

	private function initialize_permissions() {

		$this->can_map = ( 'on' != get_option( 'wyz_business_map_hide_in_single_bus' ) ) && WyzHelpers::wyz_sub_can_bus_owner_do( $this->author_id,'wyzi_sub_business_show_map');

		$this->header_content = get_option( 'wyz_business_header_content' );
		if ( '' == $this->header_content ) $this->header_content = 'map';

		$this->can_wall = WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_wall_tab');

		$this->can_photo = WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_photo_tab');

		$this->can_about = WyzHelpers::wyz_sub_can_bus_owner_do( $this->author_id,'wyzi_sub_business_show_description');

		$this->can_message = WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_message_tab');

		$this->can_products = class_exists( 'WooCommerce' ) && class_exists( 'WCMp' ) && ( ( function_exists( 'is_user_wcmp_vendor' ) && is_user_wcmp_vendor( $this->author_id ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_products_tab') ) || user_can( $this->author_id, 'manage_options' ) );

		$calendars = get_user_meta( $this->author_id, 'wyz_business_calendars', true );
		$this->can_bookings = 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' ) && isset( $calendars[ $this->business_id ] );


		$this->can_jobs = 'on' == get_option( 'wyz_users_can_job' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_can_create_job') && class_exists( 'WP_Job_Manager' );
		if ( $this->can_jobs ) {
			$this->jobs_query = new WP_Query(array(
				'post_type' => 'job_listing',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'post_author' => $this->author_id,
				'meta_query' => array(
					array(
						'key' => '_wyz_job_listing',
						'value' => $this->business_id
					)
				)
			));

			$this->can_jobs = $this->jobs_query->have_posts() && $this->can_jobs;
		}


		$this->can_offers = WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_offers_tab');

		$this->can_ratings = WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_ratings_tab');
		$this->can_additional = true;//WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_show_additional_tab');

		$this->custom_form_data = get_option( 'wyz_business_custom_form_data', array() );
		$this->has_custom = false;
		if ( WyzHelpers::wyz_sub_can_bus_owner_do( $this->author_id,'wyzi_sub_business_can_custom_fields' ) ) {
			if ( ! empty( $this->custom_form_data ) ) {
				foreach ( $this->custom_form_data as $key => $value ) {
					if ( true == $value['visible'] ){
						$this->has_custom = true;
						break;
					}
				}
			}
		}
		$this->custom_field_title = get_option( 'wyz_custom_fields_tab_title', 'Custom Fields' );
	}

	public function the_business_map() {
		if ( $this->can_map ) {
			if ( $this->header_content == 'map' ) {
				if ( $this->template_type == 1 ) {
					WyzMap::wyz_the_business_map( $this->business_id, true );
				} else {
					WyzMap::listing_single_business_map( $this->business_id );
				}
			} elseif ( $this->header_content == 'image' ) {
				WyzMap::wyz_get_business_header_image( $this->business_id );
			}
		}
	}

	public function the_tabs() {
		if ( empty( $this->tab_data ) ) {
			return;
		}
		foreach ( $this->tab_data as $tab ) {
			$this->get_tab( $tab );
		}
	}

	public function the_tabs_content() {
		if ( empty( $this->tab_data ) ) {
			return;
		}
		foreach ( $this->tab_data as $tab ) {
			$this->get_tab_content( $tab );
		}
	}

	private function get_tab( $tab ) {
		$condition;
		switch ( $tab['type'] ) {
			case 'wall':
				$condition = $this->can_wall;
				$tab['fa'] = 'info-circle';
				break;
			case 'photo':
				$condition = $this->can_photo;
				$tab['fa'] = 'file-image-o';
				break;
			case 'about':
				$condition = $this->can_about;
				$tab['fa'] = 'question-circle-o';
				break;
			case 'offers':
				$condition = $this->can_offers;
				$tab['fa'] = 'gift';
				break;
			case 'message':
				$condition = $this->can_message;
				$tab['fa'] = 'envelope-o';
				break;
			case 'products':
				$condition = $this->can_products;
				$tab['fa'] = 'cubes';
				break;
			case 'bookings':
				$condition = $this->can_bookings;
				$tab['fa'] = 'calendar';
				break;
			case 'jobs':
				$condition = $this->can_jobs;
				$tab['fa'] = 'briefcase';
				break;
			case 'ratings':
				$condition = $this->can_ratings;
				$tab['fa'] = 'star-half-o';
				break;
			case 'customs':
				$condition = $this->has_custom;
				$tab['fa'] = 'plus-circle';
				break;
			case 'additionalContent':
				$condition = $this->can_additional;
				$tab['fa'] = 'plus-circle';
				break;
		}
		$this->the_tab( $condition, $tab );
	}

	public function get_tab_content( $tab ) {
		$type = $tab['type'];
		$urlid = $tab['urlid'];	
		$active = ( isset( $tab['active'] ) && $tab['active'])?'active ':'';
		switch ( $type ) {
			case 'wall':
				$this->the_wall($active,$urlid);
				break;
			case 'photo':
				$this->the_photo($active,$urlid);
				break;
			case 'about':
				$this->the_about($active,$urlid);
				break;
			case 'offers':
				$this->the_offers($active,$urlid);
				break;
			case 'message':
				$this->the_contact($active,$urlid);
				break;
			case 'products':
				$this->the_products($active,$urlid);
				break;
			case 'bookings':
				$this->the_bookings($active,$urlid);
				break;
			case 'jobs':
				$this->the_jobs($active,$urlid);
				break;
			case 'ratings':
				$this->the_ratings($active,$urlid);
				break;
			case 'customs':
				$this->the_custom($active,$urlid);
				break;
			case 'additionalContent':
				$content = isset( $tab['additionalContent'] ) ? $tab['additionalContent'] : '';
				$this->the_additional($active,$urlid, $content);
				break;
		}
	}

	private function the_tab( $condition, $tab ) {
		if ( $condition ) { ?>
			<li class="<?php echo ( isset( $tab['active'] ) && $tab['active']?'active ':'' ). $tab['type'].' '.$tab['cssClass']; ?>"><a id="<?php echo $tab['type'];?>-btn" class="business-tab wyz-secondary-color-text-hover wyz-prim-color-txt-hover" href="#<?php echo $tab['urlid'];?>" ><span><?php echo esc_html( $tab['label'] );?></span><?php if( 2 == $this->template_type ) echo '<i class="fa fa-' . $tab['fa'] . '"></i>';?></a></li>
			<?php
		}
	}


	private function the_wall($active,$urlid) {
		if ( $this->can_wall ) { 
			if ( $this->template_type == 1 ) {?>
		<!-- Business Tab Wall -->
		<div class="tab-pane <?php echo $active; ?>row" id="<?php echo $urlid;?>">
			<!-- Business Post Area -->
			<div class="business-post-area <?php echo $this->responsive;?>">
				<!-- Create Business Post -->
				<?php if ( $this->is_user_author ) {
					require_once( $this->business_path . 'forms/posting-form.php' );
				 }
				// Case business has no posts.
				if ( ! $this->have_posts ) {
					WyzHelpers::wyz_info( esc_html( get_option( 'wyz_businesses_no_posts' ) ), false, 'id="no-business-posts"' );
					echo '<div id="postswrapper">';
				} else { // Business has posts.
					echo '<div id="postswrapper">';
				}
				echo '</div>';
				echo '<div id="loadmoreajaxloader" class="blog-pagination" style="opacity:0;"><div class="loading-spinner"><div class="dot1 wyz-primary-color wyz-prim-color"></div><div class="dot2 wyz-primary-color wyz-prim-color"></div></div></div>';?>
			</div>
			<?php $this->right_sidebar();?>
		</div>
		<?php
			} elseif ( $this->template_type == 2 ) {?>
		 <div class="tab-pane <?php echo $active; ?>" id="wall">
		 	<?php if ( $this->is_user_author ) {
				require_once( $this->business_path . 'forms/posting-form.php' );
			 }
			 // Case business has no posts.
			if ( ! $this->have_posts ) {
				WyzHelpers::wyz_info( esc_html( get_option( 'wyz_businesses_no_posts' ) ), false, 'id="no-business-posts"' );
				echo '<div id="postswrapper">';
			} else { // Business has posts.
				echo '<div id="postswrapper">';
			}
			echo '</div>';
			//echo '<div id="loadmoreajaxloader" class="blog-pagination" style="opacity:0;"><img src="' . esc_url( $this->business_url . 'images/ajax-loader.gif' ) . '" alt="Load More" /></div>';
			echo '<div id="loadmoreajaxloader" class="blog-pagination" style="opacity:0;"><div class="loading-spinner"><div class="dot1 wyz-primary-color wyz-prim-color"></div><div class="dot2 wyz-primary-color wyz-prim-color"></div></div></div>';?>
		 </div>
			<?php }
		}
	}

	private function the_photo($active, $urlid) {
		if ( $this->can_photo ) { ?>
			<!-- Business Tab Photo -->
			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<div class="busi-bank-photos">
					<?php
					require_once( $this->business_path . 'add-images.php' );
					if ( current_user_can( 'manage_options' ) || $this->is_user_author ) {
						echo wyz_add_images( $this->have_images );
					}
					if ( $this->have_images ) { ?>
						<div class="busi-photos-wrapper">
						<?php $l = count( $this->gallery_data['currentImageAttachedFull'] );
							for ( $i = 0; $i < $l; $i++ ) {
								echo '<div class="col-md-4 col-sm-6 col-xs-12"><a class="sin-photo" href="' . $this->gallery_data['currentImageAttachedFull'][ $i ] . '" data-alt="' . pathinfo( $this->gallery_data['currentImageAttachedFull'][ $i ] )['filename'] . '"><img src="' . $this->gallery_data['currentImageAttachedThumb'][ $i ] . '" width="150" height="150"  /></a></div>';
							}
							?>
						</div>
					<?php
					} else {
						WyzHelpers::wyz_info( esc_html( get_option( 'wyz_businesses_no_images' ) ) );
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	private function the_about($active,$urlid) {
		if ( $this->can_about ) {
			if ( $this->template_type == 1 ) {?>
		<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
			<div class="business-about-area  <?php echo $this->responsive;?>">
				<?php echo wpautop( get_post_meta( $this->business_id, 'wyz_business_description', true ) );?>
			</div>
			<?php $this->right_sidebar();?>
		</div>
		<?php 
			} elseif ( $this->template_type == 2 ) { ?>
		<div class="tab-pane <?php echo $active; ?>" id="about">
			<div class="wall-about-wrapper">
				<?php echo wpautop( get_post_meta( $this->business_id, 'wyz_business_description', true ) );?>
			</div>
		</div>
			<?php }
		}
	}

	private function the_offers($active,$urlid) {

		if ( $this->can_offers ) { 
			/* if ( 'on' === get_option( 'offer_tab_in_business' ) ) {*/?>
			<!-- Business Tab Offers -->
			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<!-- Business available offers -->
				<div class="busi-offers-wrapper">
					<div class="business-offers-area <?php echo $this->responsive;?>">
						<?php WyzOffer::wyz_the_business_all_offers( $this->business_id );?>
					</div>
					<?php $this->template_type == 1 && $this->right_sidebar();?>
				</div>
			</div>
			<?php 
			/*}*/
		}
	}

	private function the_contact($active,$urlid) {
		if ( $this->can_message ) { ?>
			<!-- Business Tab Message -->
			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
			<?php if ( $this->template_type == 1 ) {?>
					<!-- Business Contact -->
					<div class="busi-contact-wrapper">
						<div class="head fix">
							<h3><?php esc_html_e( 'send us a message', 'wyzi-business-finder' );?></h3>
						</div>
						<!-- Business Contact Form -->
						<?php
						echo do_shortcode('[contact-form-7 title="Business Wall Contact Form"]');?>
					</div>
			<?php } elseif( $this->template_type == 2 ) {?>
				<!-- Wall Message Wrapper -->
				<div class="wall-message-wrapper">
					<h3><?php esc_html_e( 'Send us a Message', 'wyzi-business-finder' );?></h3>
					<?php echo do_shortcode('[contact-form-7 title="Business Wall Contact Form 2"]');?>
				</div>
			<?php } 
			echo '</div>';
		}
	}



	private function the_products($active,$urlid) {	 
		if ( $this->can_products ) {?>
			<!-- Business Products -->
			<?php $user_login = get_user_by('id',$this->author_id)->user_login;
			$posts = implode( ',', $this->products_query->posts ); ?>

			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<?php echo do_shortcode( "[wcmp_products ids='$posts']" );?>
			</div>
			<?php
		}
	}

	private function the_bookings($active,$urlid) {	 
		if ( $this->can_bookings ) {?>
			<!-- Business Products -->
			<?php $user_login = get_user_by('id',$this->author_id)->user_login;?>

			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<?php 

				$calendars = get_user_meta( $this->author_id, 'wyz_business_calendars', true );
				echo do_shortcode( '[booked-calendar calendar="'.$calendars[ $this->business_id ].'"]' );?>
			</div>
			<?php
		}
	}

	private function the_jobs($active,$urlid) {
		if ( $this->can_jobs ) {?>
			<!-- Business Products -->
			<?php 
			$user_login = get_user_by('id',$this->author_id)->user_login;?>

			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<?php 

				if ( ! $this->jobs_query->have_posts() ) 
					WyzHelpers::wyz_info( esc_html__( 'No Jobs availabe at this moment.', 'wyzi-business-finder' ) );
				while( $this->jobs_query->have_posts() ) {
					$this->jobs_query->the_post();?>
					<div class="job_summary_shortcode">

					<?php require( WYZI_PLUGIN_DIR . 'job-manager/templates/content-summary-job_listing.php' );?>

					</div>
				<?php }
				?>
			</div>
			<?php
		}
	}
	

	private function the_ratings($active,$urlid) {
		if ( $this->can_ratings ) {?>
			<!-- Business Tab Ratings -->
			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<div class="business-offers-area <?php echo $this->responsive;?>">
					<?php
					if ( $this->can_rate || ! is_user_logged_in() ) {
						require_once( $this->business_path . 'forms/rating-form.php' );
					}

					// Case business has no ratings.
					if ( ! $this->have_ratings ) {
						WyzHelpers::wyz_info( get_option( 'wyz_businesses_no_ratings' ), false, 'id="no-business-ratings"' );
						echo '<div id="ratingswrapper">';
					} else { // Business has ratings.
						echo '<div id="ratingswrapper">';
					}
					echo '</div>';
					//echo '<div id="loadmoreratingsajaxloader" class="blog-pagination" style="opacity:0;"><img src="' . esc_url( $this->business_url . 'images/ajax-loader.gif' ) . '" alt="Load More" /></div>';
					echo '<div id="loadmoreratingsajaxloader" class="blog-pagination" style="opacity:0;"><div class="loading-spinner"><div class="dot1 wyz-primary-color wyz-prim-color"></div><div class="dot2 wyz-primary-color wyz-prim-color"></div></div></div>';?>
				</div>
				<?php if ( $this->template_type == 1 ) {?>
				<!-- Right Sidebar -->
				<div class="sidebar-container <?php echo $this->sidebar_responsive;?>">
					<div class="sin-busi-sidebar">
						<h4 class="sidebar-title"><?php esc_html_e( 'ratings', 'wyzi-business-finder' );?></h4>
						<div class="ratings-sidebar fix">
							<?php $this->rate_form();?>
						</div>
					</div>
				</div>
				<?php } ?>

			</div>
			<?php
		}
	}

	private function the_custom($active,$urlid) {
		if ( $this->has_custom ) {?>
			<!-- Business Tab Customs -->
			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<div class="business-offers-area <?php echo $this->responsive;?>">
				<?php 
				foreach ( $this->custom_form_data as $key => $value ) {
					$the_content_test = get_post_meta( $this->business_id, "wyzi_claim_fields_$key", true );
					if ( isset( $value['visible'] ) && true != $value['visible'] || empty( $the_content_test ) )
						continue;
					if ( ! empty( $value['label'] ) ) {
						echo '<h4>'.$value['label'].'</h4>';
					}
					if ( $value['type'] == 'selectbox' ) {
						$dropdown_data = get_post_meta( $this->business_id, "wyzi_claim_fields_$key", true );
						if ( $value['selecttype'] == 'checkboxes' && ! empty( $value['selecttype'] ) ) {
							echo '<div class="bus-custom-field">';
							foreach ( $dropdown_data as $dat ) {
								foreach( $value['options'] as $option )
									if( $option['value'] == $dat ){
										echo '<div class="bus-custom-multi-check"><span class="fa fa-check-circle"></span> ' . $option['label'] . '</div>';
										break;
									}
							}
							echo '</div>';
						}
						elseif( ! empty( $value['options'] ) ) {
							foreach( $value['options'] as $option )
								if( $option['value'] == $dropdown_data ){
									echo '<div class="bus-custom-multi-check"><span class="fa fa-check-circle"></span> ' . $option['label'] . '</div>';
									break;
								}
						}
							
					} elseif ( $value['type'] == 'file' ) {
						echo '<div class="bus-custom-field">';
						$the_file = get_post_meta( $this->business_id, "wyzi_claim_fields_$key", true );
						$extension = pathinfo( $the_file, PATHINFO_EXTENSION );
						switch ( $extension ) {
							case 'jpg':case 'png': case 'jpeg':
								echo '<img src="'.$the_file.'"/>';
							break;
							case 'pdf':?>
								<object data="<?php echo esc_url( $the_file );?>" type="application/pdf" width="100%" height="500px">
									<iframe src="<?php echo esc_url( $the_file );?>" width="100%" height="100%" style="border: none;">
										<?php esc_html_e( 'This browser does not support PDFs. Please download the PDF to view it:', 'wyzi-business-finder');?> <a href="<?php echo esc_url( $the_file );?>"><?php esc_html_e( 'Download PDF', 'wyzi-business-finder' );?></a>
									</iframe>
								</object>
							<?php
							break;
							case 'zip': case 'doc': case 'docx': case 'xls':
								echo '<a href="'.$the_file.'" target="_blank" class="wyz-link downloadable-attachment" title="'.esc_html__( 'Download', 'wyzi-business-finder' ).'">' . pathinfo( $the_file, PATHINFO_FILENAME ) . ' <i class="fa fa-cloud-download" aria-hidden="true"></i></a>';
							break;
						}
						echo '</div>';
					} elseif( 'url' == $value['type'] ){
						$extra_url = get_post_meta( $this->business_id, "wyzi_claim_fields_$key", true );
						echo '<div class="bus-custom-field"><a href="' . $extra_url . '" target="_blank">'.$extra_url.'</a></p></div>';
					}else {
						echo '<div class="bus-custom-field"><p>' . wpautop( do_shortcode( get_post_meta( $this->business_id, "wyzi_claim_fields_$key", true ) ) ) . '</p></div>';
					}
				}?>
				</div>
				<?php $this->template_type == 1 && $this->right_sidebar();?>
			</div>
			<?php
		}
	}

	private function the_additional($active,$urlid, $content) {
		if ( true ||$this->can_additional ) {
			$content = str_replace( '%business_id%', $this->business_id, $content );
			$content = str_replace( '%user_id%', get_current_user_ID(), $content );
			$content = str_replace( '%author_id%', $this->author_id, $content );?>

			<div class="tab-pane <?php echo $active; ?>" id="<?php echo $urlid;?>">
				<?php echo wpautop( do_shortcode( $content ) );?>
			</div>
			<?php
		}
	}

	private function right_sidebar() {?>
		<!-- Right Sidebar -->
		<div class="sidebar-container <?php echo $this->sidebar_responsive;?>">
			<?php if ( is_active_sidebar( 'wyz-single-business-sb' ) ) : ?>
				<div class="widget-area sidebar-widget-area">
					<?php dynamic_sidebar( 'wyz-single-business-sb' ); ?>
				</div>
			<?php endif; ?>
		</div><?php
	}

	public function rate_form() {
		$rate_nb = get_post_meta( $this->business_id, 'wyz_business_rates_count', true );
		$rate_sum = get_post_meta( $this->business_id, 'wyz_business_rates_sum', true );
		$rate;

		if ( ! empty( $rate_nb ) && ! empty( $rate_sum ) && $rate_nb > 0 ) {
			$rate = number_format( ( (float) $rate_sum ) / $rate_nb, 1 ) + 0;
		} else {
			$rate = 0;
		} ?>

		<p>
		<?php
		if ( $this->can_rate ) {
			if ( ! is_user_logged_in() ) {
				echo esc_html( get_option( 'wyz_businesses_rate_not_sub' ) );
			} elseif ( $this->is_user_author ) {
				esc_html_e( get_option( 'wyz_businesses_rate_owner' ) );
			} elseif ( ! $this->can_rate ) {
				echo esc_html_e( get_option( 'wyz_businesses_rate_rated_sub' ) );
			} else {
				echo esc_html_e( get_option( 'wyz_businesses_rate_sub' ) );
			}
		}
		$rate_reviews_txt = '';
		if ( ! empty( $rate_nb ) && $rate_nb > 0 ) {
			$rate_reviews_txt = " <span class=\"num-ratings\" title=\"Rating based on $rate_nb reviews\">($rate_nb)</span>";
		}
		?>
		</p> 


		<div class="ratings"  >
			<span data-busid="<?php echo esc_attr( $this->business_id ); ?>" data-userid="<?php echo esc_attr( get_current_user_ID() ); ?>">
				<?php echo "<span id=\"bus-rate\">$rate/5$rate_reviews_txt</span>";

				$r = round( $rate );

				for( $i = 5; $i >0; $i-- ) {
					echo "<input class=\"star star-$i\" name=\"rating\" id=\"star-$i\" type=\"radio\" value=\"$i\" " . ( $r == $i ? 'checked' : '' ) . " disabled=\"disabled\" />";
					echo '<label class="star star-' . $i . ' star-hov" for="star-' . $i . '"></label>';
				}?>
			</span>
		</div>
		<?php 
	}

	private function check_for_no_active() {
		if( empty( $this->tab_data ) || ! is_array( $this->tab_data ) ) {
			return;
		}

		foreach ( $this->tab_data as $tab ) {
			if ( $tab['active'] )
				return;
		}
		$this->tab_data[0]['active']=true;
	}

	private function evaluate_ratings() {

		$user_id = get_current_user_ID();

		$all_business_ratings = get_post_meta( $this->business_id, 'wyz_business_ratings', true );
		if ( '' == $all_business_ratings ){
			$all_business_ratings = array();
		}
		$this->have_ratings = false;
		$authors = array();
		if ( ! empty( $all_business_ratings ) ) {
			$args = array(
				'post_type' => 'wyz_business_rating',
				'post__in' => $all_business_ratings,
				'post_status' => 'publish',
				'posts_per_page' => -1,
			);
			$query = new WP_Query( $args );
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post( get_the_ID() );
				$authors[] = $post->post_author;
			}
			
			$this->have_ratings = $query->have_posts();
			wp_reset_postdata();
		}

		$this->can_rate = true;
		if ( $this->have_ratings ) {
			
			if ( ! empty( $authors ) && in_array( $user_id, $authors ) ) {
				$this->can_rate = false;
			}
		}
		$this->can_rate = ( ! $this->have_ratings || ( $this->have_ratings && $this->can_rate ) ) && is_user_logged_in()  && ! $this->is_user_author;
	}

	private function evaluate_attachments() {
		// Get business images.
		$this->gallery_data = array();
		$this->attachments = get_post_meta( $this->business_id, 'business_gallery_image', true );
		if ( $this->attachments && '' !== $this->attachments && ! empty( $this->attachments ) ) {
			$current_image_attached_thumb = array();
			$current_image_attached_full = array();
			if ( ! is_array( $this->attachments ) ) {
				$temp_thumb = wp_get_attachment_image_src( $this->attachments, 'medium', array() );
				$temp_full = wp_get_attachment_image_src( $this->attachments, 'full' );
				array_push( $current_image_attached_thumb,  $temp_thumb[0] );
				array_push( $current_image_attached_full,  $temp_full[0] );
			} else {
				foreach ( $this->attachments as $attachment ) {
					$temp_thumb = wp_get_attachment_image_src( $attachment, 'medium', array() );
					$temp_full = wp_get_attachment_image_src( $attachment, 'full' );
					if ( '' != $temp_thumb && ''!= $temp_full ) {
						array_push( $current_image_attached_thumb,  $temp_thumb[0] );
						array_push( $current_image_attached_full,  $temp_full[0] );
					}
				}
			}
			$this->gallery_data = array(
				'currentImageAttachedFull'  => $current_image_attached_full,
				'currentImageAttachedThumb' => $current_image_attached_thumb,
			);
		}
	}

	private function evaluate_have_posts() {
		$all_business_posts = get_post_meta( $this->business_id, 'wyz_business_posts', true );
		$this->have_posts = false;

		if ( ! empty( $all_business_posts ) ) {
			$args = array(
				'post_type' => 'wyz_business_post',
				'post__in' => $all_business_posts,
				'post_status' => 'publish',
				'posts_per_page' => 1,
			);
			$query = new WP_Query( $args );
			$this->have_posts = $query->have_posts();
			wp_reset_postdata();
		}

		if ( ! $this->can_products )
			return;

		$users = get_users(array('role'=>'administrator','fields'=>'ID'));
		$users[] = $this->author_id;
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'author__in' => $users,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => 'business_id',
					'value' => $this->business_id,
					'compare' =>'=',
				)
			)
		);

		$this->products_query = new WP_Query( $args );
		$this->can_products = $this->products_query->have_posts();
	}

	private function evaluate_have_images() {
		$this->have_images = $this->attachments && ! empty( $this->attachments );
	}
}