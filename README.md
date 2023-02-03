## laravel-fleet

> Laravel微脚手架，整合了Service、Repository、Cache、Transform分层结构

#### 环境及扩展要求

- `^php8.2`
- `redis`
- `ext-redis`
- `ext-http`

#### 安装

```shell
composer require xgbnl/laravel-cloud

php artisan install:cloud 
```
#### 配置缓存层

Cacheable将使用`redis`管理你的缓存,所以你要为你的`.env`进行配置

```dotenv
CACHEABLE=cache
```

编辑 `config/database.php`，添加新的键值`cache`

```php 
'redis' => [

    // add 
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '1'),
        ],
] 
```
