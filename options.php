<?php
/**
 * Option page definition
 *
 * @package friendly-automate
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

/**
 * HTML for the Friendly option page
 */
function friendlyautomate_options_page() {
	?>
	<div>
		<h2><?php esc_html_e( 'Friendly Automate', 'friendly-automate' ); ?></h2>
		<p><?php esc_html_e( 'Add Friendly Automate tracking capabilities to your website.', 'friendly-automate' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'friendlyautomate' ); ?>
			<?php do_settings_sections( 'friendlyautomate' ); ?>
			<?php submit_button(); ?>
		</form>
		<h3><?php esc_html_e( 'Shortcode Examples:', 'friendly-automate' ); ?></h3>
		<ul>
			<li><?php esc_html_e( 'Friendly Form Embed:', 'friendly-automate' ); ?> <code>[friendlyautomate type="form" id="1"]</code></li>
			<li><?php esc_html_e( 'Friendly Dynamic Content:', 'friendly-automate' ); ?> <code>[friendlyautomate type="content" slot="slot_name"]<?php esc_html_e( 'Default Text', 'friendly-automate' ); ?>[/friendlyautomate]</code></li>
		</ul>
		<h3><?php esc_html_e( 'Quick Links', 'friendly-automate' ); ?></h3>
		<ul>
			<li>
				<a href="https://friendly.ch/contact" target="_blank"><?php esc_html_e( 'Plugin support', 'friendly-automate' ); ?></a>
			</li>
			<li>
				<a href="https://calendly.com/friendly-ch/automate-training" target="_blank"><?php esc_html_e( 'Book a free, individual training session', 'friendly-automate' ); ?></a>
			</li>
			<li>
				<a href="https://mautic.org" target="_blank"><?php esc_html_e( 'We\'re based on Mautic', 'friendly-automate' ); ?></a>
			</li>
			<li>
				<a href="https://github.com/mautic/mautic-wordpress#mautic-wordpress-plugin" target="_blank"><?php esc_html_e( 'Plugin docs', 'friendly-automate' ); ?></a>
			</li>
		</ul>
	</div>
	<?php
}

/**
 * Define admin_init hook logic
 */
function friendlyautomate_admin_init() {
	register_setting( 'friendlyautomate', 'friendlyautomate_options', 'friendlyautomate_options_validate' );

	add_settings_section(
		'friendlyautomate_main',
		__( 'Main Settings', 'friendly-automate' ),
		'friendlyautomate_section_text',
		'friendlyautomate'
	);

	add_settings_field(
		'friendlyautomate_base_url',
		__( 'Friendly Automate URL', 'friendly-automate' ),
		'friendlyautomate_base_url',
		'friendlyautomate',
		'friendlyautomate_main'
	);
	add_settings_field(
		'friendlyautomate_script_location',
		__( 'Tracking script location', 'friendly-automate' ),
		'friendlyautomate_script_location',
		'friendlyautomate',
		'friendlyautomate_main'
	);
	add_settings_field(
		'friendlyautomate_fallback_activated',
		__( 'Tracking image', 'friendly-automate' ),
		'friendlyautomate_fallback_activated',
		'friendlyautomate',
		'friendlyautomate_main'
	);

	add_settings_field(
		'friendlyautomate_track_tags_categories',
		__( 'Tags and Category tracking', 'friendly-automate' ),
		'friendlyautomate_track_tags_categories',
		'friendlyautomate',
		'friendlyautomate_main'
	);

	add_settings_field(
		'friendlyautomate_track_logged_user',
		__( 'Logged user', 'friendly-automate' ),
		'friendlyautomate_track_logged_user',
		'friendlyautomate',
		'friendlyautomate_main'
	);
}
add_action( 'admin_init', 'friendlyautomate_admin_init' );

/**
 * Section text
 */
function friendlyautomate_section_text() {
}

/**
 * Define the input field for Friendly base URL
 */
function friendlyautomate_base_url() {
	$url = friendlyautomate_option( 'base_url', '' );

	?>
	<input
		id="friendlyautomate_base_url"
		name="friendlyautomate_options[base_url]"
		size="40"
		type="text"
		placeholder="https://..."
		value="<?php echo esc_url_raw( $url, array( 'http', 'https' ) ); ?>"
	/>
	<?php
}

/**
 * Define the input field for Friendly script location
 */
