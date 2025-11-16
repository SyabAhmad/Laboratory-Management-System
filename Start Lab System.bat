@echo off
cd /d "%~dp0"

:: --- Start Laravel server in background ---
powershell -WindowStyle Hidden -Command "Start-Process 'php' -ArgumentList 'artisan serve'"

:: --- Open default browser to the project ---
start "" "http://127.0.0.1:8000"
