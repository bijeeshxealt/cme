<?php

namespace WPaaS\Admin;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class MCP {

	/**
	 * Default options for the MCP.
	 */
	private $options = [
		'enabled' => true,
		'enable_create_tools' => true,
		'enable_update_tools' => true,
		'enable_read_tools' => true,
		'enable_delete_tools' => false,
		'enable_features_adapter' => false,
		'enable_rest_crud' => false,
	];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( is_readable( WP_PLUGIN_DIR . '/wordpress-mcp/wordpress-mcp.php' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the script.
	 *
	 * @action init
	 */
	public function init() {
		// Ensure the option exists in the database so the filter will trigger
		if ( false === get_option( 'wordpress_mcp_settings' ) ) {
			add_option( 'wordpress_mcp_settings', $this->options );
		}
		
		add_filter( 'option_wordpress_mcp_settings', [ $this, 'filter_wordpress_mcp_settings' ], PHP_INT_MAX );
		add_filter( 'pre_update_option_wordpress_mcp_settings', [ $this, 'filter_wordpress_mcp_settings' ], PHP_INT_MAX, 2 );

		add_action( 'admin_menu', [ $this, 'wpaas_remove_mcp_settings_page' ], PHP_INT_MAX );
		add_action( 'admin_init', [ $this, 'wpaas_prevent_mcp_settings_page_access' ], PHP_INT_MAX );
	}

	/**
	 * Filter the WordPress MCP settings option.
	 *
	 * @param   array  $value  The current option value.
	 *
	 * @return  array          The filtered option value.
	 */
	public function filter_wordpress_mcp_settings( $value, $old_value = null ) {

		return $this->options;

	}

	/**
	 * Remove the MCP settings page from the admin menu.
	 */
	public function wpaas_remove_mcp_settings_page() {

		remove_submenu_page('options-general.php', 'wordpress-mcp-settings');

	}

	/**
	 * Redirect any direct access to the MCP settings page back to the main admin dashboard.
	 */
	public function wpaas_prevent_mcp_settings_page_access() {

		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if ( ! $page || 'wordpress-mcp-settings' !== $page ) {

			return;

		}

			wp_safe_redirect( admin_url() );

			exit;

	}

}
