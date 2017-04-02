# SQS Queue [![Build Status](https://travis-ci.org/mkubenka/yii2-db-reconnect.svg?branch=master)](https://travis-ci.org/mkubenka/yii2-db-reconnect)

> Inspired by:
>
> https://github.com/xjflyttp/yii2-db-reconnect

## Installation
```sh
composer require mkubenka/yii2-db-reconnect
```

or

```json
"require": {
    "mkubenka/yii2-db-reconnect": "~1.0"
},
```

## Configuration
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

### Logging
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

## Known limitations

This method only works for non transactional statements.
