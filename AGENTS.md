# AGENTS.md — Training Registration WordPress Plugin

Guidance for AI coding agents working in this repository.

---

## 1. Project Overview & Architecture

**What it is:** A WordPress plugin ("Event Registration", v4.0.0-Beta.2) for managing training events. Event organizers create training sessions with capacity limits and registration windows; Learning Centers manage staff profiles and enroll staff members. Features include custom page templating, Excel data exports via `phpoffice/phpspreadsheet`, and swappable registration modes (Default CN vs. SOTAM MY) via the Strategy pattern.

**Tech Stack:**
- **PHP:** `>= 8.1` defined in `composer.json` and `Training-registration.php` (`Requires PHP: 8.1`). Tested on PHP 8.3 in CI and dev containers. Uses PHP 8 features (`match` expressions).
- **WordPress:** `>= 6.0` (`Requires at least: 6.0`). Tested against WordPress "latest". Target theme is Twenty Twenty-Five.
- **Database:** MySQL 8.0. Plugin maintains 3 custom tables managed via `dbDelta()`:
  - `ER_STAFF_PROFILE` (`{$wpdb->prefix}er_staff_profile`) — staff directory.
  - `ER_EVENT_LIST` (`{$wpdb->prefix}er_event_list`) — training sessions.
  - `ER_REGISTRATION_LIST` (`{$wpdb->prefix}er_event_reg`) — event registrations.
- **Dependencies:**
  - Production: `phpoffice/phpspreadsheet` (`^5.8.1`).
  - Development: `phpunit/phpunit` (`^9.6`), `10up/wp_mock` (`^1.0`), `yoast/phpunit-polyfills` (`^2.0`).
- **Autoloading:** PSR-4 mapped via Composer: `SOT\TrainingRegistration\` -> `src/`.
- **Assets & UI:** Plain CSS in `assets/css/admin/` and `assets/css/ui/`, WordPress Dashicons. No JavaScript build system, bundler, or asset pipeline.

**Entry Points & Lifecycles:**
- **Plugin Bootstrap (`Training-registration.php`):**
  - Defines `ER_PLUGIN_DIR` and table name constants (`ER_STAFF_PROFILE`, `ER_EVENT_LIST`, `ER_REGISTRATION_LIST`).
  - Loads Composer autoloader (`vendor/autoload.php`).
  - Hooks `register_activation_hook` -> `Core\Activator::activate_plugin()`.
  - Boots plugin on `plugins_loaded` -> `(new Core\Plugin())->run()`.
- **Plugin Activation (`src/Core/Activator.php` & `src/Core/PageCreator.php`):**
  - Runs `dbDelta()` to create or update the 3 custom database tables.
  - Runs `PageCreator::run()` to upsert required frontend pages (`Create Staff Profile`, `Manage My Staff`, `Register for Training`, `Training Registration`), sets home page, enforces `/%postname%/` permalinks, and flushes rewrite rules.
- **Hook & Shortcode Routing (`src/Core/Plugin.php` & `src/Core/Loader.php`):**
  - `Loader` aggregates actions and shortcodes into internal registries and binds them during `Plugin::run()`.
  - Frontend shortcodes:
    - `[training_dashboard]` -> `TrainingRegistrationUI::uiDashboard()`
    - `[register_training]` -> `TrainingRegistrationUI::eventRegistration()`
    - `[view_staff]` -> `TrainingRegistrationUI::viewEditStaff()`
    - `[staff_form]` -> `TrainingRegistrationUI::staffFormCreation()`
- **Template System (`src/Core/TemplateManager.php` & `src/Traits/TemplateRenderer.php`):**
  - `TemplateManager` intercepts page rendering via `theme_page_templates` and `template_include` to serve `templates/app-layout.php`.
  - Controllers use trait `TemplateRenderer::render($template, $data, $echo)` to extract data scopes into view files in `templates/`.
- **Admin System (`src/Admin/AdminSettings.php`):**
  - Registers top-level admin menu `er_gen_set` ("Training Registration", capability `edit_plugins`) and submenus `er_new_event_set` (Create), `er_view_reg_set` (Registrations), and `er_settings` (Settings).
  - Handles Excel export requests on `admin_init` via `AdminSettings::exportRegistrationsToExcel()`.

**Directory Layout:**
- `src/Core/` — Lifecycle orchestrator (`Plugin`), hook registry (`Loader`), activation & table installation (`Activator`), page provisioning (`PageCreator`), page template interception (`TemplateManager`), and shared utilities (`Tools`).
- `src/Admin/` — Admin pages, menus, notices, and `WP_List_Table` implementations (`AdminSettings`, `AdminMessages`, `HomeTable`, `SettingsPageContent`, `StaffRegistrationTableCN`, `StaffRegistrationTableMY`).
- `src/UI/` — Frontend shortcode handlers and UI controllers (`TrainingRegistrationUI`, `UIContent`).
- `src/Data/Repositories/` — Data access layer encapsulating all `$wpdb` SQL queries (`EventRepository`, `StaffRepository`, `RegistrationRepository`).
- `src/Data/Strategies/` — Pluggable registration mode strategy (`RegistrationModeInterface`, `RegistrationModeFactory`, `DefaultRegistrationMode`, `SotamRegistrationMode`).
- `src/Traits/` — Shared traits (`TemplateRenderer`).
- `templates/` — PHP template views (`templates/admin/`, `templates/ui/`, `templates/app-layout.php`).
- `assets/` — Static stylesheets (`assets/css/admin/`, `assets/css/ui/`).
- `files/` — Excel templates (`Default_Excel_Template.xlsx`, `SOTAM_Excel_Template.xlsx`).
- `scripts/` — Automation and setup shell/PHP scripts (`install-wp-tests.sh`, `package.sh`, `package.bat`, `seed-data-enhanced.php`).
- `tests/` — Test suites (`tests/Unit/`, `tests/Integration/`, `tests/bootstrap.php`, `tests/bootstrap-integration.php`).
- `.devcontainer/` — Docker Compose, Dockerfile, and devcontainer settings for local WordPress + MySQL development.
- `.github/workflows/` — GitHub Actions CI pipeline (`phpunit.yml`).

**Architecture Patterns:**
- **Repository Pattern:** Database interactions are strictly encapsulated in `src/Data/Repositories/`. Controllers and tools never write raw SQL.
- **Strategy Pattern:** `RegistrationModeInterface` abstracts region-specific registration workflows. The mode is selected at runtime by `RegistrationModeFactory::get_current_mode()` based on WordPress option `my_mode` (0 = Default CN, 1 = SOTAM MY). Governs field sets, Excel templates, and form rendering.
- **Registry Pattern:** `Loader` collects hooks (`er_add_action`) and shortcodes (`er_add_shortcode`), registering them in bulk during initialization.
- **Direct Dependency Instantiation:** Dependencies are instantiated directly in class constructors (no DI container).

---

## 2. Development & Tooling Commands

### Dependencies & Autoloading
```bash
# Install all dependencies (production + testing dev dependencies)
composer install

