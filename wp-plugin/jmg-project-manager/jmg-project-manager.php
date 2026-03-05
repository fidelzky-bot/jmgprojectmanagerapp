<?php
/**
 * Plugin Name: JMG Project Manager
 * Description: Project & task management (Asana-inspired) inside WordPress.
 * Version: 0.1.0
 * Author: JMG
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JMGPM_VERSION', '0.1.0' );
define( 'JMGPM_PLUGIN_FILE', __FILE__ );
define( 'JMGPM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JMGPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once JMGPM_PLUGIN_DIR . 'includes/class-jmgpm-plugin.php';

register_activation_hook( __FILE__, array( 'JMGPM_Plugin', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		JMGPM_Plugin::instance();
	}
);

