<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WhatsAppMe
 * @subpackage WhatsAppMe/public
 * @author     Creame <hola@crea.me>
 */
class WhatsAppMe_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @since    2.0.0     Added visibility setting
	 * @since    2.1.0     Added message_badge
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = array(
			'show'          => false,
			'telephone'     => '',
			'message_text'  => '',
			'message_delay' => 10000,
			'message_badge' => 'no',
			'message_send'  => '',
			'mobile_only'   => false,
			'position'      => 'right',
			'visibility'    => array( 'all' => 'yes' ),
		);

	}

	/**
	 * Get global settings and current post settings and prepare
	 *
	 * @since    1.0.0
	 * @since    2.0.0   Check visibility
	 * @return   void
	 */
	public function get_settings() {

		global $post;

		$global_settings = get_option( 'whatsappme' );

		if ( is_array( $global_settings ) ) {
			// Clean unused saved settings
			$settings = array_intersect_key( $global_settings, $this->settings );
			// Merge defaults with saved settings
			$settings = array_merge( $this->settings, $settings );

			// Post custom settings
			$post_settings = is_object( $post ) ? get_post_meta( $post->ID, '_whatsappme', true ) : '';

			// Move old 'hide' to new 'view' field
			if ( isset( $post_settings['hide'] ) ) {
				$post_settings['view'] = 'no';
				unset( $post_settings['hide'] );
			}

			// Prepare settings
			$settings['telephone']   = preg_replace( '/^0+|\D/', '', $settings['telephone'] );
			$settings['position']    = $settings['position'] != 'left' ? 'right' : 'left';
			$settings['mobile_only'] = $settings['mobile_only'] == 'yes';
			if ( isset( $post_settings['message_text'] ) ) {
				$settings['message_text'] = $post_settings['message_text'];
			}
			if ( isset( $post_settings['message_send'] ) ) {
				$settings['message_send'] = $post_settings['message_send'];
			}
			$settings['message_badge'] = $settings['message_text'] && $settings['message_badge'] == 'yes';
			$settings['message_send']  = $this->formated_message_send( $settings['message_send'] );

			$settings['show'] = $settings['telephone'] != '';
			if ( $settings['show'] ) {
				$settings['show'] = isset( $post_settings['view'] ) ?
					$post_settings['view'] == 'yes' :
					$this->check_visibility( $settings['visibility'] );
			}

			$this->settings = $settings;
		}

		// Apply filter to settings
		$this->settings = apply_filters( 'whatsappme_get_settings', $this->settings, $post );

		// Ensure not show if not phone
		if ( ! $this->settings['telephone'] ) {
			$this->settings['show'] = false;
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( $this->settings['show'] ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/whatsappme.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->settings['show'] ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/whatsappme.js', array( 'jquery' ), $this->version, true );
		}

	}

	/**
	 * Outputs WhatsApp button html and his settings on footer
	 *
	 * @since    1.0.0
	 */
	public function footer_html() {

		// Clean unnecessary settings on front
		$data = array_diff_key( $this->settings, array_flip( array( 'show', 'visibility', 'position' ) ) );

		if ( $this->settings['show'] ) {
		?>
		<div class="whatsappme whatsappme--<?php echo $this->settings['position']; ?>" data-settings="<?php echo esc_attr( json_encode( $data ) ); ?>">
			<div class="whatsappme__button">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" fill="currentColor"/></svg>
				<?php if ($this->settings['message_badge']): ?><div class="whatsappme__badge">1</div><?php endif; ?>
			</div>
			<?php if ($this->settings['message_text']): ?>
				<div class="whatsappme__box">
					<header class="whatsappme__header">
						<svg xmlns="http://www.w3.org/2000/svg" width="120" height="28" viewBox="0 0 120 28"><path d="M117.2 17c0 .4-.2.7-.4 1-.1.3-.4.5-.7.7l-1 .2c-.5 0-.9 0-1.2-.2l-.7-.7a3 3 0 0 1-.4-1 5.4 5.4 0 0 1 0-2.3c0-.4.2-.7.4-1l.7-.7a2 2 0 0 1 1.1-.3 2 2 0 0 1 1.8 1l.4 1a5.3 5.3 0 0 1 0 2.3zm2.5-3c-.1-.7-.4-1.3-.8-1.7a4 4 0 0 0-1.3-1.2c-.6-.3-1.3-.4-2-.4-.6 0-1.2.1-1.7.4a3 3 0 0 0-1.2 1.1V11H110v13h2.7v-4.5c.4.4.8.8 1.3 1 .5.3 1 .4 1.6.4a4 4 0 0 0 3.2-1.5c.4-.5.7-1 .8-1.6.2-.6.3-1.2.3-1.9s0-1.3-.3-2zm-13.1 3c0 .4-.2.7-.4 1l-.7.7-1.1.2c-.4 0-.8 0-1-.2-.4-.2-.6-.4-.8-.7a3 3 0 0 1-.4-1 5.4 5.4 0 0 1 0-2.3c0-.4.2-.7.4-1 .1-.3.4-.5.7-.7a2 2 0 0 1 1-.3 2 2 0 0 1 1.9 1l.4 1a5.4 5.4 0 0 1 0 2.3zm1.7-4.7a4 4 0 0 0-3.3-1.6c-.6 0-1.2.1-1.7.4a3 3 0 0 0-1.2 1.1V11h-2.6v13h2.7v-4.5c.3.4.7.8 1.2 1 .6.3 1.1.4 1.7.4a4 4 0 0 0 3.2-1.5c.4-.5.6-1 .8-1.6.2-.6.3-1.2.3-1.9s-.1-1.3-.3-2c-.2-.6-.4-1.2-.8-1.6zm-17.5 3.2l1.7-5 1.7 5h-3.4zm.2-8.2l-5 13.4h3l1-3h5l1 3h3L94 7.3h-3zm-5.3 9.1l-.6-.8-1-.5a11.6 11.6 0 0 0-2.3-.5l-1-.3a2 2 0 0 1-.6-.3.7.7 0 0 1-.3-.6c0-.2 0-.4.2-.5l.3-.3h.5l.5-.1c.5 0 .9 0 1.2.3.4.1.6.5.6 1h2.5c0-.6-.2-1.1-.4-1.5a3 3 0 0 0-1-1 4 4 0 0 0-1.3-.5 7.7 7.7 0 0 0-3 0c-.6.1-1 .3-1.4.5l-1 1a3 3 0 0 0-.4 1.5 2 2 0 0 0 1 1.8l1 .5 1.1.3 2.2.6c.6.2.8.5.8 1l-.1.5-.4.4a2 2 0 0 1-.6.2 2.8 2.8 0 0 1-1.4 0 2 2 0 0 1-.6-.3l-.5-.5-.2-.8H77c0 .7.2 1.2.5 1.6.2.5.6.8 1 1 .4.3.9.5 1.4.6a8 8 0 0 0 3.3 0c.5 0 1-.2 1.4-.5a3 3 0 0 0 1-1c.3-.5.4-1 .4-1.6 0-.5 0-.9-.3-1.2zM74.7 8h-2.6v3h-1.7v1.7h1.7v5.8c0 .5 0 .9.2 1.2l.7.7 1 .3a7.8 7.8 0 0 0 2 0h.7v-2.1a3.4 3.4 0 0 1-.8 0l-1-.1-.2-1v-4.8h2V11h-2V8zm-7.6 9v.5l-.3.8-.7.6c-.2.2-.7.2-1.2.2h-.6l-.5-.2a1 1 0 0 1-.4-.4l-.1-.6.1-.6.4-.4.5-.3a4.8 4.8 0 0 1 1.2-.2 8.3 8.3 0 0 0 1.2-.2l.4-.3v1zm2.6 1.5v-5c0-.6 0-1.1-.3-1.5l-1-.8-1.4-.4a10.9 10.9 0 0 0-3.1 0l-1.5.6c-.4.2-.7.6-1 1a3 3 0 0 0-.5 1.5h2.7c0-.5.2-.9.5-1a2 2 0 0 1 1.3-.4h.6l.6.2.3.4.2.7c0 .3 0 .5-.3.6-.1.2-.4.3-.7.4l-1 .1a21.9 21.9 0 0 0-2.4.4l-1 .5c-.3.2-.6.5-.8.9-.2.3-.3.8-.3 1.3s.1 1 .3 1.3c.1.4.4.7.7 1l1 .4c.4.2.9.2 1.3.2a6 6 0 0 0 1.8-.2c.6-.2 1-.5 1.5-1a4 4 0 0 0 .2 1H70l-.3-1v-1.2zm-11-6.7c-.2-.4-.6-.6-1-.8-.5-.2-1-.3-1.8-.3-.5 0-1 .1-1.5.4a3 3 0 0 0-1.3 1.2v-5h-2.7v13.4H53v-5.1c0-1 .2-1.7.5-2.2.3-.4.9-.6 1.6-.6.6 0 1 .2 1.3.6.3.4.4 1 .4 1.8v5.5h2.7v-6c0-.6 0-1.2-.2-1.6 0-.5-.3-1-.5-1.3zm-14 4.7l-2.3-9.2h-2.8l-2.3 9-2.2-9h-3l3.6 13.4h3l2.2-9.2 2.3 9.2h3l3.6-13.4h-3l-2.1 9.2zm-24.5.2L18 15.6c-.3-.1-.6-.2-.8.2A20 20 0 0 1 16 17c-.2.2-.4.3-.7.1-.4-.2-1.5-.5-2.8-1.7-1-1-1.7-2-2-2.4-.1-.4 0-.5.2-.7l.5-.6.4-.6v-.6L10.4 8c-.3-.6-.6-.5-.8-.6H9c-.2 0-.6.1-.9.5C7.8 8.2 7 9 7 10.7c0 1.7 1.3 3.4 1.4 3.6.2.3 2.5 3.7 6 5.2l1.9.8c.8.2 1.6.2 2.2.1.6-.1 2-.8 2.3-1.6.3-.9.3-1.5.2-1.7l-.7-.4zM14 25.3c-2 0-4-.5-5.8-1.6l-.4-.2-4.4 1.1 1.2-4.2-.3-.5A11.5 11.5 0 0 1 22.1 5.7 11.5 11.5 0 0 1 14 25.3zM14 0A13.8 13.8 0 0 0 2 20.7L0 28l7.3-2A13.8 13.8 0 1 0 14 0z" fill="currentColor" fill-rule="evenodd"/></svg>
						<div class="whatsappme__close">&times;</div>
					</header>
					<div class="whatsappme__message"><?php echo $this->formated_message(); ?></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		}

	}


	/**
	 * Format raw message text for html output.
	 * Also apply styles transformations like WhatsApp app.
	 *
	 * @since    1.3.0
	 * @return   string     message formated string
	 */
	public function formated_message() {

		$replacements = apply_filters( 'whatsappme_message_replacements', array(
			'/_(\S[^_]*\S)_/mu'    => '<em>$1</em>',
			'/\*(\S[^\*]*\S)\*/mu' => '<strong>$1</strong>',
			'/~(\S[^~]*\S)~/mu'    => '<del>$1</del>',
		) );

		$replacements_keys = array_keys( $replacements );

		// Split text into lines and apply replacements line by line
		$lines = explode( "\n", $this->settings['message_text'] );
		foreach ($lines as $key => $line) {
			$lines[$key] = preg_replace( $replacements_keys, $replacements, esc_html( $line ) );
		}

		return implode( '<br>', $lines );

	}

	/**
	 * Format message send, replace vars.
	 *
	 * @since    1.4.0
	 * @return   string     message formated string
	 */
	public function formated_message_send( $string ) {
		global $wp;

		$replacements = apply_filters( 'whatsappme_message_send_replacements', array(
			'/\{SITE\}/i' => get_bloginfo( 'name' ),
			'/\{URL\}/i'  => home_url( $wp->request ),
			'/\{TITLE\}/i'=> $this->get_title(),
		) );

		return preg_replace( array_keys( $replacements ), $replacements, $string );

	}

	/**
	 * Get current page title
	 *
	 * @since    1.4.0
	 * @return   string     message formated string
	 */
	public function get_title() {

		if ( is_home() || is_singular() ) {
			$title = single_post_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( function_exists( 'wp_get_document_title' ) ) {
			$title  = wp_get_document_title();

			// Try to remove sitename from $title for cleaner title
			$sep   = apply_filters( 'document_title_separator', '-' );
			$site  = get_bloginfo( 'name', 'display' );
			$title = str_replace( esc_html( convert_chars( wptexturize( " $sep " . $site ) ) ), '', $title);
		} else {
			$title = get_bloginfo( 'name' );
		}

		return apply_filters( 'whatsappme_get_title', $title );

	}

	/**
	 * Check visibility on current page
	 *
	 * @since    2.0.0
	 * @param    array       $options    array of visibility settings
	 * @return   boolean     is visible or not on current page
	 */
	public function check_visibility($options) {

		$global = isset( $options['all'] ) ? $options['all'] == 'yes' : true;

		// Check front page
		if ( is_front_page() ) {
			return isset( $options['front_page'] ) ? $options['front_page'] == 'yes' : $global;
		}

		// Check blog page
		if ( is_home() ) {
			return isset( $options['blog_page'] ) ? $options['blog_page'] == 'yes' : $global;
		}

		// Check 404 page
		if ( is_404() ) {
			return isset( $options['404_page'] ) ? $options['404_page'] == 'yes' : $global;
		}

		// Check WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			$woo = isset( $options['woocommerce'] ) ? $options['woocommerce'] == 'yes' : $global;

			// Product page
			if ( is_product() ) {
				return isset( $options['product'] ) ? $options['product'] == 'yes' : $woo;
			}

			// Cart page
			if ( is_cart() ) {
				return isset( $options['cart'] ) ? $options['cart'] == 'yes' : $woo;
			}

			// Checkout page
			if ( is_checkout() ) {
				return isset( $options['checkout'] ) ? $options['checkout'] == 'yes' : $woo;
			}

			// Customer account pages
			if ( is_account_page() ) {
				return isset( $options['account_page'] ) ? $options['account_page'] == 'yes': $woo;
			}

			if ( is_woocommerce() ) {
				return $woo;
			}
		}

		// Check Custom Post Types
		if ( is_array( $options ) ) {
			foreach ( $options as $cpt => $view ) {
				if ( substr( $cpt, 0, 4 ) == 'cpt_' ) {
					$cpt = substr( $cpt, 4 );
					if ( is_singular( $cpt ) || is_post_type_archive( $cpt ) ) {
						return $view == 'yes';
					}
				}
			}
		}

		// Search results
		if ( is_search() ) {
			return isset( $options['search'] ) ? $options['search'] == 'yes' : $global;
		}

		// Check archives
		if ( is_archive() ) {

			// Date archive
			if ( isset( $options['date'] ) && is_date() ) {
				return $options['date'] == 'yes';
			}

			// Author archive
			if ( isset( $options['author'] ) && is_author() ) {
				return $options['author'] == 'yes';
			}

			return isset( $options['archive'] ) ? $options['archive'] == 'yes' : $global;
		}

		// Check singular
		if ( is_singular() ) {

			// Page
			if ( isset( $options['page'] ) && is_page() ) {
				return $options['page'] == 'yes';
			}

			// Post (or other custom posts)
			if ( isset( $options['post'] ) && is_single() ) {
				return $options['post'] == 'yes';
			}

			return isset( $options['singular'] ) ? $options['singular'] == 'yes' : $global;
		}

		return $global;
	}
}
