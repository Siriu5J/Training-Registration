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

**Customize Site**
1. Make sure you are using the default Twenty Twenty Theme
2. Go to `Admin Control Panel` > `Appearance` > `Customization`.
3. Go to `Site Identity` and change the Site Title and Tagline to something related to training registration
4. Go to `Colors` and set Background Color to `#f8f8f8` and Header & Footer Background Color to `#012552`
5. Go to `Cover Template` and set Overlay Background Color to `#fecc00` and Overlay Text Color to `#ffffff`
6. Remove all menues and widgets
7. Add the following CSS code to `Additional CSS`:
```
button:not(.toggle),
.button,
.faux-button,
.wp-block-button__link,
.wp-block-file .wp-block-file__button,
input[type=”button”], input[type=”reset”], input[type=”submit”],
input[type="Submit"],
.bg-accent,
.bg-accent-hover:hover,
.bg-accent-hover:focus,
:root .has-accent-background-color, .comment-reply-link {
background-color: #fecc00;
color: #012552 !important;
}
a:not(.wp-block-button__link){
	color:#fecc00 !important;
	color:#012552;
}
.wp-block-button__link:hover{
	color: #012552;
}
.entry-title {
	font-size: 35pt;
}
.singular .entry-header{
	padding-top: 0;
	padding-bottom: 1.5rem;
}
}
html, body {
	height: 100%;
}
.home #site-footer {
	position: fixed;
	bottom: 0;
	width: 100%;
}
.header-navigation-wrapper {
	display: none;
}
.header-inner {
	padding-bottom: 0px;
}
```

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
For manual testing and development, you can use the included data seeder script to quickly populate your local WordPress site with realistic data (schools, events, staff, and registrations).

```bash
php scripts/seed-data.php
```

The script will:
*   Create 5 school users (`school_1` through `school_5`) with password `password`.
*   Generate 10 sample training events.
*   Create 3 staff profiles per school.
*   Randomly register staff to upcoming events.

## Additional Libraries Used/Thanks
* ~~[PHPExcel](https://github.com/PHPOffice/PHPExcel)~~
* [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
