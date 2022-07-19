<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use yii\helpers\Url;
use app\widgets\dynamic_grid_filter\DynamicGridFilterWidget;
use app\models\Category;
use yii\jui\DatePicker;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Категорії');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-list">
    <?= DynaGrid::widget([
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-info',
        'options' => ['id' => 'dynagrid-category'],
        'gridOptions' => [
            'export' => [
                'label' => Yii::t('app', 'Експорт'),
                'target' => ExportMenu::TARGET_SELF,
                'showConfirmAlert' => false
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'panel' => [
                'heading' => "<h3 class='panel-title'><i class='fa fa-arrows-alt'></i> ".Yii::t('app', 'Табиця категорій')."</h3>",
                'before' => Html::a(Yii::t('app', 'Створити Категорію'), ['create'], ['class' => 'btn btn-success']) .
                    Html::a(Yii::t('app', 'Перегляд Дерева'), ['index'], ['class' => 'btn btn-primary', 'style' => 'margin-left:20px;']),
                'after' => false,
                'showFooter' => false
            ],
            'toolbar' => [
                DynamicGridFilterWidget::widget(),
                ['content' => '{dynagrid}'],
                '{export}',
                [
                    'content' => Html::dropDownList('s_id', null, [1 => Yii::t('app', 'Видалити вибрані')], [
                        'prompt' => Yii::t('app', 'Дія на обраному'),
                        'class' => 'form-control',
                        'onchange' => '
                            var type = $(this).val();
                            if(type > 0){
                                var text = "' . Yii::t('app', 'Ви впевнені, що хочете видалити вибрані категорії?') . '";
                              
                                krajeeDialog.confirm(text, function(out){
                                    if(out) $.post("' . Url::to(['category/selected']) . '", {type: type, keys: $("#dynagrid-category>div").yiiGridView("getSelectedRows")});
                                });
                            }
                        ',
                    ])
                ],
            ]
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],

            [
                'class' => 'kartik\grid\SerialColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],

            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '50'],
                'order' => DynaGrid::ORDER_FIX_LEFT,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'hAlign' => GridView::ALIGN_CENTER
            ],

            [
                'attribute' => 'image',
                'filter' => false,
                'content' => function ($data) {
                    return Html::a(Html::img($data['image'], [
                        'style' => 'width:80px;'
                    ]), $data['image'], [
                        'rel' => 'fancybox',
                    ]);
                },
                'headerOptions' => ['width' => '80'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'order' => DynaGrid::ORDER_FIX_LEFT,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'hAlign' => GridView::ALIGN_CENTER
            ],

            [
                'attribute' => 'name',
                'order' => DynaGrid::ORDER_FIX_LEFT,
                'vAlign' => GridView::ALIGN_MIDDLE
            ],

            [
                'attribute' => 'description',
                'headerOptions' => ['width' => '240'],
                'vAlign' => GridView::ALIGN_MIDDLE
            ],

            [
                'attribute' => 'parent_id',
                'filter' => Select2::widget([
                    'name' => 'CategorySearch[parent_id]',
                    'value' => $searchModel->parent_id,
                    'data' => Category::forSelectTree(),
                    'options' => [
                        'prompt' => ''
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]),
                'content' => function ($data) {
                    if ($data['parent_id']) {
                        return Html::a($data['parent']['name'], Url::to(['category/list', 'CategorySearch[id]' => $data['parent']['id']]));
                    } else {
                        return "";
                    }
                },
                'headerOptions' => ['width' => '150'],
                'vAlign' => GridView::ALIGN_MIDDLE
            ],

            [
                'attribute' => 'created_at',
                'filter' => DatePicker::widget([
                    'name' => 'CategorySearch[created_at]',
                    'value' => $searchModel->created_at,
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'format' => 'datetime',
                'headerOptions' => ['width' => '95'],
                'visible' => false,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'hAlign' => GridView::ALIGN_CENTER
            ],

            [
                'attribute' => 'updated_at',
                'filter' => DatePicker::widget([
                    'name' => 'CategorySearch[updated_at]',
                    'value' => $searchModel->updated_at,
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'format' => 'datetime',
                'headerOptions' => ['width' => '95'],
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'hAlign' => GridView::ALIGN_CENTER
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}<br>{create}<br>{delete}',
                'buttons' => [
                    'create' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['create', 'parent_id' => $model->id]), [
                            'title' => Yii::t('yii', 'Створити під-категорію'),
                            'aria-label' => 'Створити під-категорію',
                            'data-pjax' => '0'
                        ]);
                    },

                ],
                'headerOptions' => ['width' => '30'],
                'order' => DynaGrid::ORDER_FIX_RIGHT
            ]
        ]
    ]); ?>
</div>

<?php $this->registerJsFile('/admin/js/modal.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>
