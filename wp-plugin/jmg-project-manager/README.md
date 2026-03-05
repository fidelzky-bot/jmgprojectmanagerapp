# JMG Project Manager (WordPress Plugin)

This folder contains the WordPress plugin version of your Project Manager backend.

## Install

1. Copy the folder `wp-plugin/jmg-project-manager/` into your WordPress site at:
   - `wp-content/plugins/jmg-project-manager/`
2. In WordPress Admin, go to **Plugins** → **Installed Plugins** and activate **JMG Project Manager**.
3. On activation, the plugin creates custom tables (prefixed with `wp_jmgpm_...`).

## WP-Admin “Database View”

After activation, you’ll see a **JMG Projects** menu in WP Admin. It lists rows from:

- `jmgpm_teams`
- `jmgpm_projects`
- `jmgpm_tasks`
- `jmgpm_invites`
- `jmgpm_notifications`
- `jmgpm_messages`

## REST API (parity layer)

Routes are under:

- `/wp-json/jmg/v1/...`

Examples:

- `GET /wp-json/jmg/v1/projects`
- `POST /wp-json/jmg/v1/tasks`
- `GET /wp-json/jmg/v1/notifications`

## Auth notes

- Endpoints generally require a logged-in WordPress user.
- `POST /wp-json/jmg/v1/auth/login` returns a WordPress **Application Password** token (when available) so a headless client can call API endpoints using Basic Auth.

