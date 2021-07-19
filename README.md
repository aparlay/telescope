# Core functionality share between Alua and Waptap

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aparlay/core.svg?style=flat-square)](https://packagist.org/packages/aparlay/core)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/aparlay/core/run-tests?label=tests)](https://github.com/aparlay/core/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/aparlay/core/Check%20&%20fix%20styling?label=code%20style)](https://github.com/aparlay/core/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/aparlay/core.svg?style=flat-square)](https://packagist.org/packages/aparlay/core)

## Installation

You can install the package via composer:

```bash
composer require aparlay/core
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Aparlay\Core\CoreServiceProvider" --tag="core-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Aparlay\Core\CoreServiceProvider" --tag="core-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$core = new Aparlay\Core();
echo $core->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ramin Farmani](https://github.com/farmani)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
