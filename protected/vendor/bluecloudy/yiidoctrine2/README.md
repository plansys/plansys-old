Yii Doctrine 2
============

This is an extension for the Yii Framework that integrates Doctrine 2 ORM & ODM projects 

## Requirements

* PHP 5.3.2 (or later)*
* YiiFramework 1.1.14 (or later)

## Installation

### Installing Manually 
0. Download and place the 'yiidoctrine2' directory in your Yii extension directory.

0. In config/main.php you will need to add the YiiDoctrine alias. This allows for flexability in where you place the extension.

```php
	'aliases' => array(
		.. .
        'YiiDoctrine' => realpath(__DIR__ . '/../extensions/bluecloudy/yiidoctrine2'),
        .. .
	),
```

0. Include ext.bluecloudy.YiiDoctrine.components.YDComponent.

```php
	'components' => array(
		'doctrine'=>array(
			'class' => 'YiiDoctrine.components.YDComponent',
			'basePath'      => dirname(__FILE__),
			'proxyPath'     => dirname(__FILE__).'/proxies',
			'entityPath'    => array(
				dirname(__FILE__).'/models'
			),
			'cachePath'  => dirname(dirname(__FILE__)) . '/cache',
			'db' => array(
				'driver' => 'pdo_sqlite',
				'path' => dirname(__FILE__).'/data/blog.db'
			)
		)
	)
```

### Installing With [Composer](http://getcomposer.org)

```JSON
{
    "require": {
        "bluecloudy/yiidoctrine2": "dev-master"
    }
}
```

0. In config/main.php you will need to add the YiiDoctrine alias. This allows for flexability in where you place the extension.

```php
	'aliases' => array(
		.. .
		//Path to your Composer vendor dir plus vendor/bluecloudy path
		'YiiDoctrine' =>realpath(__DIR__ . '/../../vendor/bluecloudy/yiidoctrine2/bluecloudy/yiidoctrine2'),
        .. .
	),
```