function friendlyautomate_script_location() {
	$position     = friendlyautomate_option( 'script_location', '' );
	$allowed_tags = array(
		'br'   => array(),
		'code' => array(),
	);

	?>
	<fieldset id="friendlyautomate_script_location">
		<label>
			<input
				type="radio"
				name="friendlyautomate_options[script_location]"
				value="header"
				<?php
				if ( 'footer' !== $position && 'disabled' !== $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Added in the <code>wp_head</code> action.<br/>Inserts the tracking code before the <code>&lt;head&gt;</code> tag; can be slightly slower since page load is delayed until all scripts in <code><head></code> are loaded and processed.', 'friendly-automate' ), $allowed_tags ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="friendlyautomate_options[script_location]"
				value="footer"
				<?php
				if ( 'footer' === $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Embedded within the <code>wp_footer</code> action.<br/>Inserts the tracking code before the <code>&lt;/body&gt;</code> tag; slightly better for performance but may track less reliably if users close the page before the script has loaded.', 'friendly-automate' ), $allowed_tags ); ?>
		</label>
		<br />
		<label>
			<input
				type="radio"
				name="friendlyautomate_options[script_location]"
				value="disabled"
				<?php
				if ( 'disabled' === $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Visitor will not be tracked when rendering the page. Use this option to comply with GDPR regulations. If the visitor accept cookies you must execute the <code>friendlyautomate_send()</code> JavaScript function to start tracking.', 'friendly-automate' ), $allowed_tags ); ?>
			<br/>
			<?php echo wp_kses( __( 'However when using shortcodes, a tracking cookie will be added everytime even when tracking is disabled. This is because loading a Friendly Automate resource (javascript or image) generates that cookie.', 'friendly-automate' ), $allowed_tags ); ?>
		</label>
	</fieldset>
	<?php
}

/**
 * Define the input field for Friendly fallback flag
 */
function friendlyautomate_fallback_activated() {
	$flag         = friendlyautomate_option( 'fallback_activated', false );
	$allowed_tags = array(
		'br'   => array(),
		'code' => array(),
	);

	?>
	<input
		id="friendlyautomate_fallback_activated"
		name="friendlyautomate_options[fallback_activated]"
		type="checkbox"
		value="1"
		<?php
		if ( true === $flag ) :
			?>
			checked<?php endif; ?>
	/>
	<label for="friendlyautomate_fallback_activated">
		<?php esc_html_e( 'Activate the tracking image when JavaScript is disabled.', 'friendly-automate' ); ?>
		<br/>
		<?php echo wp_kses( __( 'Be warned, that the tracking image will always generate a cookie on the user browser side. If you want to control cookies and comply to GDPR, you must use JavaScript instead.', 'friendly-automate' ), $allowed_tags ); ?>
	</label>
	<?php
}

/**
 * Define the input field for Friendly category & tag tracking
 */
function friendlyautomate_track_tags_categories() {
	$flag         = friendlyautomate_option( 'tag_category_tracking', false );
	?>
	<input
		id="friendlyautomate_tag_category_tracking"
		name="friendlyautomate_options[tag_category_tracking]"
		type="checkbox"
		value="1"
		<?php
		if ( true === $flag ) :
			?>
			checked<?php endif; ?>
	/>
	<label for="friendlyautomate_tag_category_tracking">
		<?php esc_html_e( 'Track the tags and categories that your visitors are interested in.', 'friendly-automate' ); ?>
		<br/>
		<?php esc_html_e( 'This will show add WordPress categories and Tags to your website visitors as a Friendly Automate Tag. For example, if one of your posts has the category "Hosting", Friendly Automate will add this tag to the visitors who land on your post.', 'friendly-automate' ); ?>
	</label>
	<?php
}

/**
 * Define the input field for Friendly logged user tracking flag
 */
function friendlyautomate_track_logged_user() {
	$flag = friendlyautomate_option( 'track_logged_user', false );

	?>
	<input
		id="friendlyautomate_track_logged_user"
		name="friendlyautomate_options[track_logged_user]"
		type="checkbox"
		value="1"
		<?php
		if ( true === $flag ) :
			?>
			checked<?php endif; ?>
	/>
	<label for="friendlyautomate_track_logged_user">
		<?php esc_html_e( 'Track user information for logged-in users', 'friendly-automate' ); ?>
	</label>
	<?php
}

/**
 * Validate base URL input value
 *
 * @param  array $input Input data.
 * @return array
 */
function friendlyautomate_options_validate( $input ) {
	$options = get_option( 'friendlyautomate_options' );

	$input['base_url'] = isset( $input['base_url'] )
		? trim( $input['base_url'], " \t\n\r\0\x0B/" )
		: '';

	$options['base_url']        = esc_url_raw( trim( $input['base_url'], " \t\n\r\0\x0B/" ) );
	$options['script_location'] = isset( $input['script_location'] )
		? trim( $input['script_location'] )
		: 'header';
	if ( ! in_array( $options['script_location'], array( 'header', 'footer', 'disabled' ), true ) ) {
		$options['script_location'] = 'header';
	}

	$options['fallback_activated'] = isset( $input['fallback_activated'] ) && '1' === $input['fallback_activated']
		? true
		: false;
	$options['tag_category_tracking'] = isset( $input['tag_category_tracking'] ) && '1' === $input['tag_category_tracking']
		? true
		: false;
	$options['track_logged_user']  = isset( $input['track_logged_user'] ) && '1' === $input['track_logged_user']
		? true
		: false;

	return $options;
}
