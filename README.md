# Training Registration Plugin v3
The Training Registration Plugin is a WordPress plugin created to allow SOT training organizers to create training registration forms where Learning Centers could register to. This plugin does require preliminary setup on the WordPress. For example, user login control and theme customization is necessary.
## Features
**For Training Organizers:**
* Create training events
* Post training information to Learning Centers
* Manage the status of trainings including registration open and close time as well as its activation status
* View registered trainees online
* Manage registered trainees
* Download training registration data as Excel Spreadsheet

**For Learning Centers:**
* Create and manage staff profile
* Register to open trainings
* Manage training registrations from its own Learning Center
## Installation
**Install Plugin**
1. Go to `Settings` > `Permalinks` and set it to "Post Name"
2. Download the plugin .zip from the [release page](https://github.com/Siriu5J/Training-Registration/releases).
3. Install the plugin to WordPress by using local upload.

**Customize Site (Twenty Twenty-Five)**
1. Activate the **Twenty Twenty-Five** theme.
2. Go to `Appearance` > `Editor` (this opens the WordPress Site Editor).
3. Click on the **Styles** icon (top right circle icon) and select **Colors**.
4. Define the following **Palette** for a professional and integrated look:
    *   **Base (Background):** `#f8f8f8`
    *   **Contrast (Text):** `#1d2327`
    *   **Primary (Accent):** `#012552` (Deep Blue)
    *   **Secondary (Highlight):** `#fecc00` (Gold/Yellow)
5. Under **Elements**, set the following:
    *   **Buttons:** Set the background to **Secondary** (`#fecc00`) and the text to **Primary** (`#012552`).
    *   **Links:** Set the color to **Primary** (`#012552`) with a hover effect using **Secondary** (`#fecc00`).
6. The plugin's UI now uses standard WordPress block classes (like `.wp-block-table` and `.button`), meaning it will automatically inherit these global styles without requiring large blocks of custom CSS.

**Important**: Ensure Permalinks are set to **"Post Name"** in `Settings` > `Permalinks` for the registration pages to function correctly.


## Folder Structure
The plugin follows a modern PHP structure with PSR-4 autoloading and a clear separation of concerns:

### `src/` (Core Logic)
Contains all namespaced PHP classes under `SOT\TrainingRegistration\`:
*   `Admin/`: Admin-side controllers, message handlers, and `WP_List_Table` implementations.
*   `UI/`: Frontend shortcode handlers and UI logic.
*   `Data/Repositories/`: Centralized database access (CRUD) for Events, Staff, and Registrations.
*   `Data/Strategies/`: Pluggable logic for different registration modes (e.g., Default vs SOTAM).
*   `Core/`: Fundamental plugin components like the `Loader`, `Activator`, `Tools`, and `PageCreator`.
*   `Traits/`: Reusable logic like the `TemplateRenderer`.

### `templates/` (Views)
Pure PHP files containing the HTML and presentation logic. These are rendered by controllers using the `TemplateRenderer` trait.
*   `admin/`: Layouts for the WordPress dashboard.
*   `ui/`: Layouts for the public-facing registration forms.

### `assets/` (Static Assets)
*   `css/`: Minified and organized stylesheets for both Admin and UI.

### `files/`
Static assets used by the plugin, specifically the Excel `.xlsx` templates for data exports.

### `tests/`
*   `Unit/`: Fast tests using `WP_Mock` to verify logic in isolation.
*   `Integration/`: Comprehensive tests running against a real WordPress database.

## Testing
The plugin includes a comprehensive test suite covering both unit and integration tests to ensure stability and performance.

### Prerequisites
* **Composer**: Ensure all development dependencies are installed:
  ```bash
  composer install
  ```
* **Docker**: Integration tests are designed to run within the provided Docker environment.

### Unit Tests (Fast, Mocked)
Unit tests use PHPUnit and `WP_Mock` to test logic in isolation. These are fast and do not require a live WordPress site or database.
```bash
./vendor/bin/phpunit
```

### Integration Tests (Real Database)
Integration tests run against a real WordPress instance and a dedicated test database (`wordpress_test`). They verify actual database persistence, repository queries, and complete functional flows.

#### One-Time Environment Setup
If you are running in a new environment, you may need to initialize the WordPress test library:
```bash
# Inside the container
./install-wp-tests.sh wordpress_test root changeme_db_root_password db latest
```

#### Running Integration Tests
```bash
./vendor/bin/phpunit -c phpunit-integration.xml
```

### IDE Support (Intelephense)
If you are using VS Code with Intelephense, you may see errors about `WP_UnitTestCase` being undefined. This is because the WordPress test library is located outside your workspace (in `/tmp`).

To fix this:
1.  Ensure you have the `.vscode/settings.json` file created by this setup.
2.  It should contain:
    ```json
    "intelephense.environment.includePaths": [
        "/tmp/wordpress-tests-lib",
        "/tmp/wordpress"
    ]
    ```
3.  You may need to run the command **"Intelephense: Index workspace"** from the VS Code command palette.

### Large Dataset & Performance Testing
The suite includes a `LargeDatasetTest.php` that programmatically seeds a large volume of realistic data (5+ schools, 100+ staff, 20+ events, and 1000+ registrations). Use this to verify that the admin interface and registration logic remain performant under load.

## Seeding Test Data
For manual testing and development, you can use the included enhanced data seeder script to quickly populate your local WordPress site with realistic data (staff profiles, training events, and registrations).

```bash
php scripts/seed-data-enhanced.php [OPTIONS]
```

### Options
*   `--schools=[number]`  Number of schools to generate (default: 10)
*   `--events=[number]`   Number of events to generate (default: 8)
*   `--users=[number]`    Number of staff users to generate (default: 50)
*   `--no-clear`          Do not clear existing data before seeding
*   `--force`             Skip confirmation when clearing data (useful for automated setups)
*   `--help`              Show help message

### Examples
```bash
# Generate 5 schools, 3 events, and 25 users
php scripts/seed-data-enhanced.php --schools=5 --events=3 --users=25

# Seed data without clearing existing records
php scripts/seed-data-enhanced.php --no-clear
```

The script will:
*   Automatically ensure the necessary database tables exist.
*   Generate realistic staff profiles across the specified number of schools.
*   Create training events with valid scheduling (registration open/close and event start/end times).
*   Randomly register staff members to events while respecting event capacity.

## Additional Libraries Used/Thanks
* ~~[PHPExcel](https://github.com/PHPOffice/PHPExcel)~~
* [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
