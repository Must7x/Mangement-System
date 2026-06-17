@echo off
cd /d "%~dp0"
echo ========================================
echo   نظام ادارة الموارد والمعدات
echo   http://127.0.0.1:8000
echo ========================================
echo.
php artisan db:seed --class=UserSeeder --force --no-interaction
php artisan serve
pause
