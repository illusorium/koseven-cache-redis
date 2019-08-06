Simple redis cache module for Koseven

### Install via composer
`composer require illusorium/koseven-cache-redis:dev-master`

### Usage
First, add module to modules list in APPPATH/bootstrap.php:

`
Kohana::modules([
    'cache-redis' => MODPATH . 'koseven-cache-redis'
])
`

To set Redis cache as default,
either set key `'default' => 'redis'` in config/cache.php
or set `Cache::$default = 'redis'` before usage.

To use Redis cache only for some keys, call

`Cache::instance('redis')->set|get(...)`