# Install production-only dependencies
composer install --no-dev

# Rebuild PSR-4 autoload map after adding or moving classes in src/
composer dump-autoload
```

### Running Tests
```bash
# Run unit tests (WP_Mock; isolated in-memory, no database or WordPress needed)
./vendor/bin/phpunit --testsuite Unit
# Or using configuration file directly:
php vendor/bin/phpunit --configuration phpunit.xml

# Run integration tests (requires MySQL and initialized WordPress test library)
./vendor/bin/phpunit -c phpunit-integration.xml
```

### Integration Test Environment Setup
Integration tests require a separate MySQL database and the WordPress test library.
```bash
# Usage: ./scripts/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]

# Dev Container setup:
./scripts/install-wp-tests.sh wordpress_test wordpress changeme_db_password db latest

# CI / existing MySQL service setup (pass 'true' as 6th parameter to skip DB creation):
bash scripts/install-wp-tests.sh wordpress_test root root 127.0.0.1 latest true
```

### Packaging & Release
```bash
# Create release zip (generates Training-Registration.zip with production dependencies)
./scripts/package.sh        # On macOS / Linux
scripts\package.bat         # On Windows
```

### Local Dev Environment (VS Code Dev Container)
- Open project in VS Code with Dev Containers extension and select **Reopen in Container**.
- Automatically configures WordPress on port `8080` (`http://localhost:8080`), MySQL on port `3306`, and Xdebug.
- VS Code tasks configured in `.vscode/tasks.json`:
  - `phpunit:unit` — runs unit suite.
  - `phpunit:integration` — runs integration suite.
  - `phpunit:all` — runs unit and integration suites sequentially.

