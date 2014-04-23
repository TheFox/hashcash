# Hashcash
PHP implementation of [Hashcash](http://hashcash.org) 1.

## Installation
The preferred method of installation is via [Packagist](https://packagist.org/packages/thefox/hashcash) and [Composer](https://getcomposer.org/). Run the following command to install the package and add it as a requirement to composer.json:

`composer.phar require "thefox/hashcash=1.0.*"`

## Usage
See `examples.php` for more examples.

```php
<?php
require 'vendor/autoload.php';
$hashcash = new Hashcash(20, 'example@example.com');
print "hashcash stamp: '".$hashcash->mint()."'\n";
?>
```

## License
Copyright (C) 2014 Christian Mayer (<thefox21at@gmail.com> - <http://fox21.at>)

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
