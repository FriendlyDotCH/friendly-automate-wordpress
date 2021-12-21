<?php
/**
 * Plugin Name: Friendly Automate
 * Plugin URI: https://friendly.ch/
 * Contributors: friendly,mautic,hideokamoto,shulard,escopecz,dbhurley,macbookandrew,bradycargle,gabcarvalhogama
 * Description: This plugin will allow you to add Friendly tracking to your site
 * Version: 1.1.0
 * Requires at least: 4.7
 * Tested up to: 5.8.2
 * Author: Friendly
 * Author URI: Friendly.ch
 * Text Domain: friendly-automate
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package friendly-automate
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}


// Store plugin directory.
define( 'FRIENDLYAUTOMATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Store plugin main file path.
define( 'FRIENDLYAUTOMATE_PLUGIN_FILE', __FILE__ );

add_action( 'admin_menu', 'friendlyautomate_settings' );
add_action( 'plugins_loaded', 'friendlyautomate_injector' );

require_once FRIENDLYAUTOMATE_PLUGIN_DIR . '/shortcodes.php';

/**
 * Declare option page
 */
function friendlyautomate_settings() {
	include_once FRIENDLYAUTOMATE_PLUGIN_DIR . '/options.php';

	add_options_page(
		__( 'Friendly Automate Settings', 'friendly-automate' ),
		__( 'Friendly Automate', 'friendly-automate' ),
		'manage_options',
		'friendly-automate',
		'friendlyautomate_options_page'
	);
}

/**
 * Settings Link in the ``Installed Plugins`` page
 *
 * @param array $links array of plugin action links.
 *
 * @return array
 */
function friendlyautomate_plugin_actions( $links ) {
	if ( function_exists( 'admin_url' ) ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=friendlyautomate' ),
			__( 'Settings' )
		);
		// Add the settings link before other links.
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( FRIENDLYAUTOMATE_PLUGIN_FILE ), 'friendlyautomate_plugin_actions', 10, 2 );

/**
 * Retrieve one of the friendlyautomate options but sanitized
 *
 * @param  string $option  Option name to be retrieved (base_url, script_location).
 * @param  mixed  $default Default option value return if not exists.
 *
 * @return string
 *
 * @throws InvalidArgumentException Thrown when the option name is not given.
 */
function friendlyautomate_option( $option, $default = null ) {
	$options = get_option( 'friendlyautomate_options' );

	switch ( $option ) {
		case 'script_location':
			return ! isset( $options[ $option ] ) ? 'header' : $options[ $option ];
		case 'fallback_activated':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : true;
		case 'track_tags_categories':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : true;
		case 'track_logged_user':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : false;
		default:
			if ( ! isset( $options[ $option ] ) ) {
				if ( isset( $default ) ) {
					return $default;
				}

				throw new InvalidArgumentException( 'You must give a valid option name !' );
			}

			return $options[ $option ];
	}
}

/**
 * Apply JS tracking to the right place depending script_location.
 *
 * @return void
 */
function friendlyautomate_injector() {
	$script_location = friendlyautomate_option( 'script_location' );
	if ( 'header' === $script_location ) {
		add_action( 'wp_head', 'friendlyautomate_inject_script' );
	} else {
		add_action( 'wp_footer', 'friendlyautomate_inject_script' );
	}

	if ( 'disabled' !== $script_location && true === friendlyautomate_option( 'fallback_activated', false ) ) {
		add_action( 'wp_footer', 'friendlyautomate_inject_noscript' );
	}
}

/**
 * Generate the script URL to be used outside of the plugin when
 * necessary
 *
 * @return string
 */
function friendlyautomate_base_script() {
	$base_url = friendlyautomate_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	return $base_url . '/mtc.js';
}

/**
 * Writes Tracking JS to the HTML source
 *
 * @return void
 */
function friendlyautomate_inject_script() {
	// Load the tracking library mtc.js if it is not disabled.
	$base_url        = friendlyautomate_base_script();
	$script_location = friendlyautomate_option( 'script_location' );
	$attrs           = friendlyautomate_get_tracking_attributes();
	?>
	<script type="text/javascript" >
		function friendlyautomate_send(){
			if ('undefined' === typeof mt) {
				if (console !== undefined) {
					console.warn('Friendly Automate: mt not defined. Did you load mtc.js ?');
				}
				return false;
			}
			// Add the mt('send', 'pageview') script with optional tracking attributes.
			mt('send', 'pageview', <?php echo count( $attrs ) > 0 ? wp_json_encode( $attrs ) : ''; ?>);
		}

	<?php
	// Friendly is not configured, or user disabled automatic tracking on page load (GDPR).
	if ( ! empty( $base_url ) && 'disabled' !== $script_location ) :
		?>
		(function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
			w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
			m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
		})(window,document,'script','<?php echo esc_url( $base_url ); ?>','mt');

		friendlyautomate_send();
		<?php
	endif;
	?>
	</script>
	<?php
}

