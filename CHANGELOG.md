# Changelog

All notable changes to `laravel-tgbotapi` will be documented in this file

## 1.0.0 - 14.03.2022

- initial release

## 1.1.0 - 24.03.2022

- Laravel 9 support
- Added API request timeout
- Updated readme
- Removed all optional third-party dependencies

## 1.1.1 - 14.04.2022

- Improved work with html5 games (detection of UpdateType)
- New UpdateType - TEXT
- New magic method __isset to check if an attribute exists in an update

## 1.2.0 - 02.11.2022

- Traits for handling updates in controllers
- Command to create a controller that handles updates of a specific type

## 1.2.1 - 03.11.2022

- PHP minimum version fixes for Laravel 8
- Workflow improvement for Github Actions

## 1.2.2 - 13.11.2022

- New UpdateType's

## 1.2.3 - 13.11.2022

- Reworking tgbotapi:getupdates command for long background work

## 1.3.0 - 19.11.2022

- Another rework of tgbotapi:getupdates command for long background work, fixing issues
- Ability to make asynchronous Telegram Bot Api calls #6 (powered by Laravel Octane with Swoole)

## 1.3.1 - 20.11.2022

- Automatic code style change using Laravel Pint