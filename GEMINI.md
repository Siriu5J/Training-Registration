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
  - Windows: `.\scripts\package.bat`
  - Linux/Mac: `./scripts/package.sh`
  *Note: Ensure `7z.exe` (Windows) or `zip` (Linux) is installed.*

### Installation & Setup
1. Upload the plugin zip to WordPress.
2. Activate the plugin.
3. **Important**: Set Permalinks to "Post Name" in `Settings > Permalinks`.
4. The plugin creates necessary database tables (`er_staff_profile`, `er_event_list`, `er_event_reg`) and initial pages upon activation.
5. Follow the customization steps in `README.md` for the Twenty Twenty theme.

## Development Conventions

### Architecture
- **Namespace**: `SOT\TrainingRegistration`
- **Autoloading**: PSR-4 via Composer (mapped to `src/`).
- **Entry Point**: `Training-registration.php` initializes the plugin via `SOT\TrainingRegistration\Core\Plugin`.
- **Repository Pattern**: Centralized data access in `src/Data/Repositories/`.
- **Strategy Pattern**: Pluggable registration modes in `src/Data/Strategies/`.
- **Controllers**:
    - `src/Admin/`: Admin-side logic (Settings, Tables, Messages).
    - `src/UI/`: Public-facing registration shortcode handlers.
- **Traits**: Shared logic (e.g., `TemplateRenderer`) in `src/Traits/`.
- **Templates**: Pure PHP templates in `templates/`.
- **Assets**: CSS/JS consolidated in `assets/`.

### Coding Standards
- **Naming**: 
    - Namespaced classes in `PascalCase`.
    - Database tables and constants are prefixed with `er_`.
- **Security**: Always use `wp_verify_nonce` for POST requests and `current_user_can('edit_plugins')` for capability checks.
- **Sanitization**: Use standard WP functions like `sanitize_text_field` and `intval`.

## Key Files
- `Training-registration.php`: Main plugin header and initialization.
- `src/Core/Activator.php`: Database schema and activation logic.
- `src/Core/Plugin.php`: Orchestrates dependencies and hooks.
- `src/Admin/AdminSettings.php`: Main admin page controller.
- `src/UI/TrainingRegistrationUI.php`: Shortcode handlers for front-end.
- `composer.json`: Dependency and PSR-4 definitions.
- `README.md`: User-level installation and theme customization guide.