/**
 * Writes Tracking image fallback to the HTML source
 * This is a separated function because <noscript> tags are not allowed in header !
 *
 * @return void
 */
function friendlyautomate_inject_noscript() {
	$base_url = friendlyautomate_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	$url_query = friendlyautomate_get_url_query();
	$payload   = rawurlencode( base64_encode( serialize( $url_query ) ) );
	?>
	<noscript>
		<img src="<?php echo esc_url( $base_url ); ?>/mtracking.gif?d=<?php echo esc_attr( $payload ); ?>" style="display:none;" alt="<?php echo esc_attr__( 'Mautic Tags', 'friendly-automate' ); ?>" />
	</noscript>
	<?php
}

/**
 * Builds and returns additional data for URL query
 *
 * @return array
 */
function friendlyautomate_get_url_query() {
	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

	$attrs = friendlyautomate_get_tracking_attributes();

	$attrs['language']   = get_locale();
	$attrs['page_url']   = $current_url;
	$attrs['page_title'] = function_exists( 'wp_get_document_title' )
		? wp_get_document_title()
		: wp_title( '&raquo;', false );
	$attrs['referrer']   = function_exists( 'wp_get_raw_referer' )
		? wp_get_raw_referer()
		: null;
	if ( false === $attrs['referrer'] ) {
		$attrs['referrer'] = $current_url;
	}

	return $attrs;
}

/**
 * Create custom query parameters to be injected inside tracking
 *
 * @return array
 */
function friendlyautomate_get_tracking_attributes() {
	$attrs = friendlyautomate_get_user_query();

	/**
	 * Update / add data to be sent within tracker
	 *
	 * Default data only contains the 'language' key but every added key to the
	 * array will be sent to Friendly.
	 *
	 * @since 2.1.0
	 *
	 * @param array $attrs Attributes to be filters, default ['language' => get_locale()]
	 */
	return apply_filters( 'friendlyautomate_tracking_attributes', $attrs );
}

/**
 * Extract logged user informations to be sent to tracker
 *
 * @return array
 */
function friendlyautomate_get_user_query() {
	$attrs = array();

	if (
		true === friendlyautomate_option( 'track_logged_user', false ) &&
		is_user_logged_in()
	) {
		$current_user       = wp_get_current_user();
		$attrs['email']     = $current_user->user_email;
		$attrs['firstname'] = $current_user->user_firstname;
		$attrs['lastname']  = $current_user->user_lastname;

		// Following fields have to be created manually and the fields must match these names.
		$attrs['wp_user']              = $current_user->user_login;
		$attrs['wp_alias']             = $current_user->display_name;
		$attrs['wp_registration_date'] = date(
			'Y-m-d',
			strtotime( $current_user->user_registered )
		);
	}

	return $attrs;
}

// Add tags and category tracking

if ( true === friendlyautomate_option( 'tag_category_tracking', false ) ) {
	// Add WordPress post categories and tags to Friendly Automate as tags

	function wordpress_post_meta_friendlyautomate( $attrs ) {

		// remove filter so it is not execute twice (don't know, why that happens)
		remove_filter( 'friendlyautomate_tracking_attributes', 'wordpress_post_meta_friendlyautomate' );


		// check if current element is a regular blog post - only these have categories and tags
		if (is_singular( 'post' )) {

			// add WordPress categories as Friendly Automate tags
			foreach(get_the_category() as $wp_category) {

			$tags[] = "wordpress-category-" . $wp_category->cat_name;

			} 

			// add WordPress tags as Friendly Automate tags
			foreach(get_the_tags() as $wp_tag) {

			$tags[] = "wordpress-tag-" . $wp_tag->name;

			} 

			// clean Friendly Automate tag names
		foreach ($tags as $key => $value) {

			$value = str_replace(' ', '-', $value); // Replaces all spaces with hyphens
			$value = strtr($value,array('Ä' => 'Ae','Ö' => 'Oe','Ü' => 'Ue', 'ä' => 'ae','ö' => 'oe', 'ü' => 'ue','ß' => 'ss')); // replace German umlauts with best equivalent
			$value = iconv("utf-8","ascii//TRANSLIT",$value); // replace all other umlauts using the locale setting from WordPress 
			$value = preg_replace('/[^A-Za-z0-9\-]/', '', $value); // Removes special characters
			$value = strtolower($value); // set to lowercase

			$tags[$key] = $value;

			}

			// convert tags array to string
			$tags_string = implode(",", $tags);

			// pass tags to the Friendly Automate tracking code
			$attrs['tags'] = $tags_string;

		}

		return $attrs;

	}

	add_filter( 'friendlyautomate_tracking_attributes', 'wordpress_post_meta_friendlyautomate');
}

// Elementor integration

add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	require "Elementic_Form_Action.php";


	// Instantiate the action class
	$elementic = new Elementic_Form_Action();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $elementic->get_name(), $elementic );
});