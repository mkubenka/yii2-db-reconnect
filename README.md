# SQS Queue

> Inspired by:
>
> https://github.com/xjflyttp/yii2-db-reconnect

## Composer
```json
"require": {
    "mkubenka/yii2-db-reconnect": "~1.0"
},
```

## Config
```php
'db' => [
    'class' => 'mkubenka\dbreconnect\mysql\Connection',
    'reconnectMaxCount' => 2,
    'dsn' => 'mysql:host=127.0.0.1;dbname=test',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
],
```

## log
```php
'log' => [
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'info'],
            'maxLogFiles' => 20,
            'maxFileSize' => 2048,
            'categories' => [
                'mkubenka\dbreconnect\*',
            ],
            'logFile' => '@frontend/runtime/logs/dbreconnect.log',
        ],
    ],
],
```
