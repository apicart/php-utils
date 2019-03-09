# Information

This package provide a small set of useful utilities for simpler development.

# Installation

The simplest way to install `apicart/php-utils` is using  [Composer](http://getcomposer.org/):

```sh
$ composer require apicart/php-utils
```

# Utils

## Arrays

### Array merging

```php
<?php 

use Apicart\Utils\Arrays\Arrays;

$one = ['a' => 1, 'b' => [1, 2, 3], 'c' => false];
$two = ['c' => true, 'd' => ['a']];

$result = Arrays::merge($two, $one);
// $result = ['a' => 1, 'b' => [1, 2, 3], 'c' => true, 'd' => ['a']];
```

### Array accesing

```php
<?php

use Apicart\Utils\Arrays\Arrays;

$data = [
	'id' => 12,
	'tags' => ['first', 'second', 'third'],
	'parameters' => [
		[
			'key' => 'billingAddress',
			'value' => [
				'firstName' => 'John',
				'lastName' => 'Doe',
			],
		],
		[
			'key' => 'paymentMethod',
			'value' => 'PPL',
		],
	],
	'tree' => [
		'left' => [
			'node' => 'Left node',
		],
		'right' => [
			'node' => 'Right node',
		],
	],
];

Arrays::get($data, 'id'); // 12
Arrays::get($data, 'tags.1'); // "second"
Arrays::get($data, 'parameters.key:billingAddress.value.firstName'); // "John"
Arrays::get($data, 'parameters.key:paymentMethod.value'); // "PPL"
Arrays::get($data, 'tree.right.node'); // "Right node"

Arrays::get($data, 'tree.center.node'); // null
Arrays::get($data, 'tree.center.node', 'Default value'); // Default value
```

### Collections

```php
<?php

use Apicart\Utils\Arrays\Collection;

$collection = new Collection(function (){
	return [1, 2, 3]; // or use some service for lazy loading
});

foreach ($collection as $item){
	echo $item; // 1, 2, 3
}
```


## Hashes

### Generating

```php
<?php

use Apicart\Utils\Hashes\Hashes;

$hash = Hashes::generate();
// $hash = 'e6de5f4cc9fa29fc34c23fba0d499da8';

$hash = Hashes::generate(10, 'A-Z');
// $hash = 'AKUTHNEFPL';
```