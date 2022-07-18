<?php
/* @var $this yii\web\View */
/* @var $model app\models\Category */

if($model->parent_id){
    $this->title = 'Створення під-категорії: '. $model->parent->name;
}else{
    $this->title = 'Створення категорії';
}

$this->params['breadcrumbs'][] = ['label' => 'Категорії', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="category-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
