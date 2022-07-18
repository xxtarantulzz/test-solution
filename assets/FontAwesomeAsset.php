<?php
namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'lib/font-awesome/css/font-awesome.min.css',
    ];

    public $css = [
        'lib/font-awesome/css/font-awesome.min.css',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}