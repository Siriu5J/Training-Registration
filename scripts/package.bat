@echo off
setlocal enabledelayedexpansion

set "PROJECT_NAME=Training-Registration"
set "ARCHIVE_NAME=%PROJECT_NAME%.zip"
set "BUILD_DIR=build_tmp"
set "SEVENZIP_PATH=7z.exe"

echo Creating release package for %PROJECT_NAME%...

REM Move to the project root
cd /d "%~dp0.."

REM Cleanup old build and archive
if exist "%BUILD_DIR%" rd /s /q "%BUILD_DIR%"
if exist "%ARCHIVE_NAME%" del "%ARCHIVE_NAME%"

echo.
echo Preparing temporary build directory...
mkdir "%BUILD_DIR%"

REM Copy necessary files and folders
echo Copying files...
xcopy "assets" "%BUILD_DIR%\assets\" /E /I /H /Y >nul
xcopy "files" "%BUILD_DIR%\files\" /E /I /H /Y >nul
xcopy "src" "%BUILD_DIR%\src\" /E /I /H /Y >nul
xcopy "templates" "%BUILD_DIR%\templates\" /E /I /H /Y >nul
copy "README.md" "%BUILD_DIR%\" >nul
copy "Training-registration.php" "%BUILD_DIR%\" >nul
copy "composer.json" "%BUILD_DIR%\" >nul
copy "composer.lock" "%BUILD_DIR%\" >nul

echo.
echo Installing production dependencies...
pushd "%BUILD_DIR%"
call composer install --no-dev --optimize-autoloader --quiet
if %errorlevel% neq 0 (
    echo Error: Composer install failed.
    popd
    exit /b 1
)
popd

echo.
echo Creating the archive: %ARCHIVE_NAME%
REM Zip the contents of the build directory
"%SEVENZIP_PATH%" a -tzip "%ARCHIVE_NAME%" ".\%BUILD_DIR%\*"

if %errorlevel% neq 0 (
    echo.
    echo An error occurred during packaging.
    echo Please ensure 7-Zip is installed and in your PATH.
    exit /b 1
)

echo.
echo Cleaning up...
rd /s /q "%BUILD_DIR%"

echo.
echo Successfully created %ARCHIVE_NAME%
echo.

endlocal
