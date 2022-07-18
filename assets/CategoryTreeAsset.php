<?php
namespace app\assets;

use yii\web\AssetBundle;

class CategoryTreeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'lib/tree/js/jquery.mjs.nestedSortable.js',
        'lib/tree/js/tree.js',
    ];

    public $css = [
        'lib/tree/css/tree.css',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}