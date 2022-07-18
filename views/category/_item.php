<?php

/**
 * @var $this yii\web\View
 * @var $category app\models\Category
 */

use yii\helpers\Html;
use yii\helpers\Url;

$hasChildren = isset($category['children']);
?>

<li class="category-list-item"
    data-id="<?= $category['id'] ?>"
    data-sort="<?= $category['sort_order'] ?>"
    data-update-url="<?= Url::to(['update', 'id' => $category['id']]) ?>">

    <div>
        <a class="btn btn-xs btn-primary drag-btn" title="<?= Yii::t('app', 'Перемістити'); ?>">
            <i class="fa fa-arrows"></i>
        </a>

        <span><?= $category['name'] ?></span>

        <?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $category['id']], [
            'class' => 'btn btn-xs btn-primary',
            'title' => Yii::t('app', 'Редагувати')
        ]) ?>

        <?= Html::a('<i class="fa fa-plus"></i>', ['create', 'parent_id' => $category['id']], [
            'class' => 'btn btn-xs btn-success',
            'title' => Yii::t('app', 'Добавити під-категорію')
        ]) ?>

        <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $category['id']], [
            'data' => ['confirm' => Yii::t('app', 'Ви впевнені, що хочете видалити цей елемент?'), 'method' => 'post',],
            'class' => 'btn btn-xs btn-danger',
            'title' => Yii::t('app', 'Видалити')
        ]) ?>

        <a href="#categoryList<?= $category['id'] ?>"
           class="btn btn-xs btn-primary collapse-list-btn <?= $hasChildren ? '' : 'disabled' ?>"
           role="button"
           data-toggle="collapse"
           title="<?= $hasChildren ? Yii::t('app', 'Розкрити') : Yii::t('app', 'Сховати'); ?>"
           aria-expanded="false" aria-controls="categoryList<?= $category['id'] ?>">
            <i class="fa <?= $hasChildren ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
        </a>
    </div>

    <ol id="categoryList<?= $category['id'] ?>" class="collapse category-list <?= $hasChildren ? 'in' : '' ?>">
        <?php if ($hasChildren): ?>
            <?php foreach ($category['children'] as $child): ?>
                <?= $this->render('_item', [
                    'category' => $child
                ]) ?>
            <?php endforeach; ?>
        <?php endif ?>
    </ol>
</li>
