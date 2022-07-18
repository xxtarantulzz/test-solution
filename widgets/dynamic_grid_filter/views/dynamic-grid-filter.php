<?php

use yii\helpers\Html;
use app\widgets\dynamic_grid_filter\DynamicGridFilterAsset;

DynamicGridFilterAsset::register($this);

?>

<div class='dynamic_grid_filter_columns btn-group'>
    <?= Html::button('<i class="glyphicon glyphicon-wrench"></i> ' . Yii::t('app', 'Фільтр'), [
        'id' => 'dynamic_grid_filter_columns',
        'class' => 'btn btn-info'
    ]); ?>

    <div class='form'>
        <ul class="list-group"></ul>

        <?= Html::button(Yii::t('app', 'Зберегти'), [
            'id' => 'dynamic_grid_filter_columns_save',
            'class' => 'btn btn-success'
        ]); ?>
    </div>
</div>

