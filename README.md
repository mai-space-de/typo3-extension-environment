# TYPO3 Extension: environment

This extension provides environment-dependent helper functions for setting up TYPO3. It allows you to easily manage different configurations for development, testing, and production environments, including support for DDEV.

## Features

- Environment-based configuration presets.
- DDEV environment detection and automatic configuration.
- Easy inclusion of context-dependent configuration files.
- Helpers for common TYPO3 settings (database, caching, logging, etc.).

## Installation

```bash
composer require maispace/environment
```

## Usage

In your `public/index.php` (or wherever you initialize your environment), you can use the `ConfigProvider`:

```php
use Maispace\Environment\ConfigProvider\ConfigProvider;

ConfigProvider::initialize();
```

Or with custom defaults:

```php
ConfigProvider::initialize(false)
    ->useDevelopmentPreset()
    ->applyDefaults();
```

### Available Methods

The `ConfigProvider` implements `ConfigProviderTraitInterface`, which provides many useful methods:

- `appendContextToSiteName()`: Appends the application context (e.g., [Development]) to the site name.
- `useDDEVConfiguration()`: Automatically configures database and other settings for DDEV.
- `useProductionPreset()` / `useDevelopmentPreset()`: Applies common settings for production or development.
- `initializeDatabaseConnection()`: Set up database connection.
- `initializeRedisCaching()`: Set up Redis caching.
- `includeContextDependentConfigurationFiles()`: Includes files from `config/system/additional/<Context>.php`.

## License

GPL-2.0-or-later
