# Cappadocia Viewer for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hsndmr/cappadocia-viewer-for-laravel.svg?style=flat-square)](https://packagist.org/packages/hsndmr/cappadocia-viewer-for-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hsndmr/cappadocia-viewer-for-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hsndmr/cappadocia-viewer-for-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/hsndmr/cappadocia-viewer-for-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/hsndmr/cappadocia-viewer-for-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hsndmr/cappadocia-viewer-for-laravel.svg?style=flat-square)](https://packagist.org/packages/hsndmr/cappadocia-viewer-for-laravel)


## Installation

You can install the package via composer:

```bash
composer require hsndmr/cappadocia-viewer-for-laravel --dev
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cappadocia-viewer"
```

This is the contents of the published config file:

```php
return [
    'server_url' => env('CAPPADOCIA_VIEWER_SERVER_URL', 'http://127.0.0.1:9091'),
    'timeout'    => env('CAPPADOCIA_VIEWER_TIMEOUT', 3),
    'enabled'    => env('CAPPADOCIA_VIEWER_ENABLED', true),
];
```

## Usage

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hasan Demir](https://github.com/hsndmr)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
