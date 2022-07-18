<?php

namespace app\widgets\dynamic_grid_filter;

use yii\base\Widget;

class DynamicGridFilterWidget extends Widget
{
    public function run(){
        return $this->render('dynamic-grid-filter');
    }
}