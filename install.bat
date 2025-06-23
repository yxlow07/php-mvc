@echo off

echo Installing npm modules...
npm install
echo npm modules installed successfully.
echo Installing composer dependencies...
composer install --no-dev
echo Composer dependencies installed successfully.