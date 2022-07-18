<?php

use yii\widgets\ActiveForm;
use app\models\Category;
use kartik\select2\Select2;
use yii\helpers\Url;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */

$categories = Category::forSelectTree();
if ($model->id) unset($categories[$model->id]);
?>

<div class="category-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, "parent_id")->widget(Select2::className(), [
                        'data' => $categories,
                        'options' => [
                            'prompt' => Yii::t('app', 'Виберіть категорію...')
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>

                    <?= $form->field($model, 'image_upload')->widget(FileInput::classname(), [
                        'options' => [
                            'accept' => 'image/*',
                        ],
                        'pluginOptions' => [
                            'initialPreview' => $model->image ? $model->image : false,
                            'showPreview' => false,
                            'showCaption' => false,
                            'showUpload' => false
                        ],
                        'pluginEvents' => Yii::$app->params['FileInputPluginEvents'],
                    ]); ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->render('/blocks/_save_apply_cancel') ?>

    <?php ActiveForm::end(); ?>
</div>