### Dev Data Seeding
```bash
# Seed development database with realistic test data (CAUTION: wipes existing table data)
php scripts/seed-data-enhanced.php --schools=10 --events=5 --users=50

# Seed options:
#   --schools=[N]   Number of schools to generate (default: 10)
#   --events=[N]    Number of events to generate (default: 8)
#   --users=[N]     Number of staff profiles to generate (default: 50)
#   --no-clear      Do not truncate tables before seeding
#   --force         Bypass confirmation prompt
```

### Linting & Formatting
- **No linter, static analysis tool, or automatic code formatter** (such as PHPCS, PHPStan, Psalm, or PHP-CS-Fixer) is configured in this repository.
- Agents must manually adhere to the existing code style and formatting patterns described below.

---

## 3. Code Style & Conventions

Follow existing conventions in untouched code. Keep diffs focused and minimal.

### Language & Typing
- **PHP Compatibility:** PHP `>= 8.1` (do not use features introduced in PHP 8.2+ such as readonly classes or standalone `null`/`true`/`false` types).
- **No Strict Typing:** Do NOT add `declare(strict_types=1);` to existing or new files.
- **Type Declarations:** Most methods omit parameter and return type declarations. Match the existing file style; use docblocks rather than adding strict type hints to legacy signatures.
- **Conditionals:** Prefer PHP 8 `match` expressions over verbose `switch` statements for mapping values and statuses (as seen in `Tools` and `RegistrationModeFactory`).

### File Structure & Guard Clauses
- **ABSPATH Guard:** Every class file in `src/` and view file in `templates/` must start with:
  ```php
  defined('ABSPATH') || exit;
  ```
  In classes, place this immediately after the `namespace` declaration. In template files, place it at line 3.
- **File & Class Mapping:** Strict PSR-4 mapping: one class/interface/trait per file under `src/`, with file names exactly matching class names.

### Naming Conventions
- **Classes / Interfaces / Traits:** `PascalCase` (e.g., `RegistrationModeInterface`, `EventRepository`, `TemplateRenderer`).
- **Methods:**
  - Standard & Repository methods: `snake_case` (e.g., `get_by_id`, `get_all_upcoming`, `handle_staff_creation`, `activate_plugin`).
  - Legacy UI/Admin callbacks: some exist in `camelCase` (e.g., `adminSettingsPageRegistration`, `isValidEvent`, `spotsOpen`). Use `snake_case` for any new repository, strategy, or core methods.
- **Properties & Variables:** `snake_case` (e.g., `$event_repo`, `$removal_id`, `$school_username`).
- **Constants:** `UPPER_SNAKE_CASE` (e.g., `ER_PLUGIN_DIR`, `ER_STAFF_PROFILE`, `ER_EVENT_LIST`, `ER_REGISTRATION_LIST`).
- **Slugs & Nonces:** Prefixed with `er_` (e.g., slugs `er_gen_set`, `er_new_event_set`, `er_view_reg_set`; nonces `remove_training_nonce`, `export_excel_nonce`).

### Database & SQL Conventions
- **Table References:** Always reference tables using plugin constants (`ER_STAFF_PROFILE`, `ER_EVENT_LIST`, `ER_REGISTRATION_LIST`) or `$this->table` within repository classes.
- **Prepared Queries:** Always use `$wpdb->prepare()` with appropriate placeholders (`%d`, `%s`, `%f`) for any SQL query containing dynamic data. Never concatenate raw input into SQL strings.
- **Table Prefix:** Tables are dynamically prefixed with `$wpdb->prefix . 'er_'`.

### Security & Sanitization
- **Capabilities:** Admin screens and mutation handlers must enforce capability checks (`current_user_can('edit_plugins')`). Frontend views must check `is_user_logged_in()`.
- **Nonces:** Verify nonces on every mutation request via `wp_verify_nonce()` or `check_admin_referer()`.
- **Input Sanitization:** Sanitize all incoming user data via `sanitize_text_field()`, `sanitize_textarea_field()`, or `intval()`.
- **Output Escaping:** Escape all template output:
  - Text: `esc_html()`
  - Attributes: `esc_attr()`
  - URLs: `esc_url()`

### Templates & Views
- Templates reside in `templates/ui/`, `templates/admin/`, and `templates/app-layout.php`.
- Views are loaded via `TemplateRenderer::render($template, $data, $echo)`.
- Templates receive variables via `extract($data)`. Document available variables with `@var` docblocks at the head of each template.
- Keep business logic in controllers/repositories; template files should contain only display markup and minimal presentation formatting.

