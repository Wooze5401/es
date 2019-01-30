<h1 align="center"> ES for Laravel </h1>

<p align="center"> .</p>


## Installing

```shell
$ composer require wooze/es
```
publish
```
$ php artisan vendor:publish --provider=Wooze\Es\ServiceProvider
```

## model
```
namespace App\Models;
use Wooze\Es\Models; 
 
class examle extends EsModels
{
    protected $esArray = [
            'id',
    ];
} 
```
## config
```
?php
return [
    'indices' => [
        'example' => [
            'name' => '',
            'properties' => [
             ],
            'settings' => [
            ],
            'model' => \App\Models\Example::class,
        ],
    ]
];
```


## builder
$key is the key in app_indices.php   
$fields is the array to match, the item could use ^3 to set the weight
```
$builder = new Wooze\Es\SearchBuilders($key, $fields);
```

#### paginate
$size size of per page   
$page current num of page, default page = 1
```
$builder->paginate($size, $page)
```

#### keywords
accept string or array
```
$builder->keywords($keywords)
```
#### sort
```
$builder->asc($field)
$builder->desc($field)
```

#### range
```
$builder->range($field, $operation, $value)
```
#### getParams
return the array of all param
```
$builder->getParams()
```

#### start search
return the array of id as result
```
app('es')->search($builder->gerParams());
```

## License

MIT