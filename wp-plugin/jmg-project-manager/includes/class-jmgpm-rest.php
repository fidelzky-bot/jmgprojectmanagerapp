<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class JMGPM_REST {
	private const NS = 'jmg/v1';

	public static function register_routes(): void {
		// Auth (parity with /api/auth)
		register_rest_route(
			self::NS,
			'/auth/register',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'auth_register' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			self::NS,
			'/auth/login',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'auth_login' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			self::NS,
			'/auth/me',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'auth_me' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Users (parity with /api/users)
		register_rest_route(
			self::NS,
			'/users',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'users_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/users/me',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'users_me' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'users_me_update' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/users/byProject/(?P<project_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'users_by_project' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Projects (parity with /api/projects)
		register_rest_route(
			self::NS,
			'/projects',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'projects_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'projects_create' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/projects/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'projects_get' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'projects_update' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'projects_delete' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Project roles (parity with /api/projects/:projectId/roles and notify toggle)
		register_rest_route(
			self::NS,
			'/projects/(?P<project_id>\d+)/roles',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'project_roles_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'project_roles_set' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'project_roles_remove' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/projects/(?P<project_id>\d+)/roles/(?P<user_id>\d+)/notify',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'project_roles_set_admin_notify' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Tasks (parity with /api/tasks)
		register_rest_route(
			self::NS,
			'/tasks',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'tasks_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'tasks_create' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/tasks/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'tasks_get' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'tasks_update' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'tasks_delete' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Task comments (parity with /api/tasks/:taskId/comments)
		register_rest_route(
			self::NS,
			'/tasks/(?P<task_id>\d+)/comments',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'task_comments_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'task_comments_create' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Teams (parity with /api/teams)
		register_rest_route(
			self::NS,
			'/teams',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'teams_create' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/teams/me',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'teams_me' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/teams/(?P<team_id>\d+)/members',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'teams_members_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/teams/(?P<team_id>\d+)/members/(?P<user_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'teams_members_remove' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Notifications (parity with /api/notifications + settings)
		register_rest_route(
			self::NS,
			'/notifications',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'notifications_list' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/notifications/(?P<id>\d+)/read',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'notifications_mark_read' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/notifications/mark-all-read',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'notifications_mark_all_read' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/notifications/settings/(?P<project_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'notification_settings_get' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'notification_settings_update' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Invites (parity with /api/invites)
		register_rest_route(
			self::NS,
			'/invites',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'invites_create' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/invites/generate-link',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'invites_generate_link' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/invites/join',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'invites_join' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		// Messages (parity with /api/messages)
		register_rest_route(
			self::NS,
			'/messages',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'messages_send' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/messages/conversation/(?P<user_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'messages_conversation' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/messages/read/(?P<sender_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'messages_mark_read' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/messages/unread-count',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'messages_unread_count' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/messages/conversations/recent',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'messages_recent_conversations' ),
					'permission_callback' => array( __CLASS__, 'require_login' ),
				),
			)
		);
	}

	/* ---------------------------- Permissions ---------------------------- */

	public static function require_login(): bool {
		return is_user_logged_in();
	}

	private static function current_user_id(): int {
		return (int) get_current_user_id();
	}

	private static function tables(): array {
		global $wpdb;
		return array(
			'teams'                  => $wpdb->prefix . 'jmgpm_teams',
			'team_members'           => $wpdb->prefix . 'jmgpm_team_members',
			'projects'               => $wpdb->prefix . 'jmgpm_projects',
			'project_members'        => $wpdb->prefix . 'jmgpm_project_members',
			'project_user_roles'     => $wpdb->prefix . 'jmgpm_project_user_roles',
			'tasks'                  => $wpdb->prefix . 'jmgpm_tasks',
			'comments'               => $wpdb->prefix . 'jmgpm_comments',
			'comment_mentions'       => $wpdb->prefix . 'jmgpm_comment_mentions',
			'notifications'          => $wpdb->prefix . 'jmgpm_notifications',
			'notification_settings'  => $wpdb->prefix . 'jmgpm_notification_settings',
			'invites'                => $wpdb->prefix . 'jmgpm_invites',
			'messages'               => $wpdb->prefix . 'jmgpm_messages',
		);
	}

	private static function get_user_team_id( int $user_id ): ?int {
		$team_id = (int) get_user_meta( $user_id, 'jmgpm_team_id', true );
		return $team_id > 0 ? $team_id : null;
	}

	private static function get_project_role( int $user_id, int $project_id ): string {
		global $wpdb;
		$t = self::tables();
		$role = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT role FROM {$t['project_user_roles']} WHERE project_id = %d AND user_id = %d",
				$project_id,
				$user_id
			)
		);
		return $role ? (string) $role : 'viewer';
	}

	private static function is_project_member( int $user_id, int $project_id ): bool {
		global $wpdb;
		$t = self::tables();
		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT 1 FROM {$t['project_members']} WHERE project_id = %d AND user_id = %d",
				$project_id,
				$user_id
			)
		);
		return (bool) $exists;
	}

	/**
	 * @return true|WP_Error
	 */
	private static function require_project_member( int $project_id ) {
		$user_id = self::current_user_id();
		if ( self::is_project_member( $user_id, $project_id ) ) {
			return true;
		}
		return new WP_Error( 'forbidden', 'You do not have access to this project.', array( 'status' => 403 ) );
	}

	/* ---------------------------- Notifications helpers ---------------------------- */

	private static function get_notification_roles_settings( int $project_id ): array {
		global $wpdb;
		$t = self::tables();

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT roles_json FROM {$t['notification_settings']} WHERE project_id = %d", $project_id ),
			ARRAY_A
		);
		if ( $row && isset( $row['roles_json'] ) ) {
			$decoded = json_decode( (string) $row['roles_json'], true );
			return is_array( $decoded ) ? $decoded : array();
		}

		$default_json = (string) get_option( 'jmgpm_default_notification_roles_json', '{}' );
		$wpdb->replace(
			$t['notification_settings'],
			array(
				'project_id' => $project_id,
				'roles_json' => $default_json,
			),
			array( '%d', '%s' )
		);
		$decoded = json_decode( $default_json, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	private static function insert_notification(
		int $user_id,
		int $sender_user_id,
		string $type,
		string $message,
		?string $entity_id = null,
		?string $entity_type = null,
		array $extra = array()
	): void {
		global $wpdb;
		$t = self::tables();

		$wpdb->insert(
			$t['notifications'],
			array(
				'user_id'        => $user_id,
				'sender_user_id' => $sender_user_id,
				'type'           => $type,
				'message'        => $message,
				'entity_id'      => $entity_id,
				'entity_type'    => $entity_type,
				'is_read'        => 0,
				'task_title'     => isset( $extra['taskTitle'] ) ? (string) $extra['taskTitle'] : null,
				'title'          => isset( $extra['title'] ) ? (string) $extra['title'] : null,
				'project_name'   => isset( $extra['projectName'] ) ? (string) $extra['projectName'] : null,
				'new_role'       => isset( $extra['newRole'] ) ? (string) $extra['newRole'] : null,
				'extra'          => ! empty( $extra ) ? wp_json_encode( $extra ) : null,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Mirror of controllers/notificationController.js sendRoleBasedNotifications.
	 * Uses project roles + admin notify_all toggle.
	 */
	private static function notify_role_based(
		int $project_id,
		string $type,
		string $message,
		?string $entity_id,
		?string $entity_type,
		int $by_user_id,
		array $extra = array()
	): void {
		global $wpdb;
		$t = self::tables();

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT user_id, role, notify_all FROM {$t['project_user_roles']} WHERE project_id = %d", $project_id ),
			ARRAY_A
		);

		$targets = array();
		foreach ( $rows as $r ) {
			$uid       = (int) $r['user_id'];
			$role      = (string) $r['role'];
			$notify_all = (int) $r['notify_all'] === 1;

			if ( $uid === $by_user_id ) {
				continue;
			}

			if ( 'comments' === $type ) {
				if ( 'commenter' === $role || ( 'admin' === $role && $notify_all ) ) {
					$targets[] = $uid;
				}
			} elseif ( 'messages' === $type ) {
				if ( in_array( $role, array( 'editor', 'commenter' ), true ) || ( 'admin' === $role && $notify_all ) ) {
					$targets[] = $uid;
				}
			} elseif ( in_array( $type, array( 'tasksMoved', 'tasksEdited', 'tasksAdded', 'task_deleted' ), true ) ) {
				if ( 'editor' === $role || ( 'admin' === $role && $notify_all ) ) {
					$targets[] = $uid;
				}
			} elseif ( 'adminOnly' === $type ) {
				if ( 'admin' === $role && $notify_all ) {
					$targets[] = $uid;
				}
			}
		}

		$targets = array_values( array_unique( $targets ) );
		foreach ( $targets as $uid ) {
			self::insert_notification( (int) $uid, $by_user_id, $type, $message, $entity_id, $entity_type, $extra );
		}
	}

	/**
	 * Mirror of controllers/notificationController.js sendProjectNotifications.
	 * Uses per-project notification settings + roles mapping.
	 */
	private static function notify_project_settings(
		int $project_id,
		string $type,
		string $message,
		?string $entity_id,
		?string $entity_type,
		int $by_user_id,
		array $extra = array()
	): void {
		global $wpdb;
		$t = self::tables();

		$settings = self::get_notification_roles_settings( $project_id );
		$roles    = $wpdb->get_results(
			$wpdb->prepare( "SELECT user_id, role FROM {$t['project_user_roles']} WHERE project_id = %d", $project_id ),
			ARRAY_A
		);

		$user_ids = array();
		if ( 'task_assigned' === $type && isset( $extra['assignedTo'] ) ) {
			$user_ids = array( (int) $extra['assignedTo'] );
		} else {
			$notifKey = null;
			if ( in_array( $type, array( 'statusUpdates', 'tasksEdited', 'tasksMoved', 'task_updated' ), true ) ) {
				$notifKey = 'taskUpdates';
			} elseif ( 'tasksAdded' === $type ) {
				$notifKey = 'tasksAdded';
			} elseif ( 'messages' === $type ) {
				$notifKey = 'messages';
			} elseif ( 'comments' === $type ) {
				$notifKey = 'comments';
			}

			foreach ( $roles as $r ) {
				$uid  = (int) $r['user_id'];
				$role = (string) $r['role'];
				if ( $uid === $by_user_id ) {
					continue;
				}
				if ( $notifKey && isset( $settings[ $role ][ $notifKey ] ) && true === (bool) $settings[ $role ][ $notifKey ] ) {
					$user_ids[] = $uid;
				}
			}
		}

		$user_ids = array_values( array_unique( array_filter( $user_ids ) ) );
		foreach ( $user_ids as $uid ) {
			self::insert_notification( (int) $uid, $by_user_id, $type, $message, $entity_id, $entity_type, $extra );
		}
	}

	/**
	 * @return true|WP_Error
	 */
	private static function require_project_role( int $project_id, array $allowed_roles ) {
		$user_id = self::current_user_id();
		$role    = self::get_project_role( $user_id, $project_id );
		if ( in_array( $role, $allowed_roles, true ) ) {
			return true;
		}
		return new WP_Error( 'forbidden', 'You do not have permission.', array( 'status' => 403 ) );
	}

	/* ---------------------------- Auth ---------------------------- */

	private static function user_payload( WP_User $u ): array {
		$user_id = (int) $u->ID;
		return array(
			'_id'       => $user_id, // keep Mongo-style key for frontend compatibility
			'id'        => $user_id,
			'name'      => $u->display_name,
			'nickname'  => (string) get_user_meta( $user_id, 'jmgpm_nickname', true ),
			'email'     => $u->user_email,
			'avatar'    => get_avatar_url( $user_id ),
			'contact'   => (string) get_user_meta( $user_id, 'jmgpm_contact', true ),
			'jobTitle'  => (string) get_user_meta( $user_id, 'jmgpm_job_title', true ),
			'bio'       => (string) get_user_meta( $user_id, 'jmgpm_bio', true ),
			'birthday'  => (string) get_user_meta( $user_id, 'jmgpm_birthday', true ),
			'occupation'=> (string) get_user_meta( $user_id, 'jmgpm_occupation', true ),
			'hobby'     => (string) get_user_meta( $user_id, 'jmgpm_hobby', true ),
			'team'      => self::get_user_team_id( $user_id ),
			'role'      => (string) get_user_meta( $user_id, 'jmgpm_team_role', true ),
			'lastActive'=> (string) get_user_meta( $user_id, 'jmgpm_last_active', true ),
		);
	}

	public static function auth_register( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$name        = trim( (string) $req->get_param( 'name' ) );
		$email       = trim( (string) $req->get_param( 'email' ) );
		$password    = (string) $req->get_param( 'password' );
		$inviteToken = (string) $req->get_param( 'inviteToken' );

		if ( '' === $name || '' === $email || '' === $password ) {
			return new WP_Error( 'bad_request', 'Name, email and password required.', array( 'status' => 400 ) );
		}
		if ( email_exists( $email ) ) {
			return new WP_Error( 'bad_request', 'Email already exists.', array( 'status' => 400 ) );
		}

		$username = sanitize_user( current( explode( '@', $email ) ), true );
		if ( '' === $username || username_exists( $username ) ) {
			$username = 'user' . wp_rand( 1000, 999999 );
		}

		$user_id = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		wp_update_user(
			array(
				'ID'           => $user_id,
				'display_name' => $name,
				'nickname'     => $name,
			)
		);

		// Handle invite token if present (parity with routes/auth.js)
		if ( '' !== $inviteToken ) {
			$invite = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$t['invites']} WHERE token = %s AND status = 'pending' AND (expires_at IS NULL OR expires_at > UTC_TIMESTAMP())",
					$inviteToken
				),
				ARRAY_A
			);
			if ( ! $invite ) {
				return new WP_Error( 'bad_request', 'Invalid or expired invite token.', array( 'status' => 400 ) );
			}

			$team_id = (int) $invite['team_id'];
			$wpdb->replace( $t['team_members'], array( 'team_id' => $team_id, 'user_id' => $user_id ), array( '%d', '%d' ) );
			update_user_meta( $user_id, 'jmgpm_team_id', $team_id );
			update_user_meta( $user_id, 'jmgpm_team_role', 'member' );

			// Add user to all projects of the team and assign default viewer role
			$project_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$t['projects']} WHERE team_id = %d", $team_id ) );
			foreach ( $project_ids as $pid ) {
				$pid = (int) $pid;
				$wpdb->replace( $t['project_members'], array( 'project_id' => $pid, 'user_id' => $user_id ), array( '%d', '%d' ) );
				$wpdb->replace(
					$t['project_user_roles'],
					array( 'project_id' => $pid, 'user_id' => $user_id, 'role' => 'viewer', 'notify_all' => 1 ),
					array( '%d', '%d', '%s', '%d' )
				);
			}

			$wpdb->update( $t['invites'], array( 'status' => 'accepted' ), array( 'id' => (int) $invite['id'] ), array( '%s' ), array( '%d' ) );
			return rest_ensure_response( array( 'message' => 'User created' ) );
		}

		// If no invite token, auto-create a team (parity with routes/auth.js)
		$team_name = $name . "'s Team";
		$wpdb->insert(
			$t['teams'],
			array(
				'name'          => $team_name,
				'description'   => '',
				'owner_user_id' => $user_id,
			),
			array( '%s', '%s', '%d' )
		);
		$team_id = (int) $wpdb->insert_id;
		$wpdb->replace( $t['team_members'], array( 'team_id' => $team_id, 'user_id' => $user_id ), array( '%d', '%d' ) );
		update_user_meta( $user_id, 'jmgpm_team_id', $team_id );
		update_user_meta( $user_id, 'jmgpm_team_role', 'admin' );

		return rest_ensure_response( array( 'message' => 'User created' ) );
	}

	public static function auth_login( WP_REST_Request $req ) {
		$email    = trim( (string) $req->get_param( 'email' ) );
		$password = (string) $req->get_param( 'password' );
		if ( '' === $email || '' === $password ) {
			return new WP_Error( 'bad_request', 'Email and password required.', array( 'status' => 400 ) );
		}

		$by_email = get_user_by( 'email', $email );
		$login    = $by_email ? $by_email->user_login : $email;
		$user     = wp_authenticate( $login, $password );
		if ( is_wp_error( $user ) ) {
			return new WP_Error( 'bad_request', 'Invalid credentials', array( 'status' => 400 ) );
		}

		// For headless clients: return an Application Password they can use as a token.
		// Client can then call REST with Basic Auth using username + appPassword.
		if ( class_exists( 'WP_Application_Passwords' ) ) {
			$created = WP_Application_Passwords::create_new_application_password(
				$user->ID,
				array( 'name' => 'JMG Project Manager' )
			);
			if ( is_array( $created ) && isset( $created[0] ) ) {
				return rest_ensure_response( array( 'token' => $created[0], 'userId' => (int) $user->ID ) );
			}
		}

		// Fallback: instruct clients to use cookie auth (same-origin) if app passwords are unavailable.
		return rest_ensure_response( array( 'token' => null, 'userId' => (int) $user->ID ) );
	}

	public static function auth_me( WP_REST_Request $req ) {
		$user_id = self::current_user_id();
		update_user_meta( $user_id, 'jmgpm_last_active', gmdate( 'c' ) );
		$u = get_user_by( 'id', $user_id );
		if ( ! $u ) {
			return new WP_Error( 'not_found', 'User not found', array( 'status' => 404 ) );
		}
		return rest_ensure_response( self::user_payload( $u ) );
	}

	/* ---------------------------- Users ---------------------------- */

	public static function users_list( WP_REST_Request $req ) {
		$users = get_users(
			array(
				'number' => 500,
				'fields' => array( 'ID', 'display_name', 'user_email' ),
			)
		);
		$out = array();
		foreach ( $users as $u ) {
			$out[] = self::user_payload( get_user_by( 'id', (int) $u->ID ) );
		}
		return rest_ensure_response( $out );
	}

	public static function users_me( WP_REST_Request $req ) {
		return self::auth_me( $req );
	}

	public static function users_me_update( WP_REST_Request $req ) {
		$user_id = self::current_user_id();
		$fields = array(
			'name'       => 'display_name',
			'nickname'   => 'nickname',
		);
		$update = array( 'ID' => $user_id );
		foreach ( $fields as $in => $wp_field ) {
			if ( null !== $req->get_param( $in ) ) {
				$update[ $wp_field ] = (string) $req->get_param( $in );
			}
		}
		if ( count( $update ) > 1 ) {
			wp_update_user( $update );
		}

		$meta_map = array(
			'contact'    => 'jmgpm_contact',
			'phone'      => 'jmgpm_contact',
			'jobTitle'   => 'jmgpm_job_title',
			'bio'        => 'jmgpm_bio',
			'birthday'   => 'jmgpm_birthday',
			'occupation' => 'jmgpm_occupation',
			'hobby'      => 'jmgpm_hobby',
			'nickname'   => 'jmgpm_nickname',
		);
		foreach ( $meta_map as $in => $meta_key ) {
			if ( null !== $req->get_param( $in ) ) {
				update_user_meta( $user_id, $meta_key, (string) $req->get_param( $in ) );
			}
		}

		$u = get_user_by( 'id', $user_id );
		return rest_ensure_response( self::user_payload( $u ) );
	}

	public static function users_by_project( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req['project_id'];
		$user_ids = $wpdb->get_col(
			$wpdb->prepare( "SELECT user_id FROM {$t['project_members']} WHERE project_id = %d", $project_id )
		);
		$out = array();
		foreach ( $user_ids as $uid ) {
			$u = get_user_by( 'id', (int) $uid );
			if ( $u ) {
				$out[] = self::user_payload( $u );
			}
		}
		return rest_ensure_response( $out );
	}

	/* ---------------------------- Projects ---------------------------- */

	public static function projects_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$user_id = self::current_user_id();
		$team_id = self::get_user_team_id( $user_id );
		if ( ! $team_id ) {
			return rest_ensure_response( array() );
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$t['projects']} WHERE team_id = %d ORDER BY created_at DESC",
				$team_id
			),
			ARRAY_A
		);
		return rest_ensure_response( $rows );
	}

	public static function projects_create( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$user_id = self::current_user_id();
		$team_id = (int) $req->get_param( 'team' );
		if ( $team_id <= 0 ) {
			$team_id = self::get_user_team_id( $user_id ) ?: 0;
		}
		if ( $team_id <= 0 ) {
			return new WP_Error( 'bad_request', 'Team is required.', array( 'status' => 400 ) );
		}

		$name = trim( (string) $req->get_param( 'name' ) );
		if ( '' === $name ) {
			return new WP_Error( 'bad_request', 'Name is required.', array( 'status' => 400 ) );
		}

		$description = (string) $req->get_param( 'description' );
		$status      = (string) ( $req->get_param( 'status' ) ?: 'active' );

		$inserted = $wpdb->insert(
			$t['projects'],
			array(
				'name'               => $name,
				'description'        => $description,
				'team_id'            => $team_id,
				'created_by_user_id' => $user_id,
				'status'             => $status,
			),
			array( '%s', '%s', '%d', '%d', '%s' )
		);

		if ( false === $inserted ) {
			return new WP_Error( 'db_error', 'Failed to create project.', array( 'status' => 500 ) );
		}

		$project_id = (int) $wpdb->insert_id;

		// Creator becomes admin (parity with controllers/projectController.js createProject)
		$wpdb->replace(
			$t['project_user_roles'],
			array(
				'project_id' => $project_id,
				'user_id'    => $user_id,
				'role'       => 'admin',
				'notify_all' => 1,
			),
			array( '%d', '%d', '%s', '%d' )
		);

		$wpdb->replace(
			$t['project_members'],
			array(
				'project_id' => $project_id,
				'user_id'    => $user_id,
			),
			array( '%d', '%d' )
		);

		// Ensure notification settings row exists.
		self::get_notification_roles_settings( $project_id );

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$t['projects']} WHERE id = %d", $project_id ),
			ARRAY_A
		);
		return rest_ensure_response( $row );
	}

	public static function projects_get( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$id = (int) $req['id'];

		$perm = self::require_project_member( $id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$t['projects']} WHERE id = %d", $id ),
			ARRAY_A
		);
		if ( ! $row ) {
			return new WP_Error( 'not_found', 'Project not found.', array( 'status' => 404 ) );
		}
		return rest_ensure_response( $row );
	}

	public static function projects_update( WP_REST_Request $req ) {
		global $wpdb;
		$t  = self::tables();
		$id = (int) $req['id'];

		$perm = self::require_project_member( $id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$perm = self::require_project_role( $id, array( 'admin', 'editor' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$data   = array();
		$format = array();

		foreach ( array( 'name' => '%s', 'description' => '%s', 'status' => '%s' ) as $field => $fmt ) {
			if ( null !== $req->get_param( $field ) ) {
				$data[ $field ] = (string) $req->get_param( $field );
				$format[]       = $fmt;
			}
		}

		if ( empty( $data ) ) {
			return new WP_Error( 'bad_request', 'No fields to update.', array( 'status' => 400 ) );
		}

		$updated = $wpdb->update( $t['projects'], $data, array( 'id' => $id ), $format, array( '%d' ) );
		if ( false === $updated ) {
			return new WP_Error( 'db_error', 'Failed to update project.', array( 'status' => 500 ) );
		}

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['projects']} WHERE id = %d", $id ), ARRAY_A );
		return rest_ensure_response( $row );
	}

	public static function projects_delete( WP_REST_Request $req ) {
		global $wpdb;
		$t  = self::tables();
		$id = (int) $req['id'];

		$perm = self::require_project_member( $id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$perm = self::require_project_role( $id, array( 'admin' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$deleted = $wpdb->delete( $t['projects'], array( 'id' => $id ), array( '%d' ) );
		if ( ! $deleted ) {
			return new WP_Error( 'not_found', 'Project not found.', array( 'status' => 404 ) );
		}
		return rest_ensure_response( array( 'message' => 'Project deleted' ) );
	}

	/* ---------------------------- Project roles ---------------------------- */

	public static function project_roles_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req['project_id'];

		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT project_id, user_id, role, notify_all FROM {$t['project_user_roles']} WHERE project_id = %d", $project_id ),
			ARRAY_A
		);
		return rest_ensure_response( $rows );
	}

	public static function project_roles_set( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$project_id  = (int) $req['project_id'];
		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$requester   = self::current_user_id();
		$requester_r = self::get_project_role( $requester, $project_id );
		if ( 'admin' !== $requester_r ) {
			return new WP_Error( 'forbidden', 'Only project admins can manage roles.', array( 'status' => 403 ) );
		}

		$user_id = (int) $req->get_param( 'userId' );
		$role    = (string) $req->get_param( 'role' );
		if ( $user_id <= 0 || '' === $role ) {
			return new WP_Error( 'bad_request', 'userId and role are required.', array( 'status' => 400 ) );
		}

		$wpdb->replace(
			$t['project_user_roles'],
			array(
				'project_id' => $project_id,
				'user_id'    => $user_id,
				'role'       => $role,
				'notify_all' => 1,
			),
			array( '%d', '%d', '%s', '%d' )
		);

		$wpdb->replace(
			$t['project_members'],
			array(
				'project_id' => $project_id,
				'user_id'    => $user_id,
			),
			array( '%d', '%d' )
		);

		// Notify user whose role changed (parity with projectUserRoleController.js)
		$project = $wpdb->get_row( $wpdb->prepare( "SELECT name FROM {$t['projects']} WHERE id = %d", $project_id ), ARRAY_A );
		$project_name = $project ? (string) $project['name'] : '';
		self::notify_project_settings(
			$project_id,
			'role_changed',
			'Your role was changed to ' . $role . ' in project ' . $project_name,
			(string) $project_id,
			'Project',
			$requester,
			array( 'newRole' => $role, 'projectName' => $project_name )
		);

		return rest_ensure_response(
			array(
				'projectId' => $project_id,
				'userId'    => $user_id,
				'role'      => $role,
			)
		);
	}

	public static function project_roles_remove( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$project_id  = (int) $req['project_id'];
		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$requester   = self::current_user_id();
		$requester_r = self::get_project_role( $requester, $project_id );
		if ( 'admin' !== $requester_r ) {
			return new WP_Error( 'forbidden', 'Only project admins can manage roles.', array( 'status' => 403 ) );
		}

		$user_id = (int) $req->get_param( 'userId' );
		if ( $user_id <= 0 ) {
			return new WP_Error( 'bad_request', 'userId is required.', array( 'status' => 400 ) );
		}

		$wpdb->delete( $t['project_user_roles'], array( 'project_id' => $project_id, 'user_id' => $user_id ), array( '%d', '%d' ) );
		$wpdb->delete( $t['project_members'], array( 'project_id' => $project_id, 'user_id' => $user_id ), array( '%d', '%d' ) );

		$project = $wpdb->get_row( $wpdb->prepare( "SELECT name FROM {$t['projects']} WHERE id = %d", $project_id ), ARRAY_A );
		$project_name = $project ? (string) $project['name'] : '';
		self::notify_project_settings(
			$project_id,
			'member_removed',
			'You were removed from the project ' . $project_name,
			(string) $project_id,
			'Project',
			$requester,
			array( 'projectName' => $project_name )
		);

		return rest_ensure_response( array( 'message' => 'Role removed' ) );
	}

	public static function project_roles_set_admin_notify( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req['project_id'];
		$user_id    = (int) $req['user_id'];

		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$requester   = self::current_user_id();
		$requester_r = self::get_project_role( $requester, $project_id );
		if ( 'admin' !== $requester_r ) {
			return new WP_Error( 'forbidden', 'Only project admins can update notification preferences.', array( 'status' => 403 ) );
		}

		$notify_all = (int) (bool) $req->get_param( 'notifyAll' );

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT role FROM {$t['project_user_roles']} WHERE project_id = %d AND user_id = %d", $project_id, $user_id ),
			ARRAY_A
		);
		if ( ! $row || 'admin' !== (string) $row['role'] ) {
			return new WP_Error( 'bad_request', 'User is not an admin for this project.', array( 'status' => 400 ) );
		}

		$wpdb->update(
			$t['project_user_roles'],
			array( 'notify_all' => $notify_all ),
			array( 'project_id' => $project_id, 'user_id' => $user_id ),
			array( '%d' ),
			array( '%d', '%d' )
		);

		return rest_ensure_response( array( 'success' => true, 'notifyAll' => (bool) $notify_all ) );
	}

	/* ---------------------------- Tasks ---------------------------- */

	public static function tasks_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req->get_param( 'project' );

		if ( $project_id > 0 ) {
			$perm = self::require_project_member( $project_id );
			if ( is_wp_error( $perm ) ) {
				return $perm;
			}
			$rows = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE project_id = %d ORDER BY created_at DESC", $project_id ),
				ARRAY_A
			);
			return rest_ensure_response( $rows );
		}

		$rows = $wpdb->get_results( "SELECT * FROM {$t['tasks']} ORDER BY created_at DESC", ARRAY_A );
		return rest_ensure_response( $rows );
	}

	public static function tasks_create( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$project_id = (int) $req->get_param( 'project' );
		if ( $project_id <= 0 ) {
			return new WP_Error( 'bad_request', 'Project is required.', array( 'status' => 400 ) );
		}

		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$perm = self::require_project_role( $project_id, array( 'admin', 'editor' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$title = trim( (string) $req->get_param( 'title' ) );
		if ( '' === $title ) {
			return new WP_Error( 'bad_request', 'Title is required.', array( 'status' => 400 ) );
		}

		$user_id    = self::current_user_id();
		$desc       = (string) $req->get_param( 'description' );
		$status     = (string) ( $req->get_param( 'status' ) ?: 'To Do' );
		$priority   = (string) ( $req->get_param( 'priority' ) ?: 'Low' );
		$assigned   = $req->get_param( 'assignedTo' );
		$assigned_i = null === $assigned ? null : (int) $assigned;
		$due_date   = $req->get_param( 'dueDate' );
		$due_dt     = $due_date ? gmdate( 'Y-m-d H:i:s', strtotime( (string) $due_date ) ) : null;
		$attachments = $req->get_param( 'attachments' );
		$attachments_json = null !== $attachments ? wp_json_encode( $attachments ) : null;

		$ok = $wpdb->insert(
			$t['tasks'],
			array(
				'project_id'           => $project_id,
				'owner_user_id'        => $user_id,
				'assigned_to_user_id'  => $assigned_i ?: null,
				'title'                => $title,
				'description'          => $desc,
				'status'               => $status,
				'priority'             => $priority,
				'due_date'             => $due_dt,
				'attachments'          => $attachments_json,
			),
			array( '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $ok ) {
			return new WP_Error( 'db_error', 'Failed to create task.', array( 'status' => 500 ) );
		}

		$id  = (int) $wpdb->insert_id;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE id = %d", $id ), ARRAY_A );

		// Notifications parity (task assigned + tasksAdded)
		$by_name = ( get_user_by( 'id', $user_id ) ? get_user_by( 'id', $user_id )->display_name : 'User' );
		if ( $assigned_i && $assigned_i > 0 ) {
			self::notify_project_settings(
				$project_id,
				'task_assigned',
				$by_name . ' assigned you to ' . $title,
				(string) $id,
				'Task',
				$user_id,
				array( 'assignedTo' => $assigned_i, 'taskTitle' => $title, 'title' => $title, 'byName' => $by_name )
			);
		}
		self::notify_role_based(
			$project_id,
			'tasksAdded',
			'A new task was added: ' . $title,
			(string) $id,
			'Task',
			$user_id,
			array( 'action' => 'created', 'taskId' => $id, 'title' => $title, 'by' => $user_id, 'byName' => $by_name, 'time' => gmdate( 'c' ), 'project' => $project_id )
		);

		return rest_ensure_response( $row );
	}

	public static function tasks_get( WP_REST_Request $req ) {
		global $wpdb;
		$t  = self::tables();
		$id = (int) $req['id'];
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE id = %d", $id ), ARRAY_A );
		if ( ! $row ) {
			return new WP_Error( 'not_found', 'Task not found.', array( 'status' => 404 ) );
		}
		$perm = self::require_project_member( (int) $row['project_id'] );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		return rest_ensure_response( $row );
	}

	public static function tasks_update( WP_REST_Request $req ) {
		global $wpdb;
		$t  = self::tables();
		$id = (int) $req['id'];

		$old = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE id = %d", $id ), ARRAY_A );
		if ( ! $old ) {
			return new WP_Error( 'not_found', 'Task not found.', array( 'status' => 404 ) );
		}

		$project_id = (int) $old['project_id'];
		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$perm       = self::require_project_role( $project_id, array( 'admin', 'editor' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$data   = array();
		$format = array();
		foreach ( array( 'title' => '%s', 'description' => '%s', 'status' => '%s', 'priority' => '%s' ) as $field => $fmt ) {
			if ( null !== $req->get_param( $field ) ) {
				$data[ $field ] = (string) $req->get_param( $field );
				$format[]       = $fmt;
			}
		}
		if ( null !== $req->get_param( 'assignedTo' ) ) {
			$data['assigned_to_user_id'] = (int) $req->get_param( 'assignedTo' );
			$format[] = '%d';
		}
		if ( null !== $req->get_param( 'dueDate' ) ) {
			$due_date = $req->get_param( 'dueDate' );
			$data['due_date'] = $due_date ? gmdate( 'Y-m-d H:i:s', strtotime( (string) $due_date ) ) : null;
			$format[] = '%s';
		}
		if ( null !== $req->get_param( 'attachments' ) ) {
			$data['attachments'] = wp_json_encode( $req->get_param( 'attachments' ) );
			$format[] = '%s';
		}

		if ( empty( $data ) ) {
			return new WP_Error( 'bad_request', 'No fields to update.', array( 'status' => 400 ) );
		}

		$updated = $wpdb->update( $t['tasks'], $data, array( 'id' => $id ), $format, array( '%d' ) );
		if ( false === $updated ) {
			return new WP_Error( 'db_error', 'Failed to update task.', array( 'status' => 500 ) );
		}

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE id = %d", $id ), ARRAY_A );

		// Notifications parity (tasksMoved vs tasksEdited + assignment change)
		$updater_id = self::current_user_id();
		$updater_name = ( get_user_by( 'id', $updater_id ) ? get_user_by( 'id', $updater_id )->display_name : 'User' );
		$new_status = isset( $data['status'] ) ? (string) $data['status'] : null;
		$title_now  = isset( $row['title'] ) ? (string) $row['title'] : (string) ( $data['title'] ?? '' );

		if ( null !== $new_status && (string) $old['status'] !== $new_status ) {
			self::notify_role_based(
				$project_id,
				'tasksMoved',
				$updater_name . ' moved ' . $title_now . ' to ' . $new_status,
				(string) $id,
				'Task',
				$updater_id,
				array(
					'action'    => 'moved',
					'taskId'    => $id,
					'title'     => $title_now,
					'by'        => $updater_id,
					'byName'    => $updater_name,
					'time'      => gmdate( 'c' ),
					'project'   => $project_id,
					'status'    => $new_status,
					'oldStatus' => (string) $old['status'],
				)
			);
		} else {
			self::notify_role_based(
				$project_id,
				'tasksEdited',
				$updater_name . ' updated ' . $title_now,
				(string) $id,
				'Task',
				$updater_id,
				array(
					'action'  => 'updated',
					'taskId'  => $id,
					'title'   => $title_now,
					'by'      => $updater_id,
					'byName'  => $updater_name,
					'time'    => gmdate( 'c' ),
					'project' => $project_id,
					'status'  => (string) ( $row['status'] ?? '' ),
				)
			);
		}

		if ( array_key_exists( 'assigned_to_user_id', $data ) ) {
			$new_assigned = (int) $data['assigned_to_user_id'];
			$old_assigned = isset( $old['assigned_to_user_id'] ) ? (int) $old['assigned_to_user_id'] : 0;
			if ( $new_assigned > 0 && $new_assigned !== $old_assigned ) {
				self::notify_project_settings(
					$project_id,
					'task_assigned',
					$updater_name . ' assigned you to ' . $title_now,
					(string) $id,
					'Task',
					$updater_id,
					array( 'assignedTo' => $new_assigned, 'taskTitle' => $title_now, 'title' => $title_now, 'byName' => $updater_name )
				);
			}
		}

		return rest_ensure_response( $row );
	}

	public static function tasks_delete( WP_REST_Request $req ) {
		global $wpdb;
		$t  = self::tables();
		$id = (int) $req['id'];

		$task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['tasks']} WHERE id = %d", $id ), ARRAY_A );
		if ( ! $task ) {
			return new WP_Error( 'not_found', 'Task not found.', array( 'status' => 404 ) );
		}

		$project_id = (int) $task['project_id'];
		$perm = self::require_project_member( $project_id );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}
		$perm       = self::require_project_role( $project_id, array( 'admin' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$wpdb->delete( $t['tasks'], array( 'id' => $id ), array( '%d' ) );

		$deleter_id = self::current_user_id();
		$deleter_name = ( get_user_by( 'id', $deleter_id ) ? get_user_by( 'id', $deleter_id )->display_name : 'User' );
		self::notify_role_based(
			$project_id,
			'task_deleted',
			$deleter_name . ' deleted the task: ' . (string) $task['title'],
			(string) $id,
			'Task',
			$deleter_id,
			array( 'action' => 'deleted', 'taskId' => $id, 'title' => (string) $task['title'], 'by' => $deleter_id, 'byName' => $deleter_name, 'time' => gmdate( 'c' ), 'project' => $project_id )
		);

		return rest_ensure_response( array( 'message' => 'Task deleted' ) );
	}

	/* ---------------------------- Task comments ---------------------------- */

	public static function task_comments_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$task_id = (int) $req['task_id'];
		$task = $wpdb->get_row( $wpdb->prepare( "SELECT project_id FROM {$t['tasks']} WHERE id = %d", $task_id ), ARRAY_A );
		if ( ! $task ) {
			return new WP_Error( 'not_found', 'Task not found.', array( 'status' => 404 ) );
		}
		$perm = self::require_project_member( (int) $task['project_id'] );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$t['comments']} WHERE task_id = %d ORDER BY created_at ASC", $task_id ),
			ARRAY_A
		);
		return rest_ensure_response( $rows );
	}

	public static function task_comments_create( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$task_id = (int) $req['task_id'];
		$text    = trim( (string) $req->get_param( 'text' ) );
		if ( $task_id <= 0 || '' === $text ) {
			return new WP_Error( 'bad_request', 'task_id and text are required.', array( 'status' => 400 ) );
		}

		$task = $wpdb->get_row( $wpdb->prepare( "SELECT project_id FROM {$t['tasks']} WHERE id = %d", $task_id ), ARRAY_A );
		if ( ! $task ) {
			return new WP_Error( 'not_found', 'Task not found.', array( 'status' => 404 ) );
		}

		$project_id = (int) $task['project_id'];
		$perm       = self::require_project_role( $project_id, array( 'admin', 'editor', 'commenter' ) );
		if ( is_wp_error( $perm ) ) {
			return $perm;
		}

		$user_id = self::current_user_id();
		$ok      = $wpdb->insert(
			$t['comments'],
			array(
				'task_id'        => $task_id,
				'author_user_id' => $user_id,
				'text'           => $text,
			),
			array( '%d', '%d', '%s' )
		);
		if ( false === $ok ) {
			return new WP_Error( 'db_error', 'Failed to add comment.', array( 'status' => 500 ) );
		}

		$comment_id = (int) $wpdb->insert_id;
		$mentions   = $req->get_param( 'mentions' );
		if ( is_array( $mentions ) ) {
			foreach ( $mentions as $mentioned_user_id ) {
				$mentioned_user_id = (int) $mentioned_user_id;
				if ( $mentioned_user_id > 0 ) {
					$wpdb->replace(
						$t['comment_mentions'],
						array(
							'comment_id' => $comment_id,
							'user_id'    => $mentioned_user_id,
						),
						array( '%d', '%d' )
					);
				}
			}
		}

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['comments']} WHERE id = %d", $comment_id ), ARRAY_A );

		// Notify commenters/admins (parity with commentController.js)
		$task_row = $wpdb->get_row( $wpdb->prepare( "SELECT title, project_id FROM {$t['tasks']} WHERE id = %d", $task_id ), ARRAY_A );
		$task_title = $task_row ? (string) $task_row['title'] : '';
		$by_name = ( get_user_by( 'id', $user_id ) ? get_user_by( 'id', $user_id )->display_name : 'User' );
		self::notify_role_based(
			$project_id,
			'comments',
			'New comment on task: ' . $task_title,
			(string) $comment_id,
			'Comment',
			$user_id,
			array( 'action' => 'commented', 'taskId' => $task_id, 'title' => $task_title, 'by' => $user_id, 'byName' => $by_name, 'time' => gmdate( 'c' ), 'project' => $project_id )
		);

		// Mention notifications (in-app) to mentioned users
		if ( is_array( $mentions ) ) {
			foreach ( $mentions as $mentioned_user_id ) {
				$mentioned_user_id = (int) $mentioned_user_id;
				if ( $mentioned_user_id > 0 && $mentioned_user_id !== $user_id ) {
					self::insert_notification(
						$mentioned_user_id,
						$user_id,
						'task_mentioned',
						$by_name . ' mentioned you in ' . $task_title,
						(string) $task_id,
						'Task',
						array( 'taskTitle' => $task_title, 'title' => $task_title, 'projectName' => '' )
					);
				}
			}
		}

		return rest_ensure_response( $row );
	}

	/* ---------------------------- Teams ---------------------------- */

	public static function teams_create( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$user_id = self::current_user_id();

		$name = trim( (string) $req->get_param( 'name' ) );
		if ( '' === $name ) {
			return new WP_Error( 'bad_request', 'Name is required.', array( 'status' => 400 ) );
		}

		$desc = (string) $req->get_param( 'description' );
		$ok = $wpdb->insert(
			$t['teams'],
			array(
				'name'          => $name,
				'description'   => $desc,
				'owner_user_id' => $user_id,
			),
			array( '%s', '%s', '%d' )
		);
		if ( false === $ok ) {
			return new WP_Error( 'db_error', 'Failed to create team.', array( 'status' => 500 ) );
		}

		$team_id = (int) $wpdb->insert_id;
		$wpdb->replace(
			$t['team_members'],
			array( 'team_id' => $team_id, 'user_id' => $user_id ),
			array( '%d', '%d' )
		);
		update_user_meta( $user_id, 'jmgpm_team_id', $team_id );
		update_user_meta( $user_id, 'jmgpm_team_role', 'admin' );

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['teams']} WHERE id = %d", $team_id ), ARRAY_A );
		return rest_ensure_response( $row );
	}

	public static function teams_me( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$user_id = self::current_user_id();
		$team_id = self::get_user_team_id( $user_id );
		if ( ! $team_id ) {
			return new WP_Error( 'not_found', 'User is not part of a team.', array( 'status' => 404 ) );
		}
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['teams']} WHERE id = %d", $team_id ), ARRAY_A );
		if ( ! $row ) {
			return new WP_Error( 'not_found', 'Team not found.', array( 'status' => 404 ) );
		}
		return rest_ensure_response( $row );
	}

	public static function teams_members_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$team_id = (int) $req['team_id'];

		$user_ids = $wpdb->get_col(
			$wpdb->prepare( "SELECT user_id FROM {$t['team_members']} WHERE team_id = %d", $team_id )
		);

		$users = array();
		foreach ( $user_ids as $uid ) {
			$u = get_user_by( 'id', (int) $uid );
			if ( $u ) {
				$users[] = array(
					'id'       => (int) $u->ID,
					'name'     => $u->display_name,
					'email'    => $u->user_email,
					'avatar'   => get_avatar_url( $u->ID ),
					'lastActive' => null,
				);
			}
		}
		return rest_ensure_response( $users );
	}

	public static function teams_members_remove( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$team_id = (int) $req['team_id'];
		$user_id = (int) $req['user_id'];
		$requester = self::current_user_id();

		$team = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['teams']} WHERE id = %d", $team_id ), ARRAY_A );
		if ( ! $team ) {
			return new WP_Error( 'not_found', 'Team not found.', array( 'status' => 404 ) );
		}
		if ( (int) $team['owner_user_id'] !== $requester ) {
			return new WP_Error( 'forbidden', 'Only team owner can remove members.', array( 'status' => 403 ) );
		}
		if ( $user_id === (int) $team['owner_user_id'] ) {
			return new WP_Error( 'bad_request', 'Cannot remove team owner.', array( 'status' => 400 ) );
		}

		$wpdb->delete( $t['team_members'], array( 'team_id' => $team_id, 'user_id' => $user_id ), array( '%d', '%d' ) );
		delete_user_meta( $user_id, 'jmgpm_team_id' );
		delete_user_meta( $user_id, 'jmgpm_team_role' );

		// Remove from all team projects and project roles (parity with routes/teams.js)
		$project_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$t['projects']} WHERE team_id = %d", $team_id ) );
		foreach ( $project_ids as $pid ) {
			$pid = (int) $pid;
			$wpdb->delete( $t['project_members'], array( 'project_id' => $pid, 'user_id' => $user_id ), array( '%d', '%d' ) );
			$wpdb->delete( $t['project_user_roles'], array( 'project_id' => $pid, 'user_id' => $user_id ), array( '%d', '%d' ) );
		}

		// Notify removed user (similar to teams.js member_removed)
		self::insert_notification(
			$user_id,
			$requester,
			'member_removed',
			'You were removed from the team ' . (string) $team['name'],
			(string) $team_id,
			'Team',
			array( 'projectName' => (string) $team['name'] )
		);

		return rest_ensure_response( array( 'message' => 'Member removed successfully' ) );
	}

	/* ---------------------------- Notifications ---------------------------- */

	public static function notifications_list( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$user_id = self::current_user_id();

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$t['notifications']} WHERE user_id = %d ORDER BY created_at DESC", $user_id ),
			ARRAY_A
		);
		return rest_ensure_response( $rows );
	}

	public static function notifications_mark_read( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$id = (int) $req['id'];
		$user_id = self::current_user_id();

		$wpdb->update(
			$t['notifications'],
			array( 'is_read' => 1 ),
			array( 'id' => $id, 'user_id' => $user_id ),
			array( '%d' ),
			array( '%d', '%d' )
		);
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['notifications']} WHERE id = %d", $id ), ARRAY_A );
		if ( ! $row ) {
			return new WP_Error( 'not_found', 'Notification not found.', array( 'status' => 404 ) );
		}
		return rest_ensure_response( $row );
	}

	public static function notifications_mark_all_read( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$user_id = self::current_user_id();
		$wpdb->query(
			$wpdb->prepare( "UPDATE {$t['notifications']} SET is_read = 1 WHERE user_id = %d AND is_read = 0", $user_id )
		);
		return rest_ensure_response( array( 'message' => 'All notifications marked as read' ) );
	}

	public static function notification_settings_get( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req['project_id'];

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT project_id, roles_json, created_at, updated_at FROM {$t['notification_settings']} WHERE project_id = %d", $project_id ),
			ARRAY_A
		);
		if ( $row ) {
			$row['roles'] = json_decode( (string) $row['roles_json'], true );
			unset( $row['roles_json'] );
			return rest_ensure_response( $row );
		}

		$default_json = (string) get_option( 'jmgpm_default_notification_roles_json', '{}' );
		$wpdb->insert(
			$t['notification_settings'],
			array(
				'project_id' => $project_id,
				'roles_json' => $default_json,
			),
			array( '%d', '%s' )
		);

		return rest_ensure_response(
			array(
				'projectId' => $project_id,
				'roles'     => json_decode( $default_json, true ),
			)
		);
	}

	public static function notification_settings_update( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$project_id = (int) $req['project_id'];
		$roles = $req->get_param( 'roles' );
		if ( null === $roles ) {
			// Accept raw body structure like the Mongo model update
			$roles = $req->get_json_params();
			if ( isset( $roles['roles'] ) ) {
				$roles = $roles['roles'];
			}
		}
		if ( ! is_array( $roles ) ) {
			return new WP_Error( 'bad_request', 'roles is required.', array( 'status' => 400 ) );
		}

		$roles_json = wp_json_encode( $roles );
		$wpdb->replace(
			$t['notification_settings'],
			array(
				'project_id' => $project_id,
				'roles_json' => $roles_json,
			),
			array( '%d', '%s' )
		);

		return rest_ensure_response( array( 'projectId' => $project_id, 'roles' => $roles ) );
	}

	/* ---------------------------- Invites ---------------------------- */

	private static function random_token(): string {
		// 48 hex chars (like crypto.randomBytes(24).toString('hex'))
		return bin2hex( random_bytes( 24 ) );
	}

	public static function invites_create( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$email = trim( (string) $req->get_param( 'email' ) );
		if ( '' === $email ) {
			return new WP_Error( 'bad_request', 'Email is required.', array( 'status' => 400 ) );
		}

		$user_id = self::current_user_id();
		$team_id = self::get_user_team_id( $user_id );
		if ( ! $team_id ) {
			return new WP_Error( 'bad_request', 'You must be part of a team to send invites.', array( 'status' => 400 ) );
		}

		$token = self::random_token();
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + 7 * DAY_IN_SECONDS );

		$wpdb->insert(
			$t['invites'],
			array(
				'email'           => $email,
				'team_id'         => $team_id,
				'inviter_user_id' => $user_id,
				'token'           => $token,
				'status'          => 'pending',
				'expires_at'      => $expires_at,
			),
			array( '%s', '%d', '%d', '%s', '%s', '%s' )
		);

		$invite_link = add_query_arg(
			array( 'invite' => $token ),
			home_url( '/' )
		);

		// Email sending: use wp_mail (WordPress-native)
		$subject = 'You are invited to join a team!';
		$body    = 'You have been invited to join a team. Visit: ' . esc_url_raw( $invite_link );
		wp_mail( $email, $subject, $body );

		return rest_ensure_response( array( 'message' => 'Invite sent!', 'inviteLink' => $invite_link ) );
	}

	public static function invites_generate_link( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$user_id = self::current_user_id();
		$team_id = self::get_user_team_id( $user_id );
		if ( ! $team_id ) {
			return new WP_Error( 'bad_request', 'You must be part of a team to generate invite links.', array( 'status' => 400 ) );
		}

		$token = self::random_token();
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + 7 * DAY_IN_SECONDS );

		$wpdb->insert(
			$t['invites'],
			array(
				'email'           => null,
				'team_id'         => $team_id,
				'inviter_user_id' => $user_id,
				'token'           => $token,
				'status'          => 'pending',
				'expires_at'      => $expires_at,
			),
			array( '%s', '%d', '%d', '%s', '%s', '%s' )
		);

		$invite_link = add_query_arg( array( 'invite' => $token ), home_url( '/' ) );
		return rest_ensure_response( array( 'inviteLink' => $invite_link ) );
	}

	public static function invites_join( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$user_id     = self::current_user_id();
		$inviteToken = (string) $req->get_param( 'inviteToken' );
		if ( '' === $inviteToken ) {
			return new WP_Error( 'bad_request', 'Invite token required.', array( 'status' => 400 ) );
		}

		$invite = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$t['invites']} WHERE token = %s AND status = 'pending' AND (expires_at IS NULL OR expires_at > UTC_TIMESTAMP())",
				$inviteToken
			),
			ARRAY_A
		);
		if ( ! $invite ) {
			return new WP_Error( 'bad_request', 'Invalid or expired invite token.', array( 'status' => 400 ) );
		}

		$team_id = (int) $invite['team_id'];
		$existing = $wpdb->get_var(
			$wpdb->prepare( "SELECT 1 FROM {$t['team_members']} WHERE team_id = %d AND user_id = %d", $team_id, $user_id )
		);
		if ( $existing ) {
			return new WP_Error( 'bad_request', 'You are already a member of this team.', array( 'status' => 400 ) );
		}

		$wpdb->replace( $t['team_members'], array( 'team_id' => $team_id, 'user_id' => $user_id ), array( '%d', '%d' ) );
		update_user_meta( $user_id, 'jmgpm_team_id', $team_id );
		update_user_meta( $user_id, 'jmgpm_team_role', 'member' );

		// Add user to all projects of the team and assign default viewer role (parity with inviteController.js)
		$project_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$t['projects']} WHERE team_id = %d", $team_id ) );
		foreach ( $project_ids as $pid ) {
			$pid = (int) $pid;
			$wpdb->replace( $t['project_members'], array( 'project_id' => $pid, 'user_id' => $user_id ), array( '%d', '%d' ) );
			$wpdb->replace(
				$t['project_user_roles'],
				array( 'project_id' => $pid, 'user_id' => $user_id, 'role' => 'viewer', 'notify_all' => 1 ),
				array( '%d', '%d', '%s', '%d' )
			);
		}

		$wpdb->update( $t['invites'], array( 'status' => 'accepted' ), array( 'id' => (int) $invite['id'] ), array( '%s' ), array( '%d' ) );

		return rest_ensure_response( array( 'message' => 'Joined team successfully.' ) );
	}

	/* ---------------------------- Messages ---------------------------- */

	public static function messages_send( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$receiver_id = (int) $req->get_param( 'receiverId' );
		$content     = (string) $req->get_param( 'content' );
		if ( $receiver_id <= 0 || '' === trim( $content ) ) {
			return new WP_Error( 'bad_request', 'receiverId and content are required.', array( 'status' => 400 ) );
		}

		$sender_id = self::current_user_id();
		$ok = $wpdb->insert(
			$t['messages'],
			array(
				'sender_user_id'   => $sender_id,
				'receiver_user_id' => $receiver_id,
				'content'          => $content,
				'is_read'          => 0,
			),
			array( '%d', '%d', '%s', '%d' )
		);
		if ( false === $ok ) {
			return new WP_Error( 'db_error', 'Failed to send message.', array( 'status' => 500 ) );
		}
		$id  = (int) $wpdb->insert_id;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['messages']} WHERE id = %d", $id ), ARRAY_A );
		return rest_ensure_response( $row );
	}

	public static function messages_conversation( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();

		$current = self::current_user_id();
		$other   = (int) $req['user_id'];
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$t['messages']}
				 WHERE (sender_user_id = %d AND receiver_user_id = %d)
				    OR (sender_user_id = %d AND receiver_user_id = %d)
				 ORDER BY created_at ASC",
				$current,
				$other,
				$other,
				$current
			),
			ARRAY_A
		);
		return rest_ensure_response( $rows );
	}

	public static function messages_mark_read( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$sender_id = (int) $req['sender_id'];
		$receiver_id = self::current_user_id();
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$t['messages']} SET is_read = 1 WHERE sender_user_id = %d AND receiver_user_id = %d AND is_read = 0",
				$sender_id,
				$receiver_id
			)
		);
		return rest_ensure_response( array( 'message' => 'Messages marked as read' ) );
	}

	public static function messages_unread_count( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$receiver_id = self::current_user_id();
		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$t['messages']} WHERE receiver_user_id = %d AND is_read = 0",
				$receiver_id
			)
		);
		return rest_ensure_response( array( 'count' => $count ) );
	}

	public static function messages_recent_conversations( WP_REST_Request $req ) {
		global $wpdb;
		$t = self::tables();
		$current = self::current_user_id();

		$since = gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS );
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$t['messages']}
				 WHERE created_at >= %s AND (sender_user_id = %d OR receiver_user_id = %d)
				 ORDER BY created_at DESC",
				$since,
				$current,
				$current
			),
			ARRAY_A
		);

		$convos = array();
		foreach ( $rows as $msg ) {
			$other = ( (int) $msg['sender_user_id'] === $current ) ? (int) $msg['receiver_user_id'] : (int) $msg['sender_user_id'];
			if ( ! isset( $convos[ $other ] ) ) {
				$convos[ $other ] = array(
					'user'        => array(
						'id'    => $other,
						'name'  => ( get_user_by( 'id', $other ) ? get_user_by( 'id', $other )->display_name : '' ),
						'email' => ( get_user_by( 'id', $other ) ? get_user_by( 'id', $other )->user_email : '' ),
					),
					'lastMessage' => $msg,
				);
			}
		}
		return rest_ensure_response( array_values( $convos ) );
	}
}

