<?php
/**
 * WYZI slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZISlidersFactory' ) ) {

	class WYZISlidersFactory {

		private static $template_type = '';
		private static $template_path = '';
		private static $featured_path = '';

		private static $categories_slider = NULL;
		private static $locations_slider = NULL;
		private static $rec_added_slider = NULL;
		private static $featured_slider = NULL;
		private static $offers_slider = NULL;

		private static function init_template_type() {
			if ( '' !== self::$template_type )
				return;
			self::$template_type = 1;
			if ( function_exists( 'wyz_get_theme_template' ) ) {
					self::$template_type = wyz_get_theme_template();
			}
			self::$template_path = plugin_dir_path( __FILE__ );
		}

		public static function the_categories_slider( $slider_attr = array() ) {
			if ( NULL === self::$categories_slider ) {
				self::init_template_type();
				$categories_path = self::$template_path . 'categories/categories-' . self::$template_type . '.php';
				if ( ! is_file( $categories_path ) )
					return;
				require_once( $categories_path );
			}
			self::$categories_slider = new WYZICategoriesSlider( $slider_attr );
			return self::$categories_slider->the_categories_slider();
		}

		public static function the_locations_slider( $slider_attr = array() ) {
			if ( NULL === self::$locations_slider ) {
				self::init_template_type();
				$locations_path = self::$template_path . 'locations/locations-' . self::$template_type . '.php';
				if ( ! is_file( $locations_path ) )
					return;
				require_once( $locations_path );
			}
			self::$locations_slider = new WYZILocationsSlider( $slider_attr );
			return self::$locations_slider->the_locations_slider();
		}

		public static function the_rec_added_slider( $slider_attr = array() ) {
			if ( NULL === self::$rec_added_slider ) {
				self::init_template_type();
				$rec_added_path = self::$template_path . 'recently-added/recently-added-' . self::$template_type . '.php';
				if ( ! is_file( $rec_added_path ) )
					return;
				require_once( $rec_added_path );
			}
			self::$rec_added_slider = new WYZIRecentlyAddedSlider( $slider_attr );
			return self::$rec_added_slider->the_rec_added_slider();
		}

		public static function the_featured_slider( $slider_attr = array() ) {
			if ( NULL === self::$featured_slider ) {
				self::init_template_type();
				$featured_path = self::$template_path . 'featured/featured-' . self::$template_type . '.php';
				if ( ! is_file( $featured_path ) )
					return;
				require_once( $featured_path );
			}
			self::$featured_slider = new WYZIFeaturedSlider( $slider_attr );
			return self::$featured_slider->the_featured_slider();
		}

		public static function the_offers_slider( $slider_attr = array() ) {
			if ( NULL === self::$offers_slider ) {
				self::init_template_type();
				$offers_path = self::$template_path . 'offers/offers-' . self::$template_type . '.php';
				if ( ! is_file( $offers_path ) )
					return;
				require_once( $offers_path );
			}
			self::$offers_slider = new WYZIOffersSlider( $slider_attr );
			return self::$offers_slider->the_offers_slider();
		}

	}
}