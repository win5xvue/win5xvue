# Reflection Mocker
<p align="center">
<a href="https://codecov.io/gh/ReeceM/mocker">
  <img src="https://codecov.io/gh/ReeceM/mocker/branch/master/graph/badge.svg" />
</a>
<a href="https://travis-ci.com/ReeceM/mocker">
  <img src="https://travis-ci.com/ReeceM/mocker.svg?branch=master" />
</a>
<a href="https://packagist.org/packages/reecem/mocker"><img src="https://poser.pugx.org/reecem/mocker/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/reecem/mocker"><img src="https://poser.pugx.org/reecem/mocker/license" alt="License"></a>
<a href="https://packagist.org/packages/reecem/mocker"><img src="https://poser.pugx.org/reecem/mocker/downloads" alt="Downloads"></a>
</p>

This package is initially made to for an issue on the MailEclipse package, but improvements are welcome.
It currently is probably stupid simple, but deals with the one job of reading a file and mocking it.

> Generate a mocked instance of the un-typed params in a __construct() method

This searches the file retrieved from the reflection class and looks for all object like arrow calls;
ie: 

```php
...
public function __construct($objectArg, string $arg) 
{
    $this->value    = $objectArg->value; // this will be picked up
    $this->name     = $arg;
}
...
```

## Installation

You can install the package via composer:

```bash
composer require reecem/mocker
```
## Requirements 

- Laravel ^5.6 (min)

## Usage

```php

use ReeceM\ReflectionMockery;

/**
 * The class __construct Method is automatically read and args created
 */
$mock = new ReflectionMockery('\App\User');
// or
$mock = new ReflectionMockery(new \ReflectionClass('\App\User'));

// some time later

/**
 * Use call a variable from the class that don't exist
 */
{{ $mock->get('somethingNotInUser') }}
{{ $mock->somethingNotInUser }}

// both would return 

"mock->somethingNotInUser"
// if something was set in user
'mock->somethingNotInUser => ["value that set"]'

```

### Security

If you discover any security related issues, please email zsh.rce@gmail.com instead of using the issue tracker.

## Credits

- [ReeceM](https://github.com/ReeceM)
- [All Contributors](../../contributors)

## todo

- [ ] Add functionality to account for `$this->internal = $param;` searching so it works later on in the code
- [ ] Add a translation file for mocked values when testing to give a translated result for previews

## Support
Consider supporting some code if it is useful to you :smile:


- [MailEclipse](https://github.com/Qoraiche/laravel-mail-editor) "just a small donation from Laravel mail editor owner, thanks for your contributions"

<p align="center">
<a href='http://bit.ly/2J4ZPBM' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi4.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>
</p>


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
