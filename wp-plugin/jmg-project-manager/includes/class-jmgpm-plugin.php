<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-jmgpm-schema.php';
require_once __DIR__ . '/class-jmgpm-rest.php';
require_once __DIR__ . '/class-jmgpm-admin.php';

final class JMGPM_Plugin {
	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function init(): void {
		// Reserved for future (CPTs, scripts, etc.). Using custom tables today.
	}

	public function rest_api_init(): void {
		JMGPM_REST::register_routes();
	}

	public function admin_menu(): void {
		JMGPM_Admin::register_menu();
	}

	public static function activate(): void {
		JMGPM_Schema::install();
		flush_rewrite_rules();
	}
}

