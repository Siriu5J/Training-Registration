# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin called "Training Registration Plugin v3" that allows SOT training organizers to create training registration forms where Learning Centers can register to. The plugin is built using PHP and leverages the PhpSpreadsheet library for Excel file handling.

## Key Features

- Create and manage training events
- Post training information to Learning Centers
- Manage training status (registration open/close times, activation status)
- View and manage registered trainees
- Download training registration data as Excel Spreadsheet

## Architecture and Structure

The plugin is structured as a WordPress plugin with:
- Core PHP files implementing the plugin functionality
- Integration with PhpSpreadsheet library for Excel file operations
- WordPress-specific hooks and filters for plugin integration

## Development Commands

Since this is a WordPress plugin:
- No build process required
- No linting or testing frameworks specified
- Plugin must be installed and activated in WordPress to test

## Key Components

- The plugin integrates with WordPress hooks and APIs
- It uses PhpSpreadsheet for Excel file generation
- Core functionality is implemented in PHP files
- WordPress-specific features are implemented using standard WordPress plugin patterns

## Installation Requirements

The plugin requires:
- WordPress installation
- Twenty Twenty theme
- Proper permalinks settings (Post Name)
- Specific CSS customization for UI elements