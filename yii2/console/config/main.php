<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
         /*   'mailer' => [
                'class' => 'yii\swiftmailer\Mailer',
                'viewPath' => '@app/mail',
                'htmlLayout' => 'layouts/main-html',
                'textLayout' => 'layouts/main-text',
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => ['noreply@site.com' => 'Site Name'],
                ],
                'useFileTransport' => false,
            ],*/
        ],
        'user' => [
    'class' => 'mdm\admin\models\User',
    'identityClass' => 'mdm\admin\models\User',
    'loginUrl' => ['admin/user/login'],
]
    ],
    'params' => $params,
];
