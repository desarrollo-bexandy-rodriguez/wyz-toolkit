<?php
/**
 * Goole maps
 *
 * @package wyz
 */

/**
 * Class WyzMap.
 */
class WyzMap{

	/**
	 * Initialize map js scripts.
	 *
	 * @param integer $map_type 1: global map, 2: single business, 3:contact page.
	 */

	private static $map_js;
	private static function wyz_initialize_map_scripts( $map_type ) {

		$language = get_bloginfo( 'language' );

		wp_register_script( 'wyz_map_api', '//maps.googleapis.com/maps/api/js?libraries=places&language='.$language.'&key=' . get_option( 'wyz_map_api_key' ) . '&callback=wyz_init_load_map_callback#asyncload', array( 'jquery' ) );

		self::$map_js = array( 'wyz_map_api' );
		if( 'on' == get_option( 'wyz_map_lockable', 'off' ) ) {
			wp_register_script( 'wyz_map_layer', plugin_dir_url( __FILE__ ) . 'js/map-layer.js', '', '', true );
			self::$map_js[] = 'wyz_map_layer';
		}

		if ( 2 == $map_type ) { // Single business map.
			wp_register_script( 'wyz_single_bus_map', plugin_dir_url( __FILE__ ) . 'js/single-business-map.js', self::$map_js, '', true );
		} elseif ( 1 == $map_type ) { // Global map.
			wp_register_script( 'wyz_map_cluster', plugin_dir_url( __FILE__ ) . 'js/map-cluster.js', self::$map_js, '', true );
		} else { // Contact map.
			wp_register_script( 'wyz_contact_map', plugin_dir_url( __FILE__ ) . 'js/contact-map.js', self::$map_js, '', true );
		}
	}


	public static function get_map_tabs() {

		$all_tabs = get_option( 'wyz_business_tabs_order_data' );
		$tabs = array();

		foreach ($all_tabs as $tab) {
			if ( $tab['type'] == 'photo') {
				$tabs['photo'] = ( isset( $tab['urlid'] ) && '' != $tab['urlid'] ) ? $tab['urlid'] : 'photo';
			}
			elseif($tab['type'] == 'ratings') {
				$tabs['rating'] = ( isset( $tab['urlid'] ) && '' != $tab['urlid'] ) ? $tab['urlid'] : 'ratings';
			}
			elseif($tab['type'] == 'bookings') {
				$tabs['booking'] = ( isset( $tab['urlid'] ) && '' != $tab['urlid'] ) ? $tab['urlid'] : 'bookings';
			}
			
		}

		return $tabs;
	}


