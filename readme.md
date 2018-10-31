# Laravel Telescope Pruning

[![Packagist License](https://poser.pugx.org/insenseanalytics/laravel-telescope-pruning/license.png)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://poser.pugx.org/insenseanalytics/laravel-telescope-pruning/version.png)](https://packagist.org/packages/insenseanalytics/laravel-telescope-pruning)
[![Total Downloads](https://poser.pugx.org/insenseanalytics/laravel-telescope-pruning/d/total.png)](https://packagist.org/packages/insenseanalytics/laravel-telescope-pruning)

This package enables you to intelligently prune your Laravel Telescope entries. You may specify a cap on the number of batches (application cycles) of entries you need. You can also whitelist certain tags (or monitored tags) to avoid pruning them, or specify a separate pruning cap for whitelisted tags!

## Quick Installation
Pull in the package using composer:

```bash
composer require insenseanalytics/laravel-telescope-pruning
```

To copy the config to your app's config directory:

```bash
php artisan vendor:publish --provider="Insense\LaravelTelescopePruning\TelescopePruningServiceProvider"
```

#### Service Provider (Optional / auto discovered on Laravel 5.5+)
Register the provider in your `config/app.php` file:
```php
'providers' => [
    ...,
    Insense\LaravelTelescopePruning\TelescopePruningServiceProvider::class,
]
```

## Requirements
- [PHP >= 7.1](http://php.net/)
- [Laravel 5.7+](https://github.com/laravel/framework)
- [Laravel Telescope](https://github.com/laravel/telescope)

## Contributing
We are open to PRs as long as they're backed by tests and a small description of the feature added / problem solved.

## License

The MIT License (MIT). Please see [License File](https://github.com/insenseanalytics/laravel-telescope-pruning/blob/master/LICENSE.txt) for more information.
