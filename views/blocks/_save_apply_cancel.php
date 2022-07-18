<?php

use yii\helpers\Html;

/* @var $form_id integer || null */

?>

<div class="form-group">
    <?php if (isset($form_id)): ?>
        <?= Html::submitButton(Yii::t('app', 'Зберегти'), [
            'class' => 'btn btn-success',
            'form' => $form_id
        ]); ?>

        <?php /* Html::submitButton(Yii::t('app', 'Застосовувати'), [
            'class' => 'btn btn-primary',
            'name' => 'apply',
            'value' => 'apply',
            'form' => $form_id,
            'target' => 'apply'
        ]);*/ ?>
    <?php else: ?>
        <?= Html::submitButton(Yii::t('app', 'Зберегти'), ['class' => 'btn btn-success']); ?>
        <?php /* Html::submitButton(Yii::t('app', 'Застосовувати'), ['class' => 'btn btn-primary', 'name' => 'apply']) */?>
    <?php endif; ?>

    <?= Html::a(Yii::t('app', 'Скасувати'), ['index'], ['class' => 'btn btn-default']) ?>

    <div class="clearfix"></div>
</div>