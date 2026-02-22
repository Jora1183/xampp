@echo off
echo ================================================
echo  Joomla Language Configuration Updater
echo ================================================
echo.
echo This script will:
echo 1. Backup your current configuration.php
echo 2. Add Russian language setting
echo 3. Remove read-only attribute
echo.
pause

REM Navigate to Joomla directory
cd /d "c:\xampp\htdocs\Joomla_5.4.1"

REM Create backup with timestamp
echo Creating backup...
copy configuration.php configuration.php.backup.%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
if %errorlevel% neq 0 (
    echo ERROR: Could not create backup!
    pause
    exit /b 1
)
echo Backup created successfully!

REM Remove read-only attribute
echo Removing read-only attribute...
attrib -r configuration.php
if %errorlevel% neq 0 (
    echo WARNING: Could not remove read-only attribute. You may need to run as Administrator.
)

REM Check if language setting already exists
findstr /C:"public $language" configuration.php >nul
if %errorlevel% equ 0 (
    echo Language setting already exists!
    echo Opening file for manual edit...
    notepad configuration.php
    goto :end
)

REM Create temporary file with new content
echo Adding language setting...
powershell -Command "(Get-Content configuration.php) -replace \"(public \$sitename = 'divnayausadba';)\", \"`$1`r`n`tpublic \$language = 'ru-RU';\" | Set-Content configuration_new.php"

if exist configuration_new.php (
    echo Replacing configuration file...
    del configuration.php
    ren configuration_new.php configuration.php
    echo.
    echo ================================================
    echo SUCCESS! Russian language has been set!
    echo ================================================
    echo.
    echo Changes made:
    echo - Added: public $language = 'ru-RU';
    echo.
    echo Next steps:
    echo 1. Open your browser
    echo 2. Go to: http://localhost/Joomla_5.4.1/
    echo 3. The site should now display in Russian!
    echo.
    echo If you need to revert:
    echo - Use the backup file created above
    echo.
) else (
    echo ERROR: Could not create new configuration file!
    echo.
    echo Manual steps required:
    echo 1. Open configuration.php in Notepad
    echo 2. Find line: public $sitename = 'divnayausadba';
    echo 3. Add new line after it:
    echo    public $language = 'ru-RU';
    echo 4. Save the file
    echo.
    notepad configuration.php
)

:end
pause
