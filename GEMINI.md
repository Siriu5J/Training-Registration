# Project Overview: Training Registration Plugin

The Training Registration Plugin is a WordPress plugin designed for SOT training organizers and Learning Centers. It facilitates the creation, management, and registration of training events, specifically tailored for the "Twenty Twenty" WordPress theme.

## Key Technologies
- **PHP**: Core language for plugin logic.
- **WordPress**: Host platform; uses standard WP APIs (Nonces, capability checks, `WP_List_Table`).
- **PhpSpreadsheet**: Used for generating Excel exports of registration data.
- **Composer**: Manages PHP dependencies (found in `composer.json`).
- **7-Zip**: Used by `package.bat` for creating release zip files.

## Building and Running

### Prerequisites
- PHP 7.4+ (inferred from `phpspreadsheet` usage).
- A WordPress installation with the **Twenty Twenty** theme active.
- [Composer](https://getcomposer.org/) installed.
- (Optional) [7-Zip](https://www.7-zip.org/) for packaging.

### Commands
- **Install Dependencies**:
  ```bash
  composer install
  ```
- **Package for Release**:
  ```bash
  .\package.bat
  ```
  *Note: Ensure `7z.exe` is in your PATH or update the script path.*

### Installation & Setup
1. Upload the plugin zip to WordPress.
2. Activate the plugin.
3. **Important**: Set Permalinks to "Post Name" in `Settings > Permalinks`.
4. The plugin creates necessary database tables (`er_staff_profile`, `er_event_list`, `er_event_reg`) and initial pages upon activation.
5. Follow the customization steps in `README.md` for the Twenty Twenty theme.

## Development Conventions

### Architecture
- **Entry Point**: `Training-registration.php` defines constants and initializes the plugin via `training_registration_main`.
- **Loader Pattern**: `includes/training_registration_loader.php` handles all `add_action` and `add_shortcode` calls to centralize hook management.
- **Object-Oriented**: Core logic is encapsulated in classes (e.g., `training_registration_acp`, `training_registration_ui`).
- **Admin Interface**: Uses `WP_List_Table` (in `admin/admin_home_table.php`) for managing training lists.
- **Separation of Concerns**:
    - `admin/`: Admin-side logic and styling.
    - `ui/`: Public-facing registration forms and styles.
    - `includes/`: Core functional logic and tools.
    - `files/`: Static Excel templates.

### Coding Standards
- **Naming**: Database tables and constants are prefixed with `er_`.
- **Security**: Always use `wp_verify_nonce` for POST requests and `current_user_can('edit_plugins')` for capability checks.
- **Sanitization**: Use standard WP functions like `sanitize_text_field` and `intval`.

## Key Files
- `Training-registration.php`: Main plugin header and initialization.
- `includes/activation.php`: Database schema and activation logic.
- `includes/training_registration_main.php`: Orchestrates dependencies and hooks.
- `admin/admin_settings.php`: Main admin page controller and Excel export logic.
- `ui/ui.php`: Shortcode handlers for the front-end registration experience.
- `composer.json`: Dependency definitions.
- `README.md`: User-level installation and theme customization guide.
