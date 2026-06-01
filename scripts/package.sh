#!/usr/bin/env bash

# Exit on error
set -e

PROJECT_NAME="Training-Registration"
ARCHIVE_NAME="${PROJECT_NAME}.zip"
BUILD_DIR="build_tmp"

echo "Creating release package for ${PROJECT_NAME}..."

# Move to the project root (one level up from the scripts folder)
cd "$(dirname "$0")/.."

# Cleanup old build and archive
rm -rf "${BUILD_DIR}"
rm -f "${ARCHIVE_NAME}"

echo ""
echo "Preparing temporary build directory..."
mkdir -p "${BUILD_DIR}"

# Copy necessary files and folders
echo "Copying files..."
cp -r assets "${BUILD_DIR}/"
cp -r files "${BUILD_DIR}/"
cp -r src "${BUILD_DIR}/"
cp -r templates "${BUILD_DIR}/"
cp README.md "${BUILD_DIR}/"
cp Training-registration.php "${BUILD_DIR}/"
cp composer.json "${BUILD_DIR}/"
cp composer.lock "${BUILD_DIR}/"

echo ""
echo "Installing production dependencies..."
(
    cd "${BUILD_DIR}"
    composer install --no-dev --optimize-autoloader --quiet
)

echo ""
echo "Creating the archive: ${ARCHIVE_NAME}"
# Zip the contents of the build directory
(
    cd "${BUILD_DIR}"
    zip -r "../${ARCHIVE_NAME}" . > /dev/null
)

echo ""
echo "Cleaning up..."
rm -rf "${BUILD_DIR}"

echo ""
echo "Successfully created ${ARCHIVE_NAME}"
echo ""
