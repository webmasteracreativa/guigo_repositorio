<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @since      2.0.0      Added advanced visibility settings
 * @package    WhatsAppMe
 * @subpackage WhatsAppMe/admin
 * @author     Creame <hola@crea.me>
 */
class WhatsAppMe_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The setings of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $settings    The current settings of this plugin.
	 */
	private $settings;

	/**
	 * Use International Telephone Input library (https://intl-tel-input.com/)
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      boolean    $enhanced_phone    Use enhanced phone input.
	 */
	private $enhanced_phone;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name    = $plugin_name;
		$this->version        = $version;
		$this->enhanced_phone = true;
		$this->get_settings();

	}

	/**
	 * Get all settings or set defaults
	 *
	 * @since    1.0.0
	 * @since    2.0.0     Added visibility setting
	 * @since    2.1.0     Added message_badge
	 */
	private function get_settings() {

		$this->settings = array(
			'telephone'     => '',
			'mobile_only'   => 'no',
			'message_text'  => '',
			'message_delay' => 10000,
			'message_badge' => 'no',
			'message_send'  => '',
			'position'      => 'right',
			'visibility'    => array( 'all' => 'yes' ),
		);

		$saved_settings = get_option( 'whatsappme' );

		if ( is_array( $saved_settings ) ) {
			// clean unused saved settings
			$saved_settings = array_intersect_key( $saved_settings, $this->settings );
			// merge defaults with saved settings
			$this->settings = array_merge( $this->settings, $saved_settings );
		}

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.2.0
	 * @since    2.0.0     Added whatsappme-admin styles
	 * @param    string    $hook       The name of the page.
	 * @return   void
	 */
	public function enqueue_styles($hook) {

		if ( 'settings_page_whatsappme' == $hook && $this->enhanced_phone ) {
			wp_enqueue_style( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.3/css/intlTelInput.css', array(), null, 'all' );
			wp_enqueue_style( 'whatsappme-admin', plugin_dir_url( __FILE__ ) . 'css/whatsappme.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.2.0
	 * @param    string    $hook       The id of the page.
	 * @return   void
	 */
	public function enqueue_scripts($hook) {

		if ( 'settings_page_whatsappme' == $hook && $this->enhanced_phone ) {
			wp_enqueue_script( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.3/js/intlTelInput.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'whatsappme-admin', plugin_dir_url( __FILE__ ) . 'js/whatsappme.js', array( 'intl-tel-input' ), $this->version, true );
		}

	}

	/**
	 * Initialize the settings for wordpress admin
	 * From v1.2.0 also set filter to disable enhanced phone input
	 *
	 * @since    1.0.0
	 * @since    2.0.0     Added tabs for general and Advanced settings
	 * @access   public
	 * @return   void
	 */
	public function settings_init(){

		register_setting( 'whatsappme', 'whatsappme', array( $this, 'settings_validate' ) );

		/*
		 * General Settings
		 */

		add_settings_section( 'whatsappme_section_general', null, array( $this, 'section_text' ), 'whatsappme' );

		$general_fields = array(
			'telephone'      => __( 'Telephone', 'creame-whatsapp-me' ),
			'mobile_only'    => __( 'Mobile only', 'creame-whatsapp-me' ),
			'message_text'   => __( 'Call to action', 'creame-whatsapp-me' ),
			'message_delay'  => __( 'Delay', 'creame-whatsapp-me' ),
			'message_badge'  => __( 'Button Badge', 'creame-whatsapp-me' ),
			'message_send'   => __( 'Message', 'creame-whatsapp-me' ),
			'position'       => __( 'Position on screen', 'creame-whatsapp-me' ),
		);

		foreach ( $general_fields as $key => $value ) {
			add_settings_field( 'whatsappme_' . $key, $value, array( $this, 'field_' . $key ), 'whatsappme', 'whatsappme_section_general' );
		}

		/*
		 * Advanced Settings / All
		 */

		add_settings_section( 'whatsappme_section_advanced_all', null, array( $this, 'section_text' ), 'whatsappme' );
		add_settings_field( 'whatsappme_view_all', __( 'Global', 'creame-whatsapp-me' ), array( $this, 'field_view_all' ), 'whatsappme', 'whatsappme_section_advanced_all' );

		/*
		 * Advanced Settings / WP
		 */

		add_settings_section( 'whatsappme_section_advanced_wp', null, array( $this, 'section_text' ), 'whatsappme' );

		$advanced_fields = 	array(
			'front_page' => __( 'Front Page', 'creame-whatsapp-me' ),
			'blog_page'  => __( 'Blog Page', 'creame-whatsapp-me' ),
			'404_page'   => __( '404 Page', 'creame-whatsapp-me' ),
			'search'     => __( 'Search Results', 'creame-whatsapp-me' ),
			'archive'    => __( 'Archives', 'creame-whatsapp-me' ),
			'date'       => '— ' . __( 'Date Archives', 'creame-whatsapp-me' ),
			'author'     => '— ' . __( 'Author Archives', 'creame-whatsapp-me' ),
			'singular'   => __( 'Singular', 'creame-whatsapp-me' ),
			'page'       => '— ' . __( 'Page', 'creame-whatsapp-me' ),
			'post'       => '— ' . __( 'Post', 'creame-whatsapp-me' ),
		);

		// If isn't set Blog Page or is the same than Front Page unset blog_page option
		if ( get_option( 'show_on_front' ) == 'posts' || get_option( 'page_for_posts' ) == 0 ) {
			unset( $advanced_fields['blog_page'] );
		}

		foreach ( $advanced_fields as $key => $value ) {
			add_settings_field( 'whatsappme_view_' . $key, $value, array( $this, 'field_view' ), 'whatsappme', 'whatsappme_section_advanced_wp', array( 'field' => $key ) );
		}

		/*
		 * Advanced Settings / Woocommerce
		 */

		if ( class_exists( 'WooCommerce' ) ) {

			add_settings_section( 'whatsappme_section_advanced_woo', 'WooCommerce', array( $this, 'section_text' ), 'whatsappme' );

			$woo_fields = 	array(
				'woocommerce'  => __( 'Shop', 'creame-whatsapp-me' ),
				// 'shop'         => __( 'Shop', 'creame-whatsapp-me' ),
				'product'      => '— ' . __( 'Product Page', 'creame-whatsapp-me' ),
				'cart'         => '— ' . __( 'Cart', 'creame-whatsapp-me' ),
				'checkout'     => '— ' . __( 'Checkout', 'creame-whatsapp-me' ),
				'account_page' => '— ' . __( 'My Account', 'creame-whatsapp-me' ),
			);

			foreach ( $woo_fields as $key => $value ) {
				add_settings_field( 'whatsappme_view_' . $key, $value, array( $this, 'field_view' ), 'whatsappme', 'whatsappme_section_advanced_woo', array( 'field' => $key ) );
			}
		}

		/*
		 * Advanced Settings / Custom Post Types
		 */

		$custom_post_types = array_keys( get_post_types( array( 'has_archive' => true ), 'names' ) );
		// Product CPT already defined in WooCommerce section
		if ( class_exists( 'WooCommerce' ) ) {
			$custom_post_types = array_diff( $custom_post_types, array( 'product' ) );
		}

		// Add/remove posts types on advanced settings
		$custom_post_types = apply_filters( 'whatsappme_custom_post_types', $custom_post_types );

		if ( count( $custom_post_types ) ) {

			add_settings_section( 'whatsappme_section_advanced_cpt', __( 'Custom Post Types', 'creame-whatsapp-me' ), array( $this, 'section_text' ), 'whatsappme' );

			foreach ( $custom_post_types as $custom_post_type ) {

				$post_type = get_post_type_object( $custom_post_type );
				$post_type_name = function_exists( 'mb_convert_case' ) ?
					mb_convert_case( $post_type->labels->name, MB_CASE_TITLE ) :
					strtolower( $post_type->labels->name );

				add_settings_field( 'whatsappme_view_cpt_' . $custom_post_type, $post_type_name, array( $this, 'field_view' ), 'whatsappme', 'whatsappme_section_advanced_cpt', array( 'field' => 'cpt_' . $custom_post_type ) );
			}
		}

		add_settings_section( 'whatsappme_section_end', null, array( $this, 'section_text' ), 'whatsappme' );

		$this->enhanced_phone = apply_filters( 'whatsappme_enhanced_phone', $this->enhanced_phone );
	}

	/**
	 * Validate settings, clean and set defaults before save
	 *
	 * @since    1.0.0
	 * @since    2.0.0    Added visibility setting
	 * @since    2.1.0    Added message_badge
	 * @param    array    $input       contain keys 'id', 'title' and 'callback'.
	 * @return   array
	 */
	public function settings_validate($input) {

		$input['mobile_only']   = isset( $input['mobile_only'] ) ? 'yes' : 'no';
		$input['telephone']     = $this->clean_input( $input['telephone'] );
		$input['message_text']  = $this->clean_input( $input['message_text'] );
		$input['message_delay'] = intval( $input['message_delay'] );
		$input['message_badge'] = isset( $input['message_badge'] ) ? 'yes' : 'no';
		$input['message_send']  = $this->clean_input( $input['message_send'] );
		$input['position']      = $input['position'] != 'left' ? 'right' : 'left';
		$input['visibility']    = array_filter( $input['view'], function($v) { return $v == 'yes' || $v == 'no'; } );
		unset( $input['view'] );

		add_settings_error( 'whatsappme', 'settings_updated', __( 'Settings saved', 'creame-whatsapp-me' ), 'updated' );

		return $input;
	}

	/**
	 * Section HTML output
	 *
	 * @since    1.0.0
	 * @since    2.0.0    Now accept $args and echo the appropriate section html
	 * @param    array    $args       Section info.
	 * @return   void
	 */
	public function section_text($args) {
		switch ( $args['id'] ) {
			case 'whatsappme_section_general':
				echo '<h2 class="nav-tab-wrapper wp-clearfix">' .
					'<a href="#tab-general" class="nav-tab nav-tab-active">' . __( 'General', 'creame-whatsapp-me' ) . '</a>' .
					'<a href="#tab-advanced" class="nav-tab">' . __( 'Advanced', 'creame-whatsapp-me' ) . '</a>' .
					'</h2>' .
					'<div class="tabs">' .
					'<div id="tab-general" class="tab tab-active">' .
					'<p>' . __( 'From here you can configure the behavior of the WhatsApp button on your site.', 'creame-whatsapp-me' ) . '</p>';
				break;

			case 'whatsappme_section_advanced_all':
				echo '</div><div id="tab-advanced" class="tab">' .
					'<p>' . __( 'From here you can configure on which pages the WhatsApp button will be visible.', 'creame-whatsapp-me' ) .
					' <a href="#" class="whatsappme_view_reset">' . __( 'Restore default visibility', 'creame-whatsapp-me' ) . '</a></p>';
				break;

			case 'whatsappme_section_advanced_wp':
				echo '<hr>';
				break;

			case 'whatsappme_section_end':
				echo '</div></div><!-- end tabs -->';
				break;

			default:
				break;
		}
	}

	/**
	 * Field 'telephone' output
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function field_telephone() {
		$name = $this->enhanced_phone ? '' : 'whatsappme[telephone]';
		echo '<input id="whatsappme_phone" name="' . $name . '" value="' . $this->settings['telephone'] . '" type="text" style="width:15em;height:28px;line-height:1;">' .
			'<p class="description">' . __( "Contact phone number. <strong>The button will not be shown if it's empty.</strong>", 'creame-whatsapp-me' ) . '</p>';
	}

	/**
	 * Field 'message_text' output
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function field_message_text() {
		echo '<textarea name="whatsappme[message_text]" rows="3" class="regular-text" placeholder="' . esc_attr__( "Hello 👋\nCan we help you?", 'creame-whatsapp-me' ) . '">' . $this->settings['message_text'] . '</textarea>' .
			'<p class="description">' . __( '<strong>Optional.</strong> Text to invite the user to use the contact via WhatsApp.', 'creame-whatsapp-me' ) . '</p>' .
			'<p>' . __( 'You can use formatting styles like in WhatsApp: _<em>italic</em>_ *<strong>bold</strong>* ~<del>strikethrough</del>~', 'creame-whatsapp-me' ) . '</p>';
	}

	/**
	 * Field 'message_delay' output
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function field_message_delay() {
		echo '<input name="whatsappme[message_delay]" value="' . $this->settings['message_delay'] . '" class="small-text" type="number" min="0"> ' . __( 'milliseconds', 'creame-whatsapp-me' ) .
			'<p class="description"> ' . __( 'The <strong>Call to action</strong> will be automatically displayed once when the user exceeds the estimated delay on a page.', 'creame-whatsapp-me' ) . '</p>';
	}

	/**
	 * Field 'message_badge' output
	 *
	 * @since    2.1.0
	 * @return   void
	 */
	public function field_message_badge() {
		echo '<fieldset><legend class="screen-reader-text"><span>' . __( 'Button Badge', 'creame-whatsapp-me' ) . '</span></legend>' .
			'<label><input name="whatsappme[message_badge]" value="yes" type="checkbox"' . checked( 'yes', $this->settings['message_badge'], false ) . '> ' .
			__('Display a button badge instead of opening the <strong>Call to action</strong> for a "less intrusive" mode', 'creame-whatsapp-me' ) . '</label></fieldset>';
	}

	/**
	 * Field 'message_send' output
	 *
	 * @since    1.4.0
	 * @return   void
	 */
	public function field_message_send() {
		echo '<textarea name="whatsappme[message_send]" rows="3" class="regular-text" placeholder="' . esc_attr__( "Hi {SITE}! I need more info about {TITLE}", 'creame-whatsapp-me' ) . '">' . $this->settings['message_send'] . '</textarea>' .
			'<p class="description">' . __( '<strong>Optional.</strong> Default message to start the conversation.', 'creame-whatsapp-me' ) . '</p>' .
			'<p>' . __( 'You can use vars <code>{SITE} {URL} {TITLE}</code> that will be replaced with the values of the current page.', 'creame-whatsapp-me' ) . '</p>';
	}

	/**
	 * Field 'mobile_only' output
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function field_mobile_only() {
		echo '<fieldset><legend class="screen-reader-text"><span>' . __( 'Mobile only', 'creame-whatsapp-me' ) . '</span></legend>' .
			'<label><input name="whatsappme[mobile_only]" value="yes" type="checkbox"' . checked( 'yes', $this->settings['mobile_only'], false ) . '> ' .
			__('Only display the button on mobile devices', 'creame-whatsapp-me' ) . '</label></fieldset>';
	}

	/**
	 * Field 'position' output
	 *
	 * @since    1.3.0
	 * @return   void
	 */
	public function field_position() {
		echo '<fieldset><legend class="screen-reader-text"><span>' . __( 'Position on screen', 'creame-whatsapp-me' ) . '</span></legend>' .
			'<p><label><input name="whatsappme[position]" value="right" type="radio"' . checked( 'right', $this->settings['position'], false ) . '> ' .
			__('Right', 'creame-whatsapp-me' ) . '</label>' .
			'<br><label><input name="whatsappme[position]" value="left" type="radio"' . checked( 'left', $this->settings['position'], false ) . '> ' .
			__('Left', 'creame-whatsapp-me' ) . '</label></p></fieldset>';
	}

	/**
	 * Field 'field_view_all' output
	 *
	 * @since    2.0.0
	 * @return   void
	 */
	public function field_view_all() {
		if ( isset( $this->settings['visibility']['all'] ) && $this->settings['visibility']['all'] == 'no' ) {
			$value = 'no';
		} else {
			$value = 'yes';
		}

		echo '<label class="whatsappme_view_all"><input type="radio" name="whatsappme[view][all]" value="yes"' . checked( 'yes', $value, false ) . '> ' .
			'<span class="dashicons dashicons-visibility" title="' . __( 'Show', 'creame-whatsapp-me' ) . '"></span></label>' .
			'<label class="whatsappme_view_all"><input type="radio" name="whatsappme[view][all]" value="no"' . checked( 'no', $value, false ) . '> ' .
			'<span class="dashicons dashicons-hidden" title="' . __( 'Hide', 'creame-whatsapp-me' ) . '"></span></label>';
	}

	/**
	 * Field 'field_view' output
	 *
	 * @since    2.0.0
	 * @param    array    $args       array with key field.
	 * @return   void
	 */
	public function field_view($args) {
		$field = $args['field'];
		$value = isset( $this->settings['visibility'][ $field ] ) ? $this->settings['visibility'][ $field ] : '';

		echo '<label><input type="radio" name="whatsappme[view][' . $field . ']" value="yes"' . checked( 'yes', $value, false ) . '> ' .
			'<span class="dashicons dashicons-visibility" title="' . __( 'Show', 'creame-whatsapp-me' ) . '"></span></label>' .
			'<label><input type="radio" name="whatsappme[view][' . $field . ']" value="no"' . checked( 'no', $value, false ) . '> ' .
			'<span class="dashicons dashicons-hidden" title="' . __( 'Hide', 'creame-whatsapp-me' ) . '"></span></label>' .
			'<label><input type="radio" name="whatsappme[view][' . $field . ']" value=""' . checked( '', $value, false ) . '> ' .
			__( 'Inherit', 'creame-whatsapp-me' ) . ' <span class="dashicons dashicons-visibility view_inheritance_' . $field . '"></span></label>';
	}

	/**
	 * Add menu to the options page in the wordpress admin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function add_menu() {

		add_options_page('WhatsApp me', 'WhatsApp me', 'manage_options', 'whatsappme', array( $this, 'options_page' ));

	}

	/**
	 * Add link to options page on plugins page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function settings_link( $links ) {

		$settings_link = '<a href="options-general.php?page=' . $this->plugin_name . '">' . __( 'Settings', 'creame-whatsapp-me' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;

	}

	/**
	 * Generate the options page in the wordpress admin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	function options_page() {
		?>
			<div class="wrap">
				<h1>WhatsApp me</h1>

				<form method="post" id="whatsappme_form" action="options.php">
					<?php
					settings_fields('whatsappme');
					do_settings_sections('whatsappme');
					submit_button();
					?>
				</form>
			</div>
		<?php
	}

	/**
	 * Add Meta Box for all the public post types
	 *
	 * @since    1.1.0
	 * @access   public
	 * @return   void
	 */
	public function add_meta_boxes() {
		// Default post types
		$builtin_post_types = array( 'post', 'page' );
		// Custom post types with public url
		$custom_post_types = array_keys( get_post_types( array( 'has_archive' => true ), 'names' ) );

		// Add/remove posts types for "WhatsApp me" meta box
		$post_types = apply_filters( 'whatsappme_post_types_meta_box', array_merge( $builtin_post_types, $custom_post_types ) );

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'whatsappme',
				__( 'WhatsApp me', 'creame-whatsapp-me' ),
				array( $this, 'meta_box' ),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Generate Meta Box html
	 *
	 * @since    1.1.0     (previously named "add_meta_box")
	 * @since    2.0.0     Now can set as [show, hide, default]
	 * @access   public
	 * @return   void
	 */
	public function meta_box( $post ) {

		$metadata = get_post_meta( $post->ID, '_whatsappme', true ) ?: array();
		$metadata = array_merge( array(
			'message_text' => '',
			'message_send' => '',
			'hide' => false,
			'view' => '',
		), $metadata );

		// Move old 'hide' to new 'view' field
		if ( $metadata['hide'] ) {
			$metadata['view'] = 'no';
		}
		unset( $metadata['hide'] );

		wp_nonce_field( 'whatsappme_data', 'whatsappme_nonce' );
		?>
			<div class="whatsappme-metabox">
				<p>
					<label for="whatsappme_message"><?php _e( 'Call to action', 'creame-whatsapp-me' ); ?></label><br>
					<textarea name="whatsappme_message" rows="2" class="large-text"><?php echo $metadata['message_text']; ?></textarea>
				</p>
				<p>
					<label for="whatsappme_message_send"><?php _e( 'Message', 'creame-whatsapp-me' ); ?></label><br>
					<textarea name="whatsappme_message_send" rows="2" class="large-text"><?php echo $metadata['message_send']; ?></textarea>
					<small><?php _e( 'You can use vars <code>{SITE} {URL} {TITLE}</code>', 'creame-whatsapp-me' ); ?></small>
				</p>
				<p>
					<label><input type="radio" name="whatsappme_view" value="yes" <?php checked( 'yes', $metadata['view'] ); ?>>
						<span class="dashicons dashicons-visibility" title="<?php echo __( 'Show', 'creame-whatsapp-me' ); ?>"></span></label>
					<label><input type="radio" name="whatsappme_view" value="no" <?php checked( 'no', $metadata['view'] ); ?>>
						<span class="dashicons dashicons-hidden" title="<?php echo __( 'Hide', 'creame-whatsapp-me' ); ?>"></span></label>
					<label><input type="radio" name="whatsappme_view" value="" <?php checked( '', $metadata['view'] ); ?>>
						<?php echo __( 'Default visibility', 'creame-whatsapp-me' ); ?></label>
				</p>
			</div>
			<style>
				.whatsappme-metabox code { font-size:smaller; vertical-align:text-bottom; }
				.whatsappme-metabox .dashicons { opacity:.5; }
				.whatsappme-metabox input[type=radio] { margin-right:1px; }
				.whatsappme-metabox input[type=radio]+span { margin-right:5px; transition:all 200ms; }
				.whatsappme-metabox input[type=radio]:checked+span { color:#79ba49; opacity:1; }
				.whatsappme-metabox input[type=radio]:checked+.dashicons-hidden { color:#ca4a1f; }
			</style>
		<?php
	}

	/**
	 * Save meta data from "WhatsApp me" Meta Box on post save
	 *
	 * @since    1.1.0
	 * @since    2.0.0     Change 'hide' key to 'view' now values can be [yes, no]
	 * @access   public
	 * @return   void
	 */
	public function save_post( $post_id ) {
		if ( wp_is_post_autosave( $post_id ) ||
			 ! isset( $_POST['whatsappme_nonce'] ) ||
			 ! wp_verify_nonce( $_POST['whatsappme_nonce'], 'whatsappme_data' ) ) {
			return;
		}

		// Clean and delete empty/false fields
		$metadata = array_filter( $this->clean_input( array(
			'message_text' => $_POST['whatsappme_message'],
			'message_send' => $_POST['whatsappme_message_send'],
			'view'         => $_POST['whatsappme_view'],
		) ) );

		if ( count( $metadata ) ) {
			update_post_meta( $post_id, '_whatsappme', $metadata );
		} else {
			delete_post_meta( $post_id, '_whatsappme' );
		}
	}

	/**
	 * Clean user input fields
	 *
	 * @since    2.0.0
	 * @param    mixed     $value to clean
	 * @return   mixed     $value cleaned
	 */
	public function clean_input($value) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'clean_input' ), $value );
		} else if ( is_string( $value ) ) {
			// Split lines, clean and re-join lines
			return implode( "\n", array_map( 'sanitize_text_field', explode( "\n", trim( $value ) ) ) );
		} else {
			return $value;
		}
	}
}
