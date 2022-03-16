Laravel Facebook Catalog
==============

## Installation

```
composer require raynl/laravel-facebook-catalog
```

## Example
```php
use RayNl\LaravelFacebookCatalog\LaravelFacebookCatalog;

LaravelFacebookCatalog::setTitle('Example feed');
LaravelFacebookCatalog::setDescription('Example feed of the Example shop');
LaravelFacebookCatalog::setLink('https://example.shop');

LaravelFacebookCatalog::addItem(
	"https://example.shop/p/foo-bar",
	"SKU123",
	"Foo bar",
	"https://example.shop/images/foo-bar.png",
	"Foo bar best product",
	'new',
	99.99,
	'Foo brand',
	'6387712293758',
	[
		'country' => 'NL',
		'service' => 'PostNL - Gratis',
		'price'	  => 0
	]
);

return LaravelFacebookCatalog::display();
```