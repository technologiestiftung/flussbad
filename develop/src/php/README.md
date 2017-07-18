# Example (Medoo)

This example shows the last 10 measured values and some additional information.

The [Medoo Database Framework](https://medoo.in/) is used for this example.
Its documentation can be found [here](https://medoo.in/doc).

## Installation

1. Configure your database by editing `index.php`
2. Upload the the following files via FTP:
```
Medoo.php
index.php
```
3. Open your browser and type in the address pointing to your `index.php`

# Propel

## Installation

1. Connect to your shared hosting service via SSH

2. Download Composer
```
$ curl -sS https://getcomposer.org/installer | /usr/bin/php5.5-cli
```
Use it with
```
$ /usr/bin/php5.5-cli path/to/composer.phar
```

3. [Install Propel](http://propelorm.org/documentation/01-installation.html#setup)
4. Build a Propel project [the easy way](http://propelorm.org/documentation/02-buildtime.html#the-easy-way)
```
$ /usr/bin/php5.5-cli path/to/vendor/bin/propel init
```
In this case we let Propel create our model classes from an exsiting database.

Therefore we additionally need to apply the following steps:
1. Add the following to `composer.json`

```
{
  ...
    "autoload": {
      "classmap": ["path/to/generated/models/folder/"]
    }
}
```
2. Execute
```
$ /usr/bin/php5.5-cli path/to/composer.phar dump-autoload
```

For a more detailed explanation see [here](http://propelorm.org/documentation/02-buildtime.html#the-hard-way)

5. Create your `index.php`

```php
<?php
// setup the autoloading
require_once '/path/to/vendor/autoload.php';

// setup Propel
require_once '/generated-conf/config.php';

// here goes your code...
?>
```
