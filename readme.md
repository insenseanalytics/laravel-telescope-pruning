# Insense/LaravelTelescopePruning

This package is created to ....

## Basic Example

```php
...
```

## Requirements
- [PHP >= 7.1](http://php.net/)
- [Laravel 5.5|5.6|5.7](https://github.com/laravel/framework)
- [Laravel Telescope](https://github.com/laravel/telescope)

## Quick Installation
```bash
$ composer require insenseanalytics/laravel-telescope-pruning
```

#### Service Provider (Optional / auto discovered on Laravel 5.5+)
Register provider on your `config/app.php` file.
```php
'providers' => [
    ...,
    Insense\LaravelTelescopePruning\TelescopePruningServiceProvider::class,
]
```

## Contributing
We are open to PRs as long as they're backed by tests and a small description of the feature added / problem solved.

## License

The MIT License (MIT). Please see [License File](https://github.com/insenseanalytics/laravel-telescope-pruning/blob/master/LICENSE.txt) for more information.
