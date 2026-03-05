<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class JMGPM_Schema {
	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$teams                = $wpdb->prefix . 'jmgpm_teams';
		$team_members         = $wpdb->prefix . 'jmgpm_team_members';
		$projects             = $wpdb->prefix . 'jmgpm_projects';
		$project_members      = $wpdb->prefix . 'jmgpm_project_members';
		$project_user_roles   = $wpdb->prefix . 'jmgpm_project_user_roles';
		$tasks                = $wpdb->prefix . 'jmgpm_tasks';
		$comments             = $wpdb->prefix . 'jmgpm_comments';
		$comment_mentions     = $wpdb->prefix . 'jmgpm_comment_mentions';
		$notifications        = $wpdb->prefix . 'jmgpm_notifications';
		$notification_settings = $wpdb->prefix . 'jmgpm_notification_settings';
		$invites              = $wpdb->prefix . 'jmgpm_invites';
		$messages             = $wpdb->prefix . 'jmgpm_messages';

		// Teams (mirrors models/Team.js)
		dbDelta(
			"CREATE TABLE $teams (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(255) NOT NULL,
				description TEXT NULL,
				owner_user_id BIGINT(20) UNSIGNED NOT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY owner_user_id (owner_user_id)
			) $charset_collate;"
		);

		// Team members (Team.members[])
		dbDelta(
			"CREATE TABLE $team_members (
				team_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (team_id, user_id),
				KEY user_id (user_id)
			) $charset_collate;"
		);

		// Projects (mirrors models/Project.js)
		dbDelta(
			"CREATE TABLE $projects (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(255) NOT NULL,
				description TEXT NULL,
				team_id BIGINT(20) UNSIGNED NOT NULL,
				created_by_user_id BIGINT(20) UNSIGNED NOT NULL,
				status VARCHAR(20) NOT NULL DEFAULT 'active',
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY team_id (team_id),
				KEY created_by_user_id (created_by_user_id)
			) $charset_collate;"
		);

		// Project members (Project.members[])
		dbDelta(
			"CREATE TABLE $project_members (
				project_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (project_id, user_id),
				KEY user_id (user_id)
			) $charset_collate;"
		);

		// Project user roles (mirrors models/ProjectUserRole.js)
		dbDelta(
			"CREATE TABLE $project_user_roles (
				project_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				role VARCHAR(20) NOT NULL DEFAULT 'viewer',
				notify_all TINYINT(1) NOT NULL DEFAULT 1,
				updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (project_id, user_id),
				KEY role (role)
			) $charset_collate;"
		);

		// Tasks (mirrors models/Task.js + controllers/taskController.js attachments)
		dbDelta(
			"CREATE TABLE $tasks (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				project_id BIGINT(20) UNSIGNED NOT NULL,
				owner_user_id BIGINT(20) UNSIGNED NOT NULL,
				assigned_to_user_id BIGINT(20) UNSIGNED NULL,
				title VARCHAR(255) NOT NULL,
				description LONGTEXT NULL,
				status VARCHAR(50) NOT NULL DEFAULT 'To Do',
				priority VARCHAR(50) NOT NULL DEFAULT 'Low',
				due_date DATETIME NULL,
				attachments LONGTEXT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY project_id (project_id),
				KEY owner_user_id (owner_user_id),
				KEY assigned_to_user_id (assigned_to_user_id)
			) $charset_collate;"
		);

		// Comments (mirrors models/Comment.js)
		dbDelta(
			"CREATE TABLE $comments (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				task_id BIGINT(20) UNSIGNED NOT NULL,
				author_user_id BIGINT(20) UNSIGNED NOT NULL,
				text LONGTEXT NOT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY task_id (task_id),
				KEY author_user_id (author_user_id)
			) $charset_collate;"
		);

		// Comment mentions (Comment.mentions[])
		dbDelta(
			"CREATE TABLE $comment_mentions (
				comment_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				PRIMARY KEY (comment_id, user_id),
				KEY user_id (user_id)
			) $charset_collate;"
		);

		// Notifications (mirrors models/Notification.js)
		dbDelta(
			"CREATE TABLE $notifications (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				sender_user_id BIGINT(20) UNSIGNED NOT NULL,
				type VARCHAR(100) NULL,
				message TEXT NULL,
				entity_id VARCHAR(100) NULL,
				entity_type VARCHAR(50) NULL,
				is_read TINYINT(1) NOT NULL DEFAULT 0,
				task_title VARCHAR(255) NULL,
				title VARCHAR(255) NULL,
				project_name VARCHAR(255) NULL,
				new_role VARCHAR(50) NULL,
				extra LONGTEXT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY user_id (user_id),
				KEY sender_user_id (sender_user_id),
				KEY is_read (is_read),
				KEY created_at (created_at)
			) $charset_collate;"
		);

		// Notification settings (mirrors models/NotificationSettings.js; stored as JSON)
		dbDelta(
			"CREATE TABLE $notification_settings (
				project_id BIGINT(20) UNSIGNED NOT NULL,
				roles_json LONGTEXT NOT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (project_id)
			) $charset_collate;"
		);

		// Invites (mirrors models/Invite.js; expires after 7 days in Mongo)
		dbDelta(
			"CREATE TABLE $invites (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				email VARCHAR(255) NULL,
				team_id BIGINT(20) UNSIGNED NOT NULL,
				inviter_user_id BIGINT(20) UNSIGNED NOT NULL,
				token VARCHAR(64) NOT NULL,
				status VARCHAR(20) NOT NULL DEFAULT 'pending',
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				expires_at DATETIME NULL,
				PRIMARY KEY (id),
				UNIQUE KEY token (token),
				KEY team_id (team_id),
				KEY inviter_user_id (inviter_user_id),
				KEY status (status),
				KEY expires_at (expires_at)
			) $charset_collate;"
		);

		// Messages (mirrors models/Message.js)
		dbDelta(
			"CREATE TABLE $messages (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				sender_user_id BIGINT(20) UNSIGNED NOT NULL,
				receiver_user_id BIGINT(20) UNSIGNED NOT NULL,
				content LONGTEXT NOT NULL,
				is_read TINYINT(1) NOT NULL DEFAULT 0,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY sender_user_id (sender_user_id),
				KEY receiver_user_id (receiver_user_id),
				KEY is_read (is_read),
				KEY created_at (created_at)
			) $charset_collate;"
		);

		// Seed default notification settings template stored in an option for easy reuse.
		if ( false === get_option( 'jmgpm_default_notification_roles_json', false ) ) {
			$default = array(
				'admin'     => array( 'taskUpdates' => true, 'tasksAdded' => true, 'comments' => true, 'messages' => true ),
				'editor'    => array( 'taskUpdates' => true, 'tasksAdded' => true, 'comments' => true, 'messages' => true ),
				'commenter' => array( 'taskUpdates' => false, 'tasksAdded' => false, 'comments' => true, 'messages' => false ),
				'viewer'    => array( 'taskUpdates' => false, 'tasksAdded' => false, 'comments' => false, 'messages' => false ),
			);
			update_option( 'jmgpm_default_notification_roles_json', wp_json_encode( $default ), false );
		}
	}
}

