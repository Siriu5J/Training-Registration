@echo off
setlocal

set "PROJECT_NAME=Training-Registration"
set "ARCHIVE_NAME=%PROJECT_NAME%.zip"
set "SEVENZIP_PATH=7z.exe"

echo Creating release package for %PROJECT_NAME%...

REM Check if old archive exists and delete it
if exist "%ARCHIVE_NAME%" (
    echo Deleting existing archive: %ARCHIVE_NAME%
    del "%ARCHIVE_NAME%"
)

REM Create the archive
"%SEVENZIP_PATH%" a -tzip "%ARCHIVE_NAME%" ^
    "ui" ^
    "admin" ^
    "files" ^
    "includes" ^
    "vendor" ^
    "README.md" ^
    "Training-registration.php"

if %errorlevel% neq 0 (
    echo.
    echo An error occurred during packaging.
    echo Please ensure 7-Zip is installed and in your PATH.
    exit /b 1
)

echo.
echo Successfully created %ARCHIVE_NAME%
echo.

endlocal
