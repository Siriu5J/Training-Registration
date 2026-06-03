# Training Registration Plugin

[![WordPress Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-8892bf.svg)](https://php.net/)
[![License: Apache-2.0](https://img.shields.io/badge/License-Apache--2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)
[![PHPUnit Tests](https://github.com/Siriu5J/Training-Registration/actions/workflows/phpunit.yml/badge.svg)](https://github.com/Siriu5J/Training-Registration/actions/workflows/phpunit.yml)

The Training Registration Plugin is a robust management system for WordPress designed for training organizers and Learning Centers. It provides a complete framework for orchestrating training events, managing staff profiles, and facilitating participant enrollment with professional data export capabilities.

## Key Features

### Administrative Capabilities
*   **Event Lifecycle Management**: Tools to create, modify, and schedule training events with precise registration windows and capacity limits.
*   **Registration Monitoring**: Real-time oversight of participant registrations and status management.
*   **Data Export**: Integrated functionality to generate comprehensive registration reports in Excel format using the PhpSpreadsheet library.
*   **Pluggable Strategies**: Support for multiple registration modes (e.g., Default and SOTAM) via the Strategy design pattern.

### Learning Center Capabilities
*   **Staff Management**: A centralized system for Learning Centers to maintain staff profiles and records. Includes a secure staff removal feature that automatically handles withdrawals from all open trainings while enforcing registration deadlines to prevent unauthorized cancellations.
*   **Event Enrollment**: Simplified registration process for available training sessions.
*   **Management Dashboard**: A dedicated interface for centers to view upcoming events and monitor their existing registrations.

## Installation

### Prerequisites
*   **WordPress**: Version 6.0 or higher.
*   **PHP**: Version 8.3 or higher.
*   **Theme**: Optimized for the Twenty Twenty-Five theme.
*   **Composer**: Required for dependency management and autoloading.

### Configuration and Setup
1.  **Permalinks**: Navigate to `Settings > Permalinks` in the WordPress dashboard and select **Post Name**. This configuration is required for proper routing of registration pages.
2.  **Plugin Installation**: Upload the plugin archive via `Plugins > Add New > Upload Plugin`.
3.  **Dependencies**: If installing from source, execute the following command in the plugin directory:
    ```bash
    composer install --no-dev
    ```
4.  **Activation**: Upon activation, the plugin programmatically initializes the required database schema and core pages.

## Shortcodes

The following shortcodes are utilized to render the primary interfaces:

*   `[training_dashboard]`: The primary entry point for Learning Center users.
*   `[register_training]`: Displays the list of available training events.
*   `[view_staff]`: Interface for managing staff profiles within a Learning Center.
*   `[staff_form]`: Form for adding or updating individual staff records.

## Development and Architecture

### Development Environment
The project is configured for a standardized development experience using **VS Code Dev Containers** and **Docker**.
*   **Automated Setup**: The provided `.devcontainer` configuration initializes a WordPress environment with a MySQL database.
*   **Tooling**: The environment includes PHP 8.1, Xdebug for debugging, and Composer for dependency management.
*   **Workflow**: To initialize the environment, open the project in VS Code and select **Reopen in Container**. This will automatically build the containers and install necessary dependencies.

### Dependency Management
Composer is used to manage the project's PHP dependencies and PSR-4 autoloading.
*   **Development**: Execute `composer install` to install all requirements, including testing frameworks (PHPUnit, WP_Mock).
*   **Production**: Use `composer install --no-dev` to install only the dependencies required for plugin operation.
*   **Autoloading**: The `src/` directory is mapped to the `SOT\TrainingRegistration` namespace. If class mappings are not recognized, execute `composer dump-autoload`.

### Directory Structure
*   `src/`: Core logic utilizing PSR-4 autoloading under the `SOT\TrainingRegistration` namespace.
    *   `Admin/`: Controllers for administrative interfaces and `WP_List_Table` implementations.
    *   `UI/`: Frontend shortcode handlers and interface logic.
    *   `Data/`: Repositories for database access and registration strategy implementations.
    *   `Core/`: Plugin lifecycle management, activation logic, and utility classes.
*   `templates/`: PHP-based view files for both admin and frontend interfaces.
*   `assets/`: CSS and other static assets.
*   `tests/`: Comprehensive test suite including unit and integration tests.
*   `files/`: Excel templates used for data exports.

### Testing Framework
The project maintains a rigorous testing suite using PHPUnit.

#### Running Tests
*   **Unit Tests**: Verify isolated logic using `WP_Mock`.
    ```bash
    ./vendor/bin/phpunit --testsuite Unit
    ```
*   **Integration Tests**: Validate database operations and functional flows against a WordPress test environment.
    ```bash
    ./vendor/bin/phpunit -c phpunit-integration.xml
    ```

#### Setting up Integration Tests
Integration tests require a WordPress test environment and a separate database.

1.  **Install Dependencies**:
    If you are using the provided Dev Container, `subversion` and `mysql-client` are **already installed**. Otherwise, ensure they are available in your environment:
    ```bash
    apt-get update && apt-get install -y subversion default-mysql-client
    ```

2.  **Initialize Environment**:
    Use the provided script to set up the WordPress tests library and test database.
    ```bash
    # Usage: ./scripts/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]
    
    # Example for local DevContainer (export root password first):
    export MYSQL_ROOT_PASSWORD=changeme_db_root_password
    ./scripts/install-wp-tests.sh wordpress_test wordpress changeme_db_password db latest
    ```
    *Note: The script requires `root` privileges to create the test database. Export `MYSQL_ROOT_PASSWORD` if your environment does not have passwordless root access to MySQL.*

### Running Tests in VS Code
You can run PHPUnit tests directly from VS Code with a click of a button:

1. Open the Command Palette (`Ctrl+Shift+P`)
2. Run "Tasks: Run Test Task" and select from:
   - `phpunit:unit` - Run unit tests
   - `phpunit:integration` - Run integration tests
   - `phpunit:all` - Run all tests

3. Alternatively, you can use the Tasks view (`Ctrl+Shift+P` > "Tasks: Run Task")
4. Or, use the debug configurations in the Debug view to run and debug specific tests

### Data Seeding
For development and staging environments, a data seeding script is provided to generate realistic datasets:
```bash
php scripts/seed-data-enhanced.php --schools=10 --events=5 --users=50
```

## Contribution Guidelines

Project contributions should follow the standard GitHub workflow:
1.  Fork the repository.
2.  Create a feature branch for the specific change.
3.  Commit changes with clear, descriptive messages.
4.  Submit a Pull Request for review.

## License

This project is licensed under the Apache-2.0 License. See the `LICENSE` file for details.

## Acknowledgments
*   **PhpSpreadsheet**: Utilized for Excel document generation.
*   **WP_Mock**: Employed for unit testing within the WordPress environment.
