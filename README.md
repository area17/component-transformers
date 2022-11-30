# Component Transformers

Design systems are made up of components designed to be used everywhere but often code used to parse data for the components is repeated during integration. The Component Transformers package allows you to create a transformer for each component and variation to transform the data coming from the application into a format the front end components will be able to consume.

It's designed to be used with Twill and works with both page and block data.

## Publish config

```bash
php artisan vendor:publish --tag=component-transformers-config
```

## Creating a Transformer

You can manually create a file in your `app` directory using the following as a base:

```php
<?php

namespace App\Transformers;

use A17\ComponentTransformers\Base;

class ComponentName extends Base
{
    public function primary($data = null): array
    {
        return [
            // 'title' => $data->title ?? null,
        ];
    }
}

```

or you can automatically create one with the built in command:

```bash
php artisan transformer:create
```

This will create a file like the one above in your `app` directory. If a file with the same name as the component name you entered, if will add a method to the end of that file using the variation name.

## Using a transformer

### In a block

```php
$items = Transform::listing($block->getRelated('items'))->primary();
```

### In a controller

```php
use A17\ComponentTransformers\Transform;

class MyController extends Controller
{
    public function index()
    {
        $item = MyModel::first();

        return [
            'hero' => Transform::hero($item)->primary(),
            'listing' => Transform::listing($item)->primary(),
        ];
    }
}
```