---

## 4. Testing & Verification

### Test Frameworks & Suites
1. **Unit Test Suite (`tests/Unit/`):**
   - Framework: PHPUnit 9.6 with `10up/wp_mock`.
   - Bootstrap: `tests/bootstrap.php`.
   - Configuration: `phpunit.xml`.
   - Setup: WP_Mock stubs core WordPress functions and constants. Unit tests do NOT connect to MySQL or load WordPress core.
   - Conventions: Test classes extend `PHPUnit\Framework\TestCase`. Must call `WP_Mock::setUp()` in `setUp()` and `WP_Mock::tearDown()` in `tearDown()`.
   - Function stubs: If code under test calls WordPress functions that WP_Mock does not handle, define them as stubs in `tests/bootstrap.php` guarded with `if (!function_exists(...))` instead of duplicating mocks across individual tests.
2. **Integration Test Suite (`tests/Integration/`):**
   - Framework: WordPress Core Test Library (`WP_Integration_TestCase`) + `yoast/phpunit-polyfills`.
   - Bootstrap: `tests/bootstrap-integration.php`.
   - Configuration: `phpunit-integration.xml`.
   - Setup: Requires a running MySQL instance and installed WordPress test environment (`WP_TESTS_DIR`, default `/tmp/wordpress-tests-lib`).
   - Tables: Tests automatically run `Activator::activate_plugin()` to verify real database schemas and operations.

### Verification Standards
- **Green Suite Requirement:** Both Unit and Integration suites must pass before submitting changes.
- **Autoloading:** Always run `composer dump-autoload` if adding or relocating classes under `src/`.
- **Coverage Expectations:** No minimum coverage percentage is enforced by tooling, but all new repository methods, strategies, and calculation utilities must have corresponding unit or integration test coverage.
- **CI Enforcement:** Continuous integration runs on GitHub Actions (`.github/workflows/phpunit.yml`) on PHP 8.3 with MySQL 8.0 for all pushes and pull requests to `master`. There are no client-side git pre-commit hooks.

---

## 5. Agent Rules & Constraints

- **Do Not Commit Secrets:**
  - Credentials in `.devcontainer/docker-compose.yml` (`changeme_db_*`) are local dev placeholders.
  - Never commit real database passwords, API tokens, or secrets. Use environment variables.
- **Do Not Manually Edit Lockfiles:**
  - Never edit `composer.lock` by hand. Modify `composer.json` and run `composer update <package>` or `composer install`.
  - Never commit the `vendor/` directory (it is gitignored).
- **Never Modify Test Caches or Vendor Files:**
  - Do not edit `.phpunit.result.cache` or files inside `vendor/` to force tests to pass; fix either the source code or test definitions.
- **Schema Changes Are Breaking Changes:**
  - Custom tables (`er_staff_profile`, `er_event_list`, `er_event_reg`) are defined in `src/Core/Activator.php` via `dbDelta()`.
  - Always confirm database schema modifications with the user before applying them.
  - If a column or table changes, update `src/Core/Activator.php`, all affected repositories in `src/Data/Repositories/`, both test bootstraps (`tests/bootstrap.php`, `tests/bootstrap-integration.php`), integration tests, and `scripts/seed-data-enhanced.php` together.
- **Preserve Existing Formatting:**
  - No linter or formatter is configured. Follow the indentation, whitespace, and bracket style of the specific file you are editing. Avoid sweeping refactors or reformatting untouched code.
- **Hook & Shortcode Registration Boundary:**
  - Do not call `add_action()` or `add_shortcode()` directly from controllers or repositories.
  - Register hooks through `Core\Loader` in `Core\Plugin::define_hooks()`.
  - Exceptions: Admin page-load hooks inside `AdminSettings::adminSettingsPageRegistration()` and template filters inside `TemplateManager::init()`.
- **Packaging Integrity:**
  - Do not manually edit or create `Training-Registration.zip` or `build_tmp/`. Use `scripts/package.sh` (or `package.bat`) to generate clean distribution archives.
- **Plugin Version Management:**
  - The canonical plugin version is defined in the header comment of `Training-registration.php` (`Version: 4.0.0-Beta.2`).
  - Do not modify or bump the version number unless explicitly instructed for a release.
- **Safe Seeding Practices:**
  - `scripts/seed-data-enhanced.php` deletes all existing data in plugin tables. Never run this script against staging or production databases.
