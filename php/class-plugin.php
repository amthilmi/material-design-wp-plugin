<?php
/**
 * Bootstraps the Material Theme Builder plugin.
 *
 * @package MaterialThemeBuilder
 */

namespace MaterialThemeBuilder;

use MaterialThemeBuilder\Blocks\Blocks_Frontend;
use MaterialThemeBuilder\Blocks\Image_List_Block;
use MaterialThemeBuilder\Blocks\Recent_Posts_Block;
use MaterialThemeBuilder\Blocks\Hand_Picked_Posts_Block;
use MaterialThemeBuilder\Blocks\Contact_Form_Block;
use MaterialThemeBuilder\Customizer\Controls;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * The theme slug.
	 * 
	 * @var string
	 */
	const THEME_SLUG = 'material-theme';

	/**
	 * Controls class.
	 *
	 * @var Controls
	 */
	public $customizer_controls;

	/**
	 * Image_List_Block class.
	 *
	 * @var Image_List_Block
	 */
	public $image_list_block;

	/**
	 * Recent_Posts_Block class.
	 *
	 * @var Recent_Posts_Block
	 */
	public $recent_post_block;

	/**
	 * Hand_Picked_Posts_Block class.
	 *
	 * @var Hand_Picked_Posts_Block
	 */
	public $hand_picked_post_block;

	/**
	 * Contact_Form_Block class.
	 *
	 * @var Contact_Form_Block
	 */
	public $contact_form_block;

	/**
	 * Blocks_Frontend class.
	 *
	 * @var Blocks_Frontend
	 */
	public $blocks_frontend;

	/**
	 * Initiate the plugin resources.
	 *
	 * Priority is 9 because WP_Customize_Widgets::register_settings() happens at
	 * after_setup_theme priority 10. This is especially important for plugins
	 * that extend the Customizer to ensure resources are available in time.
	 *
	 * @action after_setup_theme, 9
	 */
	public function init() {
		$this->config = apply_filters( 'material_theme_builder_plugin_config', $this->config, $this );

		$this->customizer_controls = new Controls( $this );
		$this->customizer_controls->init();

		$this->recent_post_block = new Recent_Posts_Block( $this );
		$this->recent_post_block->init();

		$this->hand_picked_post_block = new Hand_Picked_Posts_Block( $this );
		$this->hand_picked_post_block->init();

		$this->image_list_block = new Image_List_Block( $this );
		$this->image_list_block->init();

		$this->contact_form_block = new Contact_Form_Block( $this );
		$this->contact_form_block->init();

		$this->blocks_frontend = new Blocks_Frontend( $this );
		$this->blocks_frontend->init();
	}

	/**
	 * Load Gutenberg assets.
	 *
	 * @action enqueue_block_editor_assets
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script(
			'material-block-editor-js',
			$this->asset_url( 'assets/js/block-editor.js' ),
			[
				'lodash',
				'react',
				'wp-block-editor',
				'wp-editor',
				'wp-date',
				'wp-api-fetch',
				'jquery',
			],
			$this->asset_version(),
			false
		);

		$wp_localized_script_data = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		];

		if ( Helpers::is_current_user_admin_or_editor_with_manage_options() ) {
			$wp_localized_script_data['allow_contact_form_block']    = true;
			$wp_localized_script_data['recaptcha_ajax_nonce_action'] = wp_create_nonce( 'mtb_recaptcha_ajax_nonce' );
		}

		wp_localize_script( 'material-block-editor-js', 'mtb', $wp_localized_script_data );

		$fonts_url = $this->customizer_controls->get_google_fonts_url();

		wp_enqueue_style(
			'material-styles-css',
			esc_url( $fonts_url ),
			[],
			$this->asset_version()
		);

		wp_enqueue_style(
			'material-block-editor-css',
			$this->asset_url( 'assets/css/block-editor-compiled.css' ),
			[],
			$this->asset_version()
		);

		wp_localize_script( 'material-block-editor-js', 'mtbBlockDefaults', $this->get_block_defaults() );

		wp_add_inline_style( 'material-block-editor-css', $this->customizer_controls->get_frontend_css() );
	}

	/**
	 * Enqueue google fonts.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_google_fonts() {
		$fonts_url = $this->customizer_controls->get_google_fonts_url();

		wp_enqueue_style(
			'material-google-fonts-cdn',
			esc_url( $fonts_url ),
			[],
			$this->asset_version()
		);
	}

	/**
	 * Enqueue backend styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_backend_assets() {
		wp_enqueue_style(
			'material-backend-css',
			$this->asset_url( 'assets/css/backend-compiled.css' ),
			[],
			$this->asset_version()
		);
	}

	/**
	 * Enqueue front-end styles and scripts.
	 *
	 * @action wp_enqueue_scripts, 100
	 */
	public function enqueue_front_end_assets() {
		wp_enqueue_script(
			'material-front-end-js',
			$this->asset_url( 'assets/js/front-end.js' ),
			[ 'jquery' ],
			$this->asset_version(),
			true
		);

		$mtb_recaptcha_site_key   = get_option( 'mtb_recaptcha_site_key', '' );
		$wp_localized_script_data = [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ];

		if ( function_exists( 'has_block' ) && has_block( 'material/contact-form' ) && ! empty( $mtb_recaptcha_site_key ) ) {
			wp_enqueue_script(
				'google-recaptcha-v3',
				'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $mtb_recaptcha_site_key ),
				[ 'jquery', 'material-front-end-js' ],
				'3.0.0',
				true
			);

			$wp_localized_script_data['recaptcha_site_key'] = $mtb_recaptcha_site_key;
		}

		wp_localize_script( 'material-front-end-js', 'mtb', $wp_localized_script_data );

		wp_enqueue_style(
			'material-front-end-css',
			$this->asset_url( 'assets/css/front-end-compiled.css' ),
			[],
			$this->asset_version()
		);

		/**
		 * Enqueue material style overrides if the theme is not material.
		 */
		if ( false === strpos( get_stylesheet(), 'material-theme' ) ) {
			wp_enqueue_style(
				'material-overrides-css',
				$this->asset_url( 'assets/css/overrides-compiled.css' ),
				[],
				$this->asset_version()
			);
		}
	}

	/**
	 * Output inline styles with css variables at the top of the head.
	 *
	 * @action wp_head, 1
	 * @action admin_head, 1
	 */
	public function frontend_inline_css() {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<style id="material-css-variables">' . $this->customizer_controls->get_frontend_css() . '</style>';
		// phpcs:enable
	}

	/**
	 * Add custom material block category.
	 *
	 * @action block_categories, 10, 2
	 *
	 * @param array   $categories Registered categories.
	 * @param WP_Post $post       The current post object.
	 *
	 * @return array
	 */
	public function block_category( $categories, $post ) {
		$categories[] = [
			'slug'  => 'material',
			'title' => __( 'Material Blocks', 'material-theme-builder' ),
		];

		return $categories;
	}

	/**
	 * Get the block default attributes set in customizer.
	 *
	 * @return array
	 */
	public function get_block_defaults() {
		$defaults = [];
		$controls = $this->customizer_controls;

		// Set corner radius defaults for blocks.
		foreach ( $controls->get_corner_styles_controls() as $control ) {
			$value = $controls->get_theme_mod( $control['id'] );
			if ( ! empty( $value ) && ! empty( $control['blocks'] ) && is_array( $control['blocks'] ) ) {
				foreach ( $control['blocks'] as $block => $settings ) {
					$defaults[ $block ] = array_key_exists( $block, $defaults ) ? $defaults[ $block ] : [];

					// If the value exceeds min or max, limit it.
					if ( ! empty( $settings['limits'] ) && isset( $settings['limits']['min'] ) && $value < $settings['limits']['min'] ) {
						$value = $settings['limits']['min'];
					} elseif ( ! empty( $settings['limits'] ) && isset( $settings['limits']['max'] ) && $value > $settings['limits']['max'] ) {
						$value = $settings['limits']['max'];
					}

					$defaults[ $block ]['cornerRadius'] = absint( $value );
				}
			}
		}

		return $defaults;
	}

	/**
	 * Prepares an admin notice.
	 *
	 * @param string $title   The title to be showed in the notice.
	 * @param string $message The message of the notice.
	 * 
	 * @return string
	 */
	public function material_notice( $title, $message ) {
		ob_start();
		?>

		<div class="notice notice-info is-dismissible material-notice-container">
			<img 
				src="<?php echo esc_url( $this->asset_url( 'assets/images/plugin-icon.svg' ) ); ?>" 
				alt="<?php esc_attr_e( 'Material Theme Builder', 'material-theme-builder' ); ?>" 
			/>

			<div class="material-notice-container__content">
				<h3 class="material-notice-container__content__title">
					<?php echo esc_html( $title ); ?>
				</h3>
				<p class="material-notice-container__content__text">
					<?php echo wp_kses( $message, [ 'a' => [ 'href' => [] ] ] ); ?>
				</p>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Checks whether the material theme is installed.
	 * 
	 * @return bool
	 */
	public function theme_installed() {
		return file_exists( get_theme_root() . DIRECTORY_SEPARATOR . self::THEME_SLUG );
	}

	/**
	 * Returns the status of the material theme
	 *
	 * @return string
	 */
	public function material_theme_status() {
		if ( ! $this->theme_installed() ) {
			return 'install';
		}

		if ( self::THEME_SLUG !== get_template() ) {
			return 'activate';
		}

		return 'ok';
	}

	/**
	 * Show admin notice if theme isn't installed.
	 *
	 * @action admin_notices, 10, 2
	 *
	 * @return void
	 */
	public function theme_not_installed_notice() {
		// Theme already installed. Don't show the notice.
		if ( $this->theme_installed() ) {
			return;
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->material_notice(
			__( 
				'Install Material Theme to take advantage of all Material Plugin customizations', 
				'material-theme-builder' 
			),
			sprintf(
				/* translators: %s: url to the theme installation page */
				__( 
					'The Material Plugin enables you to customize Material Components. We recommend installing the companion Material Theme for full site customization. <a href="%s">Install theme</a>', 
					'material-theme-builder' 
				), 
esc_url( admin_url( '/themes.php?search=Material Theme' ) )
			)
		);
		// phpcs:enable
	}

	/**
	 * Show admin notice if theme and plugin are active.
	 *
	 * @action admin_notices, 9, 2
	 *
	 * @return void
	 */
	public function plugin_activated_notice() {
		// Theme not active or plugin didn't JUST activate. Stop here.
		if ( self::THEME_SLUG !== get_template() || ! get_transient( 'mtb-activation-notice' ) ) {
			return;
		}

		delete_transient( 'mtb-activation-notice' );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->material_notice(
			__( 'See Material Theming in action', 'material-theme-builder' ),
			sprintf(
				/* translators: %s: url to the plugin kitchen sink page */
				__( 
					'Customize and view Material Theming get applied throughout all Material Components. <a href="%s">View all Material Components</a>', 
					'material-theme-builder' 
				), 
				esc_url( admin_url( 'customize.php#material-library' ) )
			)
		);
		// phpcs:enable
	} 
}
