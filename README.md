# Core functionality share between Alua and Waptap

## Installation

You can install the package via composer:

```bash
composer require aparlay/core
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Aparlay\Core\CoreServiceProvider" --tag="core-migrations"
php artisan migrate

php artisan db:seed --class="\Aparlay\Core\Database\Seeders\DatabaseSeeder"
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
