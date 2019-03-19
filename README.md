# Hashcash

Pure PHP implementation of [Hashcash](http://hashcash.org) 1.

## Project Outlines

The project outlines as described in my blog post about [Open Source Software Collaboration](https://blog.fox21.at/2019/02/21/open-source-software-collaboration.html).

- The main purpose of this software is to implement [Hashcash](http://hashcash.org) using pure PHP.
- The features should not go beyond the features and functions  the original implementation of Hashcash is providing. So the feature-set is kind of restricted what could be possible in the future. But still, feel free to request features.

## Installation

The preferred method of installation is via [Packagist](https://packagist.org/packages/thefox/hashcash) and [Composer](https://getcomposer.org/). Run the following command to install the package and add it as a requirement to composer.json:

	composer.phar require "thefox/hashcash=~1.6"

## Usage

See `examples.php` for more examples.

```php
<?php
require 'vendor/autoload.php';
use TheFox\Pow\Hashcash;
$stamp = Hashcash::newInstance(20, 'example@example.com')->mint();
print "hashcash stamp: '".$stamp."'\n";
?>
```

### Alternative Usage

```php
<?php
require 'vendor/autoload.php';
use TheFox\Pow\Hashcash;
$hashcash = new Hashcash(20, 'example@example.com');
print "hashcash stamp: '".$hashcash->mint()."'\n";
?>
```

## Links

- [Packagist Package](https://packagist.org/packages/thefox/hashcash)
- [Travis CI Repository](https://travis-ci.org/TheFox/hashcash)
- [PHPWeekly - Issue June 5, 2014](http://phpweekly.com/archive/2014-06-05.html)

## Contribute

You're welcome to contribute to this project. Fork this project at <https://github.com/TheFox/hashcash>. You should read GitHub's [How to Fork a Repo](https://help.github.com/articles/fork-a-repo).

## License

Copyright (C) 2014 Christian Mayer <https://fox21.at>

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
