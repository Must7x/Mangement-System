@echo off
cd /d "%~dp0"
echo ========================================
echo   نظام ادارة الموارد والمعدات
echo   http://127.0.0.1:8000
echo ========================================
echo.
php artisan serve
pause
