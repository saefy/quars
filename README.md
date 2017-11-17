# quars (Framework Core)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is the Quars Framework Core, please use [saefy/quars-application](https://github.com/saefy/quars-application/) to create web apps with Quars.

Quars is a PHP Framework for small projects, easy to implement. It uses Phroutes/phroutes for routing, blade template engine used in Laravel.

- Database is handled with \Quars\Db\Db
- ActiveRecord library is \Quars\ActiveRecord

## Structure

This is the directory structure you should use for your project. 

```
my_application_example/
  app/
    Cache/
    Config/
    Controllers/
    Errors/
    Helpers/
    Libraries/
    Models/
    Routes/
    Services/
    Views/
    boostrap/
    public/
vendor/
```


## Install

Via Composer

``` bash
$ composer require saefy/quars
```

## Usage

This is the main framework source code should be installed ussing composer, please refer to saefy/quars-aplication to get all folder structure for your project.

After having all code structure run:
```
composer install
sh quars_serve application_example
```
Note: quars_serve is for dev purposes only, don't use it in prod environments.

``` php
// Run the app
\Quars\Request::serve();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email mmendoza000@gmail.com instead of using the issue tracker.

## Credits

- [Miguel Mendoza C.][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/saefy/quars.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/saefy/quars/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/saefy/quars.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/saefy/quars.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/saefy/quars.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/saefy/quars
[link-travis]: https://travis-ci.org/saefy/quars
[link-scrutinizer]: https://scrutinizer-ci.com/g/saefy/quars/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/saefy/quars
[link-downloads]: https://packagist.org/packages/saefy/quars
[link-author]: https://github.com/mmendoza000
[link-contributors]: ../../contributors
