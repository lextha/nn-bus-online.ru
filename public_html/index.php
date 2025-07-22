<?php

//define('YII_ENABLE_ERROR_HANDLER', false);
//define('YII_ENABLE_EXCEPTION_HANDLER', false);
error_reporting(E_ALL ^ E_NOTICE);
 // error_reporting(0);
//if ($_SERVER['REMOTE_ADDR']=='5.187.71.226' OR $_SERVER['REMOTE_ADDR']=='127.0.0.1') {
   defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev'); 
  //  ini_set('error_reporting', E_ALL);
   
//}



//ini_set('memory_limit', '1024M');
//ini_set('max_execution_time', '300');
require __DIR__ . '/../yii2/vendor/autoload.php';
require __DIR__ . '/../yii2/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../yii2/common/config/bootstrap.php';
require __DIR__ . '/../yii2/frontend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../yii2/common/config/main.php',
   // require __DIR__ . '/../yii2/common/config/main-local.php',
    require __DIR__ . '/../yii2/frontend/config/main.php',
    //require __DIR__ . '/../yii2/frontend/config/main-local.php'
);

if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
  $config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../yii2/common/config/main.php',
    require __DIR__ . '/../yii2/common/config/main-local.php',
    require __DIR__ . '/../yii2/frontend/config/main.php',
    require __DIR__ . '/../yii2/frontend/config/main-local.php'
);  
    
}

(new yii\web\Application($config))->run();
