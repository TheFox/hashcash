# Hashcash
PHP implementation of [Hashcash](http://hashcash.org) 1.

[![Latest Stable Version](https://poser.pugx.org/thefox/hashcash/v/stable.png)](https://packagist.org/packages/thefox/hashcash)
[![Latest Unstable Version](https://poser.pugx.org/thefox/hashcash/v/unstable.png)](https://packagist.org/packages/thefox/hashcash)
[![License](https://poser.pugx.org/thefox/hashcash/license.png)](https://packagist.org/packages/thefox/hashcash)

## Installation
The preferred method of installation is via [Packagist](https://packagist.org/packages/thefox/hashcash) and [Composer](https://getcomposer.org/). Run the following command to install the package and add it as a requirement to composer.json:

`composer.phar require "thefox/hashcash=1.4.*"`

## Usage
See `examples.php` for more examples.

```php
<?php
require 'vendor/autoload.php';
use TheFox\Pow\Hashcash;
$hashcash = new Hashcash(20, 'example@example.com');
print "hashcash stamp: '".$hashcash->mint()."'\n";
?>
```

## Contribute
You're welcome to contribute to this project. Fork this project at <https://github.com/TheFox/hashcash>. You should read GitHub's [How to Fork a Repo](https://help.github.com/articles/fork-a-repo).

## License
See [LICENSE.md](LICENSE.md) file.
