<?php
/**
 * Plugin Name:       Custom Image Attributes
 * Plugin URI:        https://liveseo.co.uk/tools/custom-image-attributes/
 * Description:       Add custom image attributes to any image. Great for adding attributes to images for lightboxes, seo, analytics & more.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.3
 * Author:            Live SEO
 * Author URI:        https://liveseo.co.uk/about/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://liveseo.co.uk/tools/custom-image-attributes/
 * Text Domain:       custom-image-attributes
 * Domain Path:       /languages
 */

if(!defined('ABSPATH')){
	exit;
}

require_once "include/index.php";

/**
 * The Main Class Of Plugin
 */
final class CustomImageAttributes
{
	// Class construction
	private function __construct()
	{
		$this->define_function();

		add_action('plugins_loaded', [$this, 'init_plugin']);
		/**
 			* Load plugin textdomain.
		*/
		add_action( 'init', [$this, 'CustomImageAttributes_load_textdomain'] );

		add_filter( 'plugin_action_links_custom-image-attributes/index.php',  [$this, 'custom_image_attributes_settings_link'] );		

	}

	/*
		Single instence 
	*/
	public static function init(){
		static $instance = false;

		if (!$instance) {
			$instance = new self();
		}

		return $instance;
	}


	public function define_function(){
		define("CustomImageAttributes_FILE", __FILE__);
		define("CustomImageAttributes_PATH", __DIR__);
		define("CustomImageAttributes_URL", plugins_url('', CustomImageAttributes_FILE));
		define("CustomImageAttributes_ASSETS", CustomImageAttributes_URL.'/assets');
	}

	public function init_plugin(){
		new CustomImageAttributes_SettingsPage();										
	}

	public function CustomImageAttributes_load_textdomain() {
	  load_plugin_textdomain( 'custom-image-attributes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	public function custom_image_attributes_settings_link( $links ) {
		// Build and escape the URL.
		$url = esc_url( add_query_arg(
			'page',
			'custom-image-attributes-setting',
			get_admin_url() . 'admin.php'
		) );
		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		// Adds the link to the end of the array.
		array_push(
			$links,
			$settings_link
		);
		return $links;
	}
}

/*
Initialize the main plugin
*/
function CustomImageAttributes_init(){
	return CustomImageAttributes::init();
}

/*
Active Plugin
*/
CustomImageAttributes_init();