	private static function wyz_get_businesses_js_data( $id, $def_coor, $taxonomies ) {

		$template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) )
			$template_type = wyz_get_theme_template();

		$grid_look = get_post_meta( get_the_ID(), 'wyz_list_grid', true );
		if ( empty( $grid_look ) ) {
			$grid_look = false;
		}
		
		$is_listing_page = is_page_template( 'templates/business-listing-page.php' );


		$posts_per_page = intval( get_post_meta( $id, 'wyz_listing_page_pagination', true ) );
		if ( '' == $posts_per_page || 1 > $posts_per_page ) {
			$posts_per_page = -1;
		}

		$radius_unit = get_option( 'wyz_business_map_radius_unit' );
		if ( 'mile' != $radius_unit ) {
			$radius_unit = 'km';
		}

		$def_loc_id =  get_post_meta( $id, 'wyz_def_map_location', true );
		if ( '' != $def_loc_id ) {
			$coor = get_post_meta( $def_loc_id, 'wyz_location_coordinates', true );
			if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
			$def_loc_id = '{"id":"'.$def_loc_id.'","lat":"'. $coor['latitude'] .'","lon":"'. $coor['longitude'] .'"}';
		} else {
			$def_loc_id = -1;
		}

		$def_cat_id =  get_post_meta( $id, 'wyz_default_map_category', true );
		if ( '' == $def_cat_id ) {
			$def_cat_id = -1;
		}

		$def_rad =  get_post_meta( $id, 'wyz_default_map_radius', true );
		if ( '' == $def_rad ) {
			$def_rad = 0;
		}


		$map_skin = get_post_meta( $id, 'wyz_post_map_skin', true );
		$map_skin = self::get_map_skin( $map_skin, $id );

		$header_type = get_post_meta( $id, 'wyz_page_header_content', true );
		$filter_type = '';
		if ( $is_listing_page ) {
			$filter_type = get_post_meta( $id, 'wyz_map_location_filter_type', true );
		} else {
			switch( $header_type ) {
				case 'map':
					$filter_type = get_post_meta( $id, 'wyz_map_location_filter_type', true );
				break;
				case 'image':
					$filter_type = get_post_meta( $id, 'wyz_image_location_filter_type', true );
				break;
			}
		}

		$on_load_loc_req = get_post_meta( $id, 'wyz_on_load_loc_req', true );


		$on_load_loc_req = 'on' == $on_load_loc_req;
		
		if ( '' == $filter_type )
			$filter_type = 'dropdown';

		$tabs = self::get_map_tabs();


		$global_map_java_data = array(
			'defCoor' => $def_coor,
			'radiusUnit' => $radius_unit,
			'GPSLocations' => array(),
			'markersWithIcons' => array(),
			'businessNames' => array(),
			'businessLogoes' => array(),
			'businessPermalinks' => array(),
			'businessCategories' => array(),
			'businessCategoriesColors' => array(),
			'myLocationMarker' => plugin_dir_url( __FILE__ ) . 'images/iamhere.png',
			'locLocationMarker' => plugin_dir_url( __FILE__ ) . 'images/locationhere.png',
			'geolocation' => 'on' == wyz_get_option( 'geolocation' ) ? true : false,
			'taxonomies' => $taxonomies,
			'businessList' => '',
			'isListingPage' => $is_listing_page,
			'isGrid' => $grid_look,
			'favEnabled' => 'on' == get_option( 'wyz_enable_favorite_business' ),
			'postsPerPage' => $posts_per_page,
			'businessIds' => array(),
			'defLoc' => $def_loc_id,
			'defCat' => $def_cat_id,
			'defRad' => $def_rad,
			'defLogo' => WyzHelpers::get_default_image( 'business' ),
			'templateType' => $template_type,
			'hasAfter' => true,
			'hasBefore' => false,
			'favorites' => array(),
			'filterType' => $filter_type,
			'onLoadLocReq' => $on_load_loc_req,
			'translations' => array(
				'searchText' => esc_html__( 'Search here...', 'wyzi-business-finder' ),
				'viewDetails' => esc_html__( 'View Details', 'wyzi-business-finder' ),
				'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
				'noBusinessesFound' => esc_html__( 'No Businesses match your search', 'wyzi-business-finder' ),
				'notValidRad' => esc_html__( 'Not a valid radius', 'wyzi-business-finder' ),
				'geoFail' => esc_html__( 'The Geolocation service failed. You can still search by location radius, but not by your location.', 'wyzi-business-finder' ),
				'geoBrowserFail' => esc_html__( 'Error: Your browser doesn\'t support geolocation.', 'wyzi-business-finder' ),
				'prev' => esc_html__( 'Previous', 'wyzi-business-finder' ),
				'nxt' => esc_html__( 'Next', 'wyzi-business-finder' ),
			),
			'tabs' => $tabs,
			'mapSkin' => $map_skin,
		);

		return $global_map_java_data;
	}


	public static function get_map_skin( $map_skin, $id ) {
		$hide_poi = 'on' == get_post_meta( $id, 'wyz_hide_post_map_poi', true );
		
		switch( $map_skin ) {
			case 1:
				return json_decode('[
					  {
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#f5f5f5"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.icon",
					    "stylers": [
					      {
					        "visibility": "off"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#616161"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#f5f5f5"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.land_parcel",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#bdbdbd"
					      }
					    ]
					  },' . ( $hide_poi ? ( '{
					    "featureType": "poi",
					    "stylers": [
					      { "visibility": "off" }
					    ]   
					  },' ) : ( '{
					    "featureType": "poi",
					    "stylers": [
					      {
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#eeeeee"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#e5e5e5"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#9e9e9e"
					      }
					    ]
					  },' )) .
					  '{
					    "featureType": "road",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#ffffff"
					      }
					    ]
					  },
					  {
					    "featureType": "road.arterial",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dadada"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#616161"
					      }
					    ]
					  },
					  {
					    "featureType": "road.local",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#9e9e9e"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.line",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#e5e5e5"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.station",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#eeeeee"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#c9c9c9"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#9e9e9e"
					      }
					    ]
					  }
					]', true);

			case 2:
				return json_decode('[
					  {
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#ebe3cd"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#523735"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#f5f1e6"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#c9b2a6"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.land_parcel",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#dcd2be"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.land_parcel",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#ae9e90"
					      }
					    ]
					  },
					  {
					    "featureType": "landscape.natural",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dfd2ae"
					      }
					    ]
					  },' . ( $hide_poi ? ( '{
					    "featureType": "poi",
					    "stylers": [
					      { "visibility": "off" }
					    ]   
					  },' ) : 
					  '{
					    "featureType": "poi",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dfd2ae",
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#93817c"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#a5b076"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#447530"
					      }
					    ]
					  },') .
					  '{
					    "featureType": "road",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#f5f1e6"
					      }
					    ]
					  },
					  {
					    "featureType": "road.arterial",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#fdfcf8"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#f8c967"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#e9bc62"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway.controlled_access",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#e98d58"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway.controlled_access",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#db8555"
					      }
					    ]
					  },
					  {
					    "featureType": "road.local",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#806b63"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.line",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dfd2ae"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.line",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#8f7d77"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.line",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#ebe3cd"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.station",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dfd2ae"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#b9d3c2"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#92998d"
					      }
					    ]
					  }
					]',true);
			break;
			case 3:
				return json_decode('[
					  {
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#212121"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.icon",
					    "stylers": [
					      {
					        "visibility": "off"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#212121"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.country",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#9e9e9e"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.land_parcel",
					    "stylers": [
					      {
					        "visibility": "off"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.locality",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#bdbdbd"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "stylers": [
					      {
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },' . ( $hide_poi ? ( '{
					    "featureType": "poi",
					    "stylers": [
					      { "visibility": "off" }
					    ]   
					  },' ) :

					  '{
					    "featureType": "poi",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#dfd2ae",
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#93817c"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#a5b076"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#447530"
					      }
					    ]
					  },').
					  '{
					    "featureType": "road",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#2c2c2c"
					      }
					    ]
					  },
					  {
					    "featureType": "road",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#8a8a8a"
					      }
					    ]
					  },
					  {
					    "featureType": "road.arterial",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#373737"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#3c3c3c"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway.controlled_access",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#4e4e4e"
					      }
					    ]
					  },
					  {
					    "featureType": "road.local",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#616161"
					      }
					    ]
					  },
					  {
					    "featureType": "transit",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#757575"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#000000"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#3d3d3d"
					      }
					    ]
					  }
					]',true);

			break;
			case 4:
				return json_decode('[
					  {
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#242f3e"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#746855"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#242f3e"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.locality",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#d59563"
					      }
					    ]
					  },' . ( $hide_poi ? ( '{
					    "featureType": "poi",
					    "stylers": [
					      { "visibility": "off" }
					    ]   
					  },' ) : 
					  '{
					    "featureType": "poi",
					    "stylers": [
					      {
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#d59563"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#263c3f"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#6b9a76"
					      }
					    ]
					  },') .
					  '{
					    "featureType": "road",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#38414e"
					      }
					    ]
					  },
					  {
					    "featureType": "road",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#212a37"
					      }
					    ]
					  },
					  {
					    "featureType": "road",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#9ca5b3"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#746855"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#1f2835"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#f3d19c"
					      }
					    ]
					  },
					  {
					    "featureType": "transit",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#2f3948"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.station",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#d59563"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#17263c"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#515c6d"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#17263c"
					      }
					    ]
					  }
					]',true);
			break;
			case 5:
				return json_decode('[
					  {
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#1d2c4d"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#8ec3b9"
					      }
					    ]
					  },
					  {
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#1a3646"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.country",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#4b6878"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.land_parcel",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#64779e"
					      }
					    ]
					  },
					  {
					    "featureType": "administrative.province",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#4b6878"
					      }
					    ]
					  },
					  {
					    "featureType": "landscape.man_made",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#334e87"
					      }
					    ]
					  },
					  {
					    "featureType": "landscape.natural",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#023e58"
					      }
					    ]
					  },' .( $hide_poi ? ( '{
					    "featureType": "poi",
					    "stylers": [
					      { "visibility": "off" }
					    ]   
					  },' ) : 
					  '{
					    "featureType": "poi",
					    "stylers": [
					      {
					        "visibility": "on"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#283d6a"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#6f9ba5"
					      }
					    ]
					  },
					  {
					    "featureType": "poi",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#1d2c4d"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#023e58"
					      }
					    ]
					  },
					  {
					    "featureType": "poi.park",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#3C7680"
					      }
					    ]
					  },') .
					  '{
					    "featureType": "road",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#304a7d"
					      }
					    ]
					  },
					  {
					    "featureType": "road",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#98a5be"
					      }
					    ]
					  },
					  {
					    "featureType": "road",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#1d2c4d"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#2c6675"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "geometry.stroke",
					    "stylers": [
					      {
					        "color": "#255763"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#b0d5ce"
					      }
					    ]
					  },
					  {
					    "featureType": "road.highway",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#023e58"
					      }
					    ]
					  },
					  {
					    "featureType": "transit",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#98a5be"
					      }
					    ]
					  },
					  {
					    "featureType": "transit",
					    "elementType": "labels.text.stroke",
					    "stylers": [
					      {
					        "color": "#1d2c4d"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.line",
					    "elementType": "geometry.fill",
					    "stylers": [
					      {
					        "color": "#283d6a"
					      }
					    ]
					  },
					  {
					    "featureType": "transit.station",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#3a4762"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "geometry",
					    "stylers": [
					      {
					        "color": "#0e1626"
					      }
					    ]
					  },
					  {
					    "featureType": "water",
					    "elementType": "labels.text.fill",
					    "stylers": [
					      {
					        "color": "#4e6d70"
					      }
					    ]
					  }
					]',true);
			break;
		}
		return json_decode( $hide_poi ? '[
			{
			    "featureType": "poi",
			    "stylers": [
			      { "visibility": "off" }
			    ]   
			  }
			]' : '[]',true);
	}

	public static function wyz_the_business_map( $business_id, $is_business ) {

		self::wyz_initialize_map_scripts( 2 );
		global $template_type;

		if ( has_post_thumbnail( $business_id ) ) {

			$marker_logo = wp_get_attachment_image_src( get_post_thumbnail_id( $business_id ), 'medium' );
			if ( is_array( $marker_logo ) ) {
				$marker_logo = $marker_logo[0];
			}
		} else{

			$marker_logo = WyzHelpers::get_default_image( 'business' );
		}

		$temp_term = WyzHelpers::wyz_get_representative_business_category_id( $business_id );//get_the_terms( $business_id, 'wyz_business_category' );

		if ( '' != $temp_term ) {

			$cat = get_term_meta( $temp_term, 'wyz_business_cat_bg_color', true );
			$icon_meta_key = 'map_icon' . ( 2 == $template_type ? '2' : '' );
			$holder = wp_get_attachment_url( get_term_meta( $temp_term, $icon_meta_key, true ) );
		} else {
			$cat = '';
		}
		if ( ! isset( $holder ) || false == $holder ) {
			$marker = '';
		} else {
			$marker = $holder;
		}


		$map_skin = get_option( 'wyz_business_archives_map_skin' );
		$map_php_vars = array(
			'businesses' => array(
				array(
					'loaded' => false,
					'id' => $business_id,
					'logo' => $marker_logo,
					'businessName' => get_the_title( $business_id ),
					'businessPermalink' => get_permalink( $business_id ),
					'marker' => $marker,
					'categoryColor' => $cat,
				),
			),
			'mapSkin' => WyzMap::get_map_skin( $map_skin, -1 ),
			'isBusiness' => $is_business,
			'defaultIcon' => plugin_dir_url( __FILE__ ) . 'images/default-map-icon.jpg',
			'viewDetails' => esc_html__( 'View Details', 'wyzi-business-finder' ),
			'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
			'templateType' => $template_type,
		);

		wp_enqueue_script( 'wyz_single_bus_map' );

		wp_localize_script( 'wyz_single_bus_map', 'businessMap', $map_php_vars );
		$mapGPS = get_post_meta( $business_id, 'wyz_business_location', true );
		if ( ! isset( $mapGPS['latitude'] ) || '' == $mapGPS['latitude'] || ! isset( $mapGPS['longitude'] ) || '' == $mapGPS['longitude'] ) {
			$lat = get_option( 'wyz_businesses_default_lat' );
			$lon = get_option( 'wyz_businesses_default_lon' );
			$zoom = 11;
		} else {
			$lat = $mapGPS['latitude'];
			$lon = $mapGPS['longitude'];
			if ( ! isset( $mapGPS['zoom'] ) ) {
				$zoom = 12;
			} else {
				$zoom = $mapGPS['zoom'];
			}
	   	 }

		echo '<script type="text/javascript">//<![CDATA[ 
		var lat = ' . json_encode( $lat ) . '; var lon = ' . json_encode( $lon ) . '; var zoom = ' . wp_json_encode( $zoom ) . ';//]]></script>';
		echo self::wyz_get_the_map( '',$lat, $lon, $zoom, 2, '' );
	}

	public static function wyz_get_global_map( $id, $def_coor ) {
		self::wyz_initialize_map_scripts( 1 );
		$global_zoom = get_post_meta( get_the_ID(), 'wyz_page_map', true );

		$global_zoom = ( isset( $global_zoom['zoom'] ) && '' != isset( $global_zoom['zoom'] ) ? $global_zoom['zoom'] : 12 );

		$taxonomies = WyzHelpers::get_business_categories();
		$global_map_java_data = self::wyz_get_businesses_js_data( $id, $def_coor, $taxonomies );
		wp_enqueue_script( 'wyz_map_cluster' );
		wp_localize_script( 'wyz_map_cluster', 'globalMap', $global_map_java_data );

		echo self::wyz_get_the_map( $id, '', '', $global_zoom, 1, $taxonomies );
	}

	public static function wyz_get_archives_map() {
		self::wyz_initialize_map_scripts( 1 );
		$global_zoom = get_post_meta( get_the_ID(), 'wyz_page_map', true );

		$global_zoom = ( isset( $global_zoom['zoom'] ) ? $global_zoom['zoom'] : 12 );

		wp_enqueue_script( 'wyz_archives_map', plugin_dir_url( __FILE__ ) . 'js/archives-map.js', self::$map_js, '', true ); ?>
		<div class="map-container home-map-container margin-bottom-50 mb-50">
			<div id="map-mask" style="position:absolute; width:100%; height: <?php echo esc_attr( get_option( 'wyz_businesses_map_height' ) );?>px;background-color:#e4e9f5;opacity:0.6;display:none;z-index:1000;"></div>
			<div id="home-map" style="height:<?php echo esc_attr( get_option( 'wyz_businesses_map_height', 500 ) );?>px;"></div>
		</div>
		<?php
	}

	public static function wyz_contact_map( $id, $coor ) {
		self::wyz_initialize_map_scripts( 3 );
		if ( ! isset( $coor['zoom'] ) ) {
			$zoom = 12;
		} else {
			$zoom = $coor['zoom'];
		}

		$map_java_data = array(
			'coor' => $coor,
			'zoom' => $zoom,
			'marker' =>  plugin_dir_url( __FILE__ ) . 'images/contact-marker.png',
		);
		wp_enqueue_script( 'wyz_contact_map' );
		wp_localize_script( 'wyz_contact_map', 'contactMap', $map_java_data );
	    	echo self::wyz_get_the_map( $id,'', '', '', 3, '' );
	}

	public static function wyz_get_page_header_image( $id ) {
		$img = get_post_meta( $id, 'wyz_page_header_image', true );
		if ( '' == $img ) { return; }
		echo '<div id="page-header-image" style="min-height:'.get_post_meta( $id, 'wyz_image_height', true ).'px;background-image: url(' . esc_url( $img ) . ');">';?>
			<div id="header-image-content">
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<h2 class="header-image-main-content"><?php echo esc_html( get_post_meta( $id, 'wyz_page_header_image_main_text', true ) );?></h2>
						<div class="header-image-sub-content"><?php echo esc_html( get_post_meta( $id, 'wyz_page_header_image_sub_text', true ) );?></div>
						<?php if ( 'on' == get_post_meta( $id, 'wyz_page_header_image_show_filters', true ) ) {
							$filter_data = get_post_meta( $id, 'wyz_page_header_image_filters', true );
							if( '' == $filter_data ) $filter_data = array();
							else $filter_data = explode(',', $filter_data); ?>
						<div class="header-image-filters col-xs-12"><?php WyzHelpers::wyz_get_business_filters( $filter_data );?><div class="clear"></div></div>
						<?php }?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function wyz_get_business_header_image( $id ) {
		$img = get_post_meta( $id, 'wyz_business_header_image', true );
		$bg_color = get_post_meta( $id, 'wyz_business_logo_bg', true );
		if ( '' == $img ) { 
			echo '<div id="page-header-image" class="business-header-image business-header-no-image" style="min-height:'.get_option( 'wyz_businesses_map_height' ).'px;background-color: #ccc;">';
			if ( WyzHelpers:: wyz_is_current_user_author( $id ) )
				echo '<div class="container-2"><span class="icon"><i class="fa fa-photo"></i></span><button id="business-header-no-image-btn" class="business-set-header-image-btn" >' . esc_html__( 'Upload Cover Photo', 'wyzi-business-finder' ) . '</button></div>';
			if ( WyzHelpers::wyz_sub_can_bus_owner_do(WyzHelpers::wyz_the_business_author_id(),'wyzi_sub_show_business_logo') ) {
				echo '<div class="header-image-logo col-lg-3 col-md-4 col-sm-4 col-xs-6" style="background-color: ' . $bg_color . ';">';
				if ( is_singular( 'wyz_offers' ) ) {
					echo get_the_post_thumbnail( $id, 'medium', array( 'class' => 'logo' ) );
				} else {
					the_post_thumbnail( 'medium', array( 'class' => 'logo' ) );
				}
				echo '</div>';
			} 
			echo '</div>';
		}
		else { 
			echo '<div id="page-header-image" class="business-header-image" style="min-height:'.get_option( 'wyz_businesses_map_height' ).'px;background-image: url(' . $img . ');">';
			if ( WyzHelpers:: wyz_is_current_user_author( $id ) )
				echo '<div class="container-2"><span class="icon"><i class="fa fa-photo"></i></span><button id="business-header-no-image-btn" class="business-edit-header-image-btn" >' . esc_html__( 'Change Cover Photo', 'wyzi-business-finder' ) . '</button></div>';
			if ( WyzHelpers::wyz_sub_can_bus_owner_do(WyzHelpers::wyz_the_business_author_id(),'wyzi_sub_show_business_logo') ) {
				echo '<div class="header-image-logo col-lg-3 col-md-4 col-sm-4 col-xs-6" style="background-color: ' . $bg_color . ';">';
				if ( is_singular( 'wyz_offers' ) ) {
					echo get_the_post_thumbnail( $id, 'medium', array( 'class' => 'logo' ) );
				} else {
					the_post_thumbnail( 'medium', array( 'class' => 'logo' ) );
				}
				echo '</div>';
			} 
			echo '</div>';
		}
	}


	public static function get_map_lock( $return = false ) {
		if ( 'on' != get_option( 'wyz_map_lockable' ) )
			return '';

		$s = '<span id="map-unlock" class="map-unlock"></span>';
		if ( $return ) {
			return $s;
		}
		echo $s;
	}



	private static function wyz_get_the_map( $id, $lat, $lon, $zoom, $map_type, $taxonomies ) {

		// 1: global map, 2: single business, 3:contact page.

		global $template_type;
		
		if ( 2 == $map_type ) {
			
			$map_scroll_zoom = get_option( 'wyz_business_map_scroll_zoom' );
			if ( 'on' != $map_scroll_zoom ) {
				$map_scroll_zoom = 'off';
			}
		
			return ( '<script type="text/javascript">//<![CDATA[
			var lat = ' . wp_json_encode( $lat ) . '; var lon = ' . wp_json_encode( $lon ) . '; var zoom = ' . wp_json_encode( $zoom ) . ';var mapScrollZoom = ' . wp_json_encode( $map_scroll_zoom ) . ';//]]></script>
			<div class="map-container" style="position:relative;">
				<div id="business-map" style="height:' . get_option( 'wyz_businesses_map_height' ) . 'px;"></div>
				' . self::get_map_lock( true ) . '</span>
			</div>' );
		}
		
		$map_scroll_zoom = get_post_meta( $id, 'wyz_map_scroll_zoom_checkbox', true );
		if ( 'on' != $map_scroll_zoom ) {
			$map_scroll_zoom = 'off';
		}
			
		if ( 3 == $map_type ) {
			
			if ( '' == get_post_meta( $id, 'wyz_map_height', true ) ) {
				$map_height = 400;
			} else {
				$map_height = get_post_meta( $id, 'wyz_map_height', true );
			}

			return ( '<script type="text/javascript">//<![CDATA[
			var mapScrollZoom = ' . wp_json_encode( $map_scroll_zoom ) . ';//]]></script>
			<div id="canvas" class="map-container">
				<div id="contact-map" style="height:' . $map_height . 'px;"></div>
				' . self::get_map_lock( true ) . '
			</div>' );
		}

		if ( '' == get_post_meta( $id, 'wyz_map_height', true ) ) {
			$map_height = 400;
		} else {
			$map_height = get_post_meta( $id, 'wyz_map_height', true );
		}
		$map_autozoom = get_post_meta( $id, 'wyz_page_autozoom', true );
		if ( 'on' != $map_autozoom ) {
			$map_autozoom = 'off';
		}
		ob_start(); ?>
		<script type="text/javascript">//<![CDATA[
		var lat = <?php echo wp_json_encode( $lat ); ?>; var lon = <?php echo wp_json_encode( $lon );?>; var zoom = <?php echo wp_json_encode( $zoom );?>; var mapAutoZoom = <?php echo wp_json_encode( $map_autozoom );?>; var mapScrollZoom = <?php echo wp_json_encode( $map_scroll_zoom );?>;
		//]]></script>

		<div class="map-container home-map-container margin-bottom-100">

			<?php //include( plugin_dir_path( __FILE__ ) . 'images/map-loading.php' );?>

			<div id="map-loading" class="map-cssload-container">
				<div class="cssload-whirlpool"></div>
			</div>

			<!-- <div id="map-mask" style="position:absolute; width:100%; height: <?php echo esc_attr( $map_height );?>px;background-color:#e4e9f5;opacity:0.6;display:none;z-index:1000;"></div>
			<div id="home-map" style="height:<?php //echo esc_attr( $map_height );?>px;"></div> -->
			<!-- Map Lock & Unlock -->
			<?php //self::get_map_lock();?>
			<!-- <div class="home-map-area section"> -->
			<div id="home-map" style="height:<?php echo $map_height;?>px;"></div>
			<?php if ( 2 == $template_type ) {?>
			<div id="slidable-map-sidebar" style="height:<?php echo $map_height;?>px;"><?php self::listing_map_sidebar();?></div>
			<?php } 
			if ( 2 == $template_type ) echo '</div>'; ?>
		<!-- </div> -->
			<!-- Location Search -->
			<div class="location-search-float pt-60 pb-60">
				<div class="container">
					<div class="row">
						<div class="text-center col-xs-12">
							<div class="location-search filter-count-4">
								<?php if ( 1 == $template_type ) {?><h2><?php esc_html_e( 'search your location', 'wyzi-business-finder' );?></h2><?php }?>
								<form action="#">
									<div class="input-keyword input-location input-box"><input id="map-names" type="text" placeholder="<?php esc_html_e( 'search keywords', 'wyzi-business-finder' );?>"/>
									</div>
									<?php WyzHelpers::wyz_locations_filter( true );?>
									
									<?php WyzHelpers::wyz_categories_filter( $taxonomies );?>
									<?php $def_rad =  get_post_meta( $id, 'wyz_default_map_radius', true );
									if ( '' == $def_rad ) {
										$def_rad = 0;
									}?>
									<div id="loc-radius-cont" class="input-range input-location input-box last">
										<p><?php esc_html_e( 'Radius', 'wyzi-business-finder' );?><?php if ( $template_type == 1 ) {?>:  <span></span><?php } ?></p>
										<input id="loc-radius" type="range" value="<?php echo $def_rad;?>" min="0" max="500" step="1" />
									</div>
									<div class="input-submit">
										<button id="map-search-submit" class="wyz-primary-color wyz-secon-color" type="button"><i class="fa fa-search"></i> <?php esc_html_e( 'search', 'wyzi-business-finder' );?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php if ( 2 != $template_type ) echo '</div>';
		return ob_get_clean();
	}

	private static function the_business_map_js( $business_id, $is_business ) {
		global $template_type;
		$language = get_bloginfo( 'language' );
		wp_register_script( 'wyz_map_api', '//maps.googleapis.com/maps/api/js?libraries=places&language='.$language.'&key=' . get_option( 'wyz_map_api_key' ) . '&callback=wyz_init_load_map_callback#asyncload' );
		wp_register_script( 'wyz_single_bus_map', plugin_dir_url( __FILE__ ) . 'js/single-business-map.js', array( 'wyz_map_api' ), '', true );

		if ( has_post_thumbnail( $business_id ) ) {

			$marker_logo = wp_get_attachment_image_src( get_post_thumbnail_id( $business_id ), 'medium' );
			if ( is_array( $marker_logo ) ) {
				$marker_logo = $marker_logo[0];
			}
		} else{

			$marker_logo = WyzHelpers::get_default_image( 'business' );
		}

		$temp_term = WyzHelpers::wyz_get_representative_business_category_id( $business_id );

		if ( '' != $temp_term ) {

			$cat = get_term_meta( $temp_term, 'wyz_business_cat_bg_color', true );
			$icon_meta_key = 'map_icon' . ( 2 == $template_type ? '2' : '' );
			$holder = wp_get_attachment_url( get_term_meta( $temp_term, $icon_meta_key, true ) );
				
		} else {
			$cat = '';
		}
		if ( ! isset( $holder ) || false == $holder ) {
			$marker = '';
		} else {
			$marker = $holder;
		}

		
		$map_skin = get_option( 'wyz_business_archives_map_skin' );
		$map_php_vars = array(
			'businesses' => array(
				array(
					'loaded' => false,
					'id' => $business_id,
					'logo' => $marker_logo,
					'businessName' => get_the_title( $business_id ),
					'businessPermalink' => get_permalink( $business_id ),
					'marker' => $marker,
					/*'ratings' =>  WyzBusinessRating::get_business_rates_stars( $business_id, true ),
					'banner_image' => WyzHelpers::get_image( $business_id ),
					'slogan' => get_post_meta( $business_id, 'wyz_business_slogan', true ),*/
				),
			),
			'mapSkin' => WyzMap::get_map_skin( $map_skin, -1 ),
			'defaultIcon' => plugin_dir_url( __FILE__ ) . 'images/default-map-icon.jpg',
			'isBusiness' => $is_business,
			'viewDetails' => esc_html__( 'View Details', 'wyzi-business-finder' ),
			'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
			'photoLink' => self::get_map_tabs()['photo'],
			'templateType' => $template_type,
		);

		wp_enqueue_script( 'wyz_single_bus_map' );

		wp_localize_script( 'wyz_single_bus_map', 'businessMap', $map_php_vars );
		$mapGPS = get_post_meta( $business_id, 'wyz_business_location', true );
		if ( ! isset( $mapGPS['latitude'] ) || '' == $mapGPS['latitude'] || ! isset( $mapGPS['longitude'] ) || '' == $mapGPS['longitude'] ) {
			$lat = get_option( 'wyz_businesses_default_lat' );
			$lon = get_option( 'wyz_businesses_default_lon' );
			$zoom = 11;
		} else {
			$lat = $mapGPS['latitude'];
			$lon = $mapGPS['longitude'];
			if ( ! isset( $mapGPS['zoom'] ) ) {
				$zoom = 12;
			} else {
				$zoom = $mapGPS['zoom'];
			}
	   	 }
	   	 ?>
		<script type="text/javascript">//<![CDATA[
		var lat = <?php echo wp_json_encode( $lat ); ?>; var lon = <?php echo wp_json_encode( $lon );?>; var zoom = <?php echo wp_json_encode( $zoom );?>;
		//]]></script>

		<?php

		echo '<script type="text/javascript">//<![CDATA[ 
		var lat = ' . json_encode( $lat ) . '; var lon = ' . json_encode( $lon ) . '; var zoom = ' . wp_json_encode( $zoom ) . ';//]]></script>';
		
	}

	public static function listing_single_business_map( $business_id ) {
		self::the_business_map_js($business_id, true);
		?>

		<div class="page-map-area section">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8"><div id="business-map" style="height:<?php echo get_option( 'wyz_businesses_map_height' );?>px;"></div></div>
					<?php self::listing_map_sidebar('col-md-4', $business_id);?>
				</div>
			</div>
		</div>
		<?php
	}

	private static function listing_map_sidebar( $class='', $business_id = '' ){
		
	echo ( '' == $class ? '<div class="col-md-8"></div>' : '' );
	$can_booking = false;
	$tab_data = get_option( 'wyz_business_tabs_order_data' );
	foreach ($tab_data as $tab) {
		if( $tab['type'] == 'bookings') {
			$can_booking = true;
			break;
		}
	}
	if ( '' != $business_id )
		$map_height = get_post_meta( $business_id, 'wyz_map_height', true );
	else
		$map_height = get_post_meta( get_the_ID(), 'wyz_map_height', true );
	if ( is_singular( 'wyz_business' ) || is_singular( 'wyz_offers' ) ){
		$map_height = get_option( 'wyz_businesses_map_height' );
		$tabs = self::get_map_tabs();
		$author_id = WyzHelpers::wyz_the_business_author_id();
		if ( $can_booking ) {
			if ( 'off' == get_option( 'wyz_users_can_booking' ) || ! WyzHelpers::wyz_sub_can_bus_owner_do($author_id,'wyzi_sub_business_can_create_bookings') || ! WyzHelpers::get_user_calendar( $author_id, get_the_ID() ) )
				$can_booking = false;
		}
	}
	else $tabs = array(
			'photo' => '',
			'rating' => '',
			'booking' => '',
		);

	if ( '' == $map_height )
		$map_height = 500;
	$is_fav = in_array( get_the_ID(),WyzHelpers::get_user_favorites());
	?>

	<div class="col-md-4">
                
		<!-- Page Map Right Content -->
		<div class="page-map-right-content"  style="height:<?php echo $map_height;?>px;">
		
			<div class="search-wrapper">
				<?php if ( '' == $class ) {?><a href="#" class="close-button"></a>
		<?php }?>
				<div id="map-sidebar-loading" class="loading-spinner">
					<div class="dot1 wyz-primary-color wyz-prim-color"></div>
					<div class="dot2 wyz-primary-color wyz-prim-color"></div>
				</div>
			</div>

			<div class="map-company-info fix">
				<a href="#" class="company-logo float-left"><img src="" alt=""></a>
				<div class="content fix">
					<h4 id="map-company-info-name"><a href="#"></a></h4>
					<p id="map-company-info-slogan"></p>
					<div id="map-company-info-rating" class="rating wyz-prim-color-txt"></div>
				</div>
			</div>

			<ul class="map-info-links fix">
				<?php if('on' == get_option( 'wyz_enable_favorite_business' )){ ?>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a href="#" class="ajax-click fav-bus" data-action="<?php echo $is_fav? 0 : 1;?>" data-busid="<?php echo is_singular('wyz_business')?get_the_ID():$business_id;?>">
					<i class="fa fa-heart<?php echo $is_fav ? '' : '-o';?>"></i><span><?php esc_html_e( 'Favorite', 'wyzi-business-finder' );?></span></a>
				</li>
				<?php }
				$permalink = is_singular( 'wyz_offers' ) ? get_the_permalink( $business_id ) : '';
				$rate_url = $permalink . '#' . $tabs['rating'];
				$booking_url = $permalink . '#' . $tabs['booking'];
				?>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a href="<?php echo $rate_url;?>" id="rate-bus"><i class="fa fa-star-o"></i><span><?php esc_html_e( 'Rate List', 'wyzi-business-finder' );?></span></a></li>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a class="map-share-btn" href="#"><i class="fa fa-share-alt"></i><span><?php esc_html_e( 'Share List', 'wyzi-business-finder' );?></span></a></li>
				<?php if ( $can_booking ) {?>
				<li><a href="<?php echo $booking_url;?>" id="book-bus"><i class="fa fa-calendar-o"></i><span><?php esc_html_e( 'Book List', 'wyzi-business-finder' );?></span></a></li>
				<?php }?>
				<?php if(is_singular('wyz_business')||is_singular('wyz_offers'))WyzPostShare::the_share_buttons(get_the_ID(),2,true);?>
			</ul>
			<ul class="map-info-gallery fix">
			</ul>

		</div>

	</div>
		<?php
	}





	public static function listing_global_business_map( $business_id, $def_coor ) {
		//self::the_business_map_js($business_id, true);
		self::wyz_initialize_map_scripts( 1 );
		$global_zoom = get_post_meta( get_the_ID(), 'wyz_page_map', true );

		$global_zoom = ( isset( $global_zoom['zoom'] ) ? $global_zoom['zoom'] : 12 );

		$taxonomies = WyzHelpers::get_business_categories();
		$global_map_java_data = self::wyz_get_businesses_js_data( $business_id, $def_coor, $taxonomies );
		wp_enqueue_script( 'wyz_map_cluster' );
		wp_localize_script( 'wyz_map_cluster', 'globalMap', $global_map_java_data );

		echo self::wyz_get_the_map( $business_id, '', '', $global_zoom, 1, $taxonomies );

		?>

		<!-- <div class="home-map-area section">
			<div id="home-map" style="height:<?php //echo esc_attr( get_option( 'wyz_businesses_map_height', 500 ) );?>px;"></div>
			<div id="slidable-map-sidebar"><?php //self::listing_map_sidebar();?></div>
		</div> -->
		<?php
	}
}
