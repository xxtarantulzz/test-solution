<?php
namespace app\widgets\dynamic_grid_filter;

use yii\web\AssetBundle;

class DynamicGridFilterAsset extends AssetBundle
{
    public $css = [
        'css/dynamic_grid_filter.css',
    ];

    public $js = [
        'js/dynamic_grid_filter.js',
        'js/jquery.ui.touch-punch.min.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__."/asset";
        parent::init();
    }

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}