<?php
/**
 * Plugin Name: Storefront Beta Tester
 * Plugin URI: https://github.com/BurlesonBrad/storefront-beta
 * Description: Run bleeding edge versions of Storefront from WooTheme's Storefront Github repo. This will replace your installed version of Storefront with the latest tagged release on Github - use with caution, and not on production sites. You have been warned. This is for anyone building themes based on Storefront, and anyone creating child themes from Storefront.
 * Version: 0.0.1
 * Author: Brad Griffin
 * Author URI: https://bradgriffin.me/
 * Requires at least: 4.2
 * Tested up to: 4.4
 *
 * Based on WP_GitHub_Updater by Joachim Kudish.
 * Based on Mike's WooCommerce Updater Plugin here: https://github.com/woothemes/woocommerce-beta-tester
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Confirm woocommerce is at least installed before doing anything
 * Curiously, developers are discouraged from using WP_PLUGIN_DIR and not given a
 * function with which to get the plugin directory, so this is what we have to do
 */
 
//this would need to be changed to check not only for woocommerce.php but for storefront as well, right? //
if ( ! file_exists( trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'woocommerce/woocommerce.php' ) ) :

	add_action( 'admin_notices', 'wcbt_woocoommerce_not_installed' );

elseif ( ! class_exists( 'Storefront_Beta_Tester' ) ) : //changed from WC_Beta_Tester to what Mike said to use//

	/**
	 * Storefront_Beta_Tester Main Class
	 */
	class Storefront_Beta_Tester {  //changed from WC_Beta_Tester to what Mike said to use//

		/** Config */
		private $config = array();

		/** Github Data */
		protected static $_instance = null;

		/**
		 * Main Instance
		 */
		public static function instance() {
			return self::$_instance = is_null( self::$_instance ) ? new self() : self::$_instance;
		}

		/**
		 * Ran on activation to flush update cache
		 */
		 // not too sure: is this part even needed to pull changes from git to a working instance of Storefront? //
		public static function activate() {
			delete_site_transient( 'update_plugins' );
			delete_site_transient( 'woocommerce_latest_tag' );
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->config = array(
				'plugin_file'        => 'woocommerce/woocommerce.php', //this would be the theme file? //
				'slug'               => 'woocommerce', //this would be what exactly? the slug name of 'storefront' ? //
				'proper_folder_name' => 'woocommerce', //same here: This would need to be 'storefront' since that's the name of the folder, right? //
				'api_url'            => 'https://api.github.com/repos/woothemes/woocommerce', // this line would be 'https://api.github.com/repos/woothemes/storefront' //
				'github_url'         => 'https://github.com/woothemes/woocommerce', // likewise this line should read 'https://github.com/woothemes/storefront' //
				'requires'           => '4.4', //not sure//
				'tested'             => '4.4' //not sure//
			);
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );
			add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 10, 3 ); // 'get_plugin_info' is mentioned here and on line 252. These would/should be changed to 'get_theme_info' right? // 
			add_filter( 'upgrader_source_selection', array( $this, 'upgrader_source_selection' ), 10, 3 ); // used again on line 281 //
		}

		/**
		 * Update args
		 * @return array
		 */
		public function set_update_args() { these are all ars that set what the function of 'set_update_args' consist of, right? If named the same, it might look weird, but in function if these were left alone things would still update, right? //
			$plugin_data                    = $this->get_plugin_data(); // 'get_plugin_data' is also referenced again in lines 203 & 204. This line only sets the name, right? It's 203 & 204 that need to be modified, right? //
			$this->config[ 'plugin_name' ]  = $plugin_data['Name'];
			$this->config[ 'version' ]      = $plugin_data['Version'];
			$this->config[ 'author' ]       = $plugin_data['Author'];
			$this->config[ 'homepage' ]     = $plugin_data['PluginURI'];
			$this->config[ 'new_version' ]  = $this->get_latest_prerelease();
			$this->config[ 'last_updated' ] = $this->get_date();
			$this->config[ 'description' ]  = $this->get_description();
			$this->config[ 'zip_url' ]      = 'https://github.com/woothemes/woocommerce/zipball/' . $this->config[ 'new_version' ];
		}

		/**
		 * Check wether or not the transients need to be overruled and API needs to be called for every single page load
		 *
		 * @return bool overrule or not
		 */
		public function overrule_transients() {
			return ( defined( 'Storefront_Beta_Tester_FORCE_UPDATE' ) && Storefront_Beta_Tester_FORCE_UPDATE );
		}

		/**
		 * Get New Version from GitHub
		 *
		 * @since 1.0
		 * @return int $version the version number
		 */
		public function get_latest_prerelease() {
			$tagged_version = get_site_transient( md5( $this->config['slug'] ) . '_latest_tag' );  // '_latest_tag' is the version number that we see in git, right? //

			if ( $this->overrule_transients() || empty( $tagged_version ) ) {

				$raw_response = wp_remote_get( trailingslashit( $this->config['api_url'] ) . 'releases' );

				if ( is_wp_error( $raw_response ) ) {
					return false;
				}

				$releases       = json_decode( $raw_response['body'] );  //this decodes the api from git, right? //
				$tagged_version = false;

				if ( is_array( $releases ) ) {
					foreach ( $releases as $release ) {
						if ( $release->prerelease ) {
							$tagged_version = $release->tag_name;
							break;
						}
					}
				}

				// refresh every 6 hours
				if ( ! empty( $tagged_version ) ) {
					set_site_transient( md5( $this->config['slug'] ) . '_latest_tag', $tagged_version, 60*60*6 ); // this sets how often it will check for any updates: 60 seconds x 60 minutes x 6 hours & then sets that as the last number in the single quote //
				}
			}

			return $tagged_version;
		}

		/**
		 * Get GitHub Data from the specified repository
		 *
		 * @since 1.0
		 * @return array $github_data the data
		 */
		 // this part pulls the info from github //
		public function get_github_data() {
			if ( ! empty( $this->github_data ) ) {
				$github_data = $this->github_data;
			} else {
				$github_data = get_site_transient( md5( $this->config['slug'] ) . '_github_data' );

				if ( $this->overrule_transients() || ( ! isset( $github_data ) || ! $github_data || '' == $github_data ) ) {
					$github_data = wp_remote_get( $this->config['api_url'] );

					if ( is_wp_error( $github_data ) ) {
						return false;
					}

					$github_data = json_decode( $github_data['body'] );

					// refresh every 6 hours
					set_site_transient( md5( $this->config['slug'] ) . '_github_data', $github_data, 60*60*6 );
				}

				// Store the data in this class instance for future calls
				$this->github_data = $github_data;
			}

			return $github_data;
		}
		/**
		 * Get update date
		 *
		 * @since 1.0
		 * @return string $date the date
		 */
		 // this part pulls "when" it was updated //
		public function get_date() {
			$_date = $this->get_github_data();
			return ! empty( $_date->updated_at ) ? date( 'Y-m-d', strtotime( $_date->updated_at ) ) : false;
		}

		/**
		 * Get plugin description
		 *
		 * @since 1.0
		 * @return string $description the description
		 */
		 // this part pulls any description info//
		public function get_description() {
			$_description = $this->get_github_data();
			return ! empty( $_description->description ) ? $_description->description : false;
		}

		/**
		 * Get Plugin data
		 *
		 * @since 1.0
		 * @return object $data the data
		 */
		 // this part needs to be modified from talking about a plugin to referencing a theme //
		public function get_plugin_data() { //if we change or modify line 82, then it needs to be modified here as well //
			return get_plugin_data( WP_PLUGIN_DIR . '/' . $this->config['plugin_file'] ); // this is referencing the theme again. It needs to reference the theme though, right? //
		}

		/**
		 * Hook into the plugin update check and connect to GitHub
		 *
		 * @since 1.0
		 * @param object  $transient the plugin data transient
		 * @return object $transient updated plugin data transient
		 */
		 // although line 208 say 'plugin update', the following lines still work & function the same, right? or does it need to be modified b/c we're updating a theme? //
		public function api_check( $transient ) {
			// Check if the transient contains the 'checked' information
			// If not, just return its value without hacking it
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			// Clear our transient
			delete_site_transient( md5( $this->config['slug'] ) . '_latest_tag' );

			// Update tags
			$this->set_update_args();

			// check the version and decide if it's new
			$update = version_compare( $this->config['new_version'], $this->config['version'], '>' );

			if ( $update ) {
				$response              = new stdClass;
				$response->plugin      = $this->config['slug'];
				$response->new_version = $this->config['new_version'];
				$response->slug        = $this->config['slug'];
				$response->url         = $this->config['github_url'];
				$response->package     = $this->config['zip_url'];

				// If response is false, don't alter the transient
				if ( false !== $response ) {
					$transient->response[ $this->config['plugin_file'] ] = $response;
				}
			}

			return $transient;
		}

		/**
		 * Get Plugin info
		 *
		 * @since 1.0
		 * @param bool    $false  always false
		 * @param string  $action the API function being performed
		 * @param object  $args   plugin arguments
		 * @return object $response the plugin info
		 */
		public function get_plugin_info( $false, $action, $response ) { // should 'get_plugin_info' become 'get_theme_info' ? //
			// Check if this call API is for the right plugin
			if ( ! isset( $response->slug ) || $response->slug != $this->config['slug'] ) {
				return false;
			}

			// Update tags
			$this->set_update_args(); //this is one of those lines that SETs the parameters of the "thing". In this case, the "thing" is 'set_update_args' and it is SETing the slug, plugin, name, version, and whatnot //

			$response->slug          = $this->config['slug'];
			$response->plugin        = $this->config['slug'];
			$response->name          = $this->config['plugin_name'];
			$response->plugin_name   = $this->config['plugin_name'];
			$response->version       = $this->config['new_version'];
			$response->author        = $this->config['author'];
			$response->homepage      = $this->config['homepage'];
			$response->requires      = $this->config['requires'];
			$response->tested        = $this->config['tested'];
			$response->downloaded    = 0;
			$response->last_updated  = $this->config['last_updated'];
			$response->sections      = array( 'description' => $this->config['description'] );
			$response->download_link = $this->config['zip_url'];

			return $response;
		}

		/**
		 * Rename the downloaded zip
		 */
		public function upgrader_source_selection( $source, $remote_source, $upgrader ) { // should this be changed or modified or does it function just the same? //
			global $wp_filesystem;

			if ( strstr( $source, '/woothemes-woocommerce-' ) ) {
				$corrected_source = trailingslashit( $remote_source ) . trailingslashit( $this->config[ 'proper_folder_name' ] );

				if ( $wp_filesystem->move( $source, $corrected_source, true ) ) {
					return $corrected_source;
				} else {
					return new WP_Error();
				}
			}

			return $source;
		}
	}

	register_activation_hook( __FILE__, array( 'Storefront_Beta_Tester', 'activate' ) ); //changed from WC_Beta_Tester to what Mike said to use//

	add_action( 'admin_init', array( 'Storefront_Beta_Tester', 'instance' ) ); //changed from WC_Beta_Tester to what Mike said to use//

endif;


/**
* WooCommerce Not Installed Notice
**/
// this should be changed or modified so that it checks for Storefront first, right? Then if Storefront isn't installed it should crank out a message about Storefront instead of WooCommerce
if ( ! function_exists( 'wcbt_woocoommerce_not_installed' ) ) { // this would be changed to 'sfbt_storefront_not_installed' right? //

	function wcbt_woocoommerce_not_installed() { // this would be changed to 'sfbt_storefront_not_installed' right? //

		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Beta Tester requires %s to be installed.', 'woocommerce-beta-tester' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';
		
		/** modified line would be something like this
		 echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Beta Tester requires %s to be installed.', 'storefront-beta' ), '<a href="https://github.com/BurlesonBrad/storefront-beta" target="_blank">Storefront Beta</a>'  ) . '</p></div>';
		**/
	}

}
