<?php

namespace app\controllers;

use app\models\behaviors\FileBehavior;
use app\models\Category;
use app\models\search\CategorySearch;
use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'delete', 'selected', 'list', 'ajax-change-name'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'selected' => ['POST'],
                    'ajax-change-name' => ['POST']
                ],
            ],
        ];
    }

    public function actionAjaxChangeName()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            if(isset($post['id']) && isset($post['name'])){
                $category = Category::findOne($post['id']);
                if($category){
                    $category->name = $post['name'];
                    $category->save(false);
                }
            }

        }
    }

    public function actionList()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }


    public function actionIndex($id = 1)
    {
        $model = new Category();
        $formName = $model->formName();

        return $this->render('index', [
            'categories' => Category::getTree(),
            'id' => $id,
            'formName' => $formName,
        ]);
    }

    public function actionCreate($parent_id = null)
    {
        $model = new Category();
        $model->parent_id = $parent_id;

        if (Yii::$app->request->isPost) {
            $post = FileBehavior::deleteFilesInPost($model, Yii::$app->request->post());
            if (!Yii::$app->request->isAjax) {
                if ($model->load($post)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if ($model->save()) {
                        if (!$model->hasErrors()) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Категорія була створена.'));
                            if (isset($_POST['apply'])) return $this->redirect(['update', 'id' => $model->id]);
                            return $this->redirect(['index']);
                        }
                    }

                    $transaction->rollback();
                }
            } else {
                //если аяксом идет сохранение
                if ($model->load($post) && $model->save()) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $post = FileBehavior::deleteFilesInPost($model, Yii::$app->request->post());
            if (!Yii::$app->request->isAjax) {
                if ($model->load($post)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if ($model->save()) {
                        if (!$model->hasErrors()) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Категорія була оновлена.'));
                            if (isset($_POST['apply'])) return $this->redirect(['update', 'id' => $model->id]);
                            return $this->redirect(['index']);
                        }
                    }

                    $transaction->rollback();
                }
            } else {
                //если аяксом идет сохранение
                if ($model->load($post) && $model->save()) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Категорія була видалена.'));
        return $this->redirect(['index']);
    }

    public function actionSelected()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            if (!empty($post['keys'])) {
                for ($i = 0; $i < count($post['keys']); $i = $i + 1) {
                    $model = $this->findModel($post['keys'][$i]);
                    if ($model) $model->delete();
                }
            }
        }

        return $this->redirect(['list']);
    }

    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запитувана сторінка не існує.');
        }
    }
}
