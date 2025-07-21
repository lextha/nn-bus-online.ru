<?php

use himiklab\sitemap\behaviors\SitemapBehavior;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'language' => 'ru-RU',
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
   // 'bootstrap' => ['log','debug'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
       /* 'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['5.187.70.511']
        ],*/
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                // your models
               // 'common\models\City',
                'common\models\Route',
                //'common\models\Station',
            ],
            'enableGzip' => false, // default is false
            'cacheExpire' => 1, // 1 second. Default is 24 hours
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
             'cookieValidationKey' => 'z0vcJPphI-rect_i-P00GJBvZPHuHTZP',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'frontend\components\SlugUrlRule'],
                [
                    'pattern' => 'sitemap', 
                    'route' => 'sitemap/default/index', 
                    'suffix' => '.xml'
                ],
                '' => 'site/index',
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
            ],
        ],
        
    ],
    'params' => $params,
];
