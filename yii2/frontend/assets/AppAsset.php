<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css?v=2',
    ];
    public $js = [
        //'js/jquery-3.5.1.min.js',
    ];
    public $depends = [
        //'yii\web\YiiAsset',
       // 'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}
