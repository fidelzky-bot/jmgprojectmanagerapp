<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class JMGPM_Admin {
	private static function tables(): array {
		global $wpdb;
		return array(
			'teams'         => $wpdb->prefix . 'jmgpm_teams',
			'projects'      => $wpdb->prefix . 'jmgpm_projects',
			'tasks'         => $wpdb->prefix . 'jmgpm_tasks',
			'invites'       => $wpdb->prefix . 'jmgpm_invites',
			'notifications' => $wpdb->prefix . 'jmgpm_notifications',
			'messages'      => $wpdb->prefix . 'jmgpm_messages',
		);
	}

	private static function render_table( array $columns, array $rows ): void {
		echo '<table class="widefat striped" style="margin-top:12px">';
		echo '<thead><tr>';
		foreach ( $columns as $key => $label ) {
			echo '<th scope="col">' . esc_html( $label ) . '</th>';
		}
		echo '</tr></thead><tbody>';
		if ( empty( $rows ) ) {
			echo '<tr><td colspan="' . esc_attr( (string) count( $columns ) ) . '">No rows found.</td></tr>';
		} else {
			foreach ( $rows as $row ) {
				echo '<tr>';
				foreach ( $columns as $key => $label ) {
					$val = $row[ $key ] ?? '';
					if ( is_string( $val ) && strlen( $val ) > 180 ) {
						$val = substr( $val, 0, 180 ) . '…';
					}
					echo '<td>' . esc_html( (string) $val ) . '</td>';
				}
				echo '</tr>';
			}
		}
		echo '</tbody></table>';
	}

	public static function register_menu(): void {
		add_menu_page(
			'JMG Project Manager',
			'JMG Projects',
			'read',
			'jmgpm',
			array( __CLASS__, 'render_projects_page' ),
			'dashicons-portfolio',
			26
		);

		add_submenu_page( 'jmgpm', 'Projects', 'Projects', 'read', 'jmgpm', array( __CLASS__, 'render_projects_page' ) );
		add_submenu_page( 'jmgpm', 'Tasks', 'Tasks', 'read', 'jmgpm-tasks', array( __CLASS__, 'render_tasks_page' ) );
		add_submenu_page( 'jmgpm', 'Teams', 'Teams', 'read', 'jmgpm-teams', array( __CLASS__, 'render_teams_page' ) );
		add_submenu_page( 'jmgpm', 'Invites', 'Invites', 'read', 'jmgpm-invites', array( __CLASS__, 'render_invites_page' ) );
		add_submenu_page( 'jmgpm', 'Notifications', 'Notifications', 'read', 'jmgpm-notifications', array( __CLASS__, 'render_notifications_page' ) );
		add_submenu_page( 'jmgpm', 'Messages', 'Messages', 'read', 'jmgpm-messages', array( __CLASS__, 'render_messages_page' ) );
	}

	private static function wrap( string $title, callable $body ): void {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( $title ) . '</h1>';
		$body();
		echo '</div>';
	}

	public static function render_projects_page(): void {
		self::wrap(
			'Projects',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['projects'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, name, status, team_id, created_by_user_id, created_at, updated_at FROM {$t['projects']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'                => 'ID',
						'name'              => 'Name',
						'status'            => 'Status',
						'team_id'           => 'Team',
						'created_by_user_id'=> 'Created By',
						'created_at'        => 'Created',
						'updated_at'        => 'Updated',
					),
					$rows
				);
			}
		);
	}

	public static function render_tasks_page(): void {
		self::wrap(
			'Tasks',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['tasks'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, project_id, title, status, priority, owner_user_id, assigned_to_user_id, due_date, created_at, updated_at FROM {$t['tasks']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'                 => 'ID',
						'project_id'         => 'Project',
						'title'              => 'Title',
						'status'             => 'Status',
						'priority'           => 'Priority',
						'owner_user_id'      => 'Owner',
						'assigned_to_user_id'=> 'Assigned To',
						'due_date'           => 'Due',
						'created_at'         => 'Created',
						'updated_at'         => 'Updated',
					),
					$rows
				);
			}
		);
	}

	public static function render_teams_page(): void {
		self::wrap(
			'Teams',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['teams'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, name, owner_user_id, created_at FROM {$t['teams']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'            => 'ID',
						'name'          => 'Name',
						'owner_user_id' => 'Owner',
						'created_at'    => 'Created',
					),
					$rows
				);
			}
		);
	}

	public static function render_invites_page(): void {
		self::wrap(
			'Invites',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['invites'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, email, team_id, inviter_user_id, token, status, created_at, expires_at FROM {$t['invites']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'              => 'ID',
						'email'           => 'Email',
						'team_id'         => 'Team',
						'inviter_user_id' => 'Inviter',
						'token'           => 'Token',
						'status'          => 'Status',
						'created_at'      => 'Created',
						'expires_at'      => 'Expires',
					),
					$rows
				);
			}
		);
	}

	public static function render_notifications_page(): void {
		self::wrap(
			'Notifications',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['notifications'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, user_id, sender_user_id, type, message, entity_type, entity_id, is_read, created_at FROM {$t['notifications']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'            => 'ID',
						'user_id'       => 'User',
						'sender_user_id'=> 'Sender',
						'type'          => 'Type',
						'message'       => 'Message',
						'entity_type'   => 'Entity Type',
						'entity_id'     => 'Entity ID',
						'is_read'       => 'Read',
						'created_at'    => 'Created',
					),
					$rows
				);
			}
		);
	}

	public static function render_messages_page(): void {
		self::wrap(
			'Messages',
			static function () {
				global $wpdb;
				$t = self::tables();
				echo '<p>Showing rows from <code>' . esc_html( $t['messages'] ) . '</code>.</p>';
				$rows = $wpdb->get_results( "SELECT id, sender_user_id, receiver_user_id, is_read, created_at, content FROM {$t['messages']} ORDER BY created_at DESC LIMIT 200", ARRAY_A );
				self::render_table(
					array(
						'id'              => 'ID',
						'sender_user_id'  => 'Sender',
						'receiver_user_id'=> 'Receiver',
						'is_read'         => 'Read',
						'created_at'      => 'Created',
						'content'         => 'Content',
					),
					$rows
				);
			}
		);
	}
}

