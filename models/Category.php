<?php

namespace app\models;

use app\models\behaviors\FileBehavior;
use app\models\query\CategoryQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $parent_id
 * @property integer $sort_order
 * @property string $description
 *
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Category $parent
 * @property Category[] $children
 *
 *
 */
class Category extends ActiveRecord
{
    public $image_upload;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'safe'],
            [['parent_id', 'created_at', 'updated_at', 'sort_order'], 'integer'],
            [['name', 'image'], 'string', 'max' => 255],
            ['sort_order', 'default', 'value' => 0],

            [['name'], 'unique'],

            [['image_upload'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp'],

            //[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Ім\'я'),
            'image' => Yii::t('app', 'Зображення'),
            'image_upload' => Yii::t('app', 'Зображення'),
            'parent_id' => Yii::t('app', 'Батьківська категорія'),
            'sort_order' => Yii::t('app', 'Сортування'),
            'description' => Yii::t('app', 'Опис'),

            'created_at' => Yii::t('app', 'Створена'),
            'updated_at' => Yii::t('app', 'Оновлена'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => FileBehavior::className(),
                'options' => [
                    'image_upload' => [
                        'name' => 'image',
                        'type' => 'image',
                        'width' => 200,
                        'height' => 200
                    ],
                ]
            ],
        ];
    }

    public function afterFind()
    {
        if (!$this->image) $this->image = '/img/no_image.png';

        return parent::afterFind();
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id'])->from(Category::tableName() . ' parent');
    }

    /**
     * @return ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id'])->from(Category::tableName() . ' categories');
    }


    static function getAllNamesCategory(){
        $categories = ArrayHelper::map(
            Category::find()->select([
                'id', 'name'
            ])->orderBy(['name' => SORT_ASC])->asArray()->all(),
            'id',
            'name'
        );

        return $categories;
    }


    /**
     * @param $options array
     * @return mixed
     */
    public static function getTreeForMap($options = [])
    {
        $depth = ArrayHelper::remove($options, 'depth', -1);
        $filter = ArrayHelper::remove($options, 'filter', function ($item) {
            return true;
        });

        $list = self::find()->all();
        $list = ArrayHelper::remove($options, 'list', $list);

        $getChildren = function ($id, $depth) use ($list, &$getChildren, $filter) {
            $result = [];
            foreach ($list as $item) {
                if ((int)$item['parent_id'] === (int)$id) {
                    $r = [
                        'name' => $item['name'],
                        'id' => $item['id'],
                        'sort_order' => $item['sort_order'],
                        'image' => $item['image'],
                    ];
                    $c = $depth ? $getChildren($item['id'], $depth - 1) : null;
                    if (!empty($c)) {
                        $r['children'] = $c;
                    }
                    if ($filter($r)) {
                        $result[] = $r;
                    }
                }
            }

            usort($result, function ($a, $b) {
                return $a['sort_order'] > $b['sort_order'];
            });

            return $result;

        };

        return $getChildren(0, $depth);
    }

    /**
     * @param $options array
     * @return mixed
     */
    public static function getTree($options = [])
    {
        $depth = ArrayHelper::remove($options, 'depth', -1);
        $filter = ArrayHelper::remove($options, 'filter', function ($item) {
            return true;
        });

        $list = self::find()->all();
        $list = ArrayHelper::remove($options, 'list', $list);

        $getChildren = function ($id, $depth) use ($list, &$getChildren, $filter) {
            $result = [];
            foreach ($list as $item) {
                if ((int)$item['parent_id'] === (int)$id) {
                    $r = [
                        'name' => $item['name'],
                        'id' => $item['id'],
                        'sort_order' => $item['sort_order'],
                        'image' => $item['image'],
                    ];
                    $c = $depth ? $getChildren($item['id'], $depth - 1) : null;
                    if (!empty($c)) {
                        $r['children'] = $c;
                    }
                    if ($filter($r)) {
                        $result[] = $r;
                    }
                }
            }

            usort($result, function ($a, $b) {
                return $a['sort_order'] > $b['sort_order'];
            });

            return $result;

        };

        return $getChildren(0, $depth);
    }

    /**
     * @param $id integer
     * @param $level integer
     * @param $prefix string
     * @return mixed
     */
    public static function forSelectTree($id = null, $level = 1, $prefix = '-')
    {
        $return = [];
        $prefix = str_repeat($prefix, $level);
        if ($level > 0) $prefix .= ' ';
        $level++;

        $categories = Category::find()->where(['parent_id' => $id])->orderBy(['sort_order' => SORT_ASC])->all();

        foreach ($categories as $category) {
            $return[$category->id] = "$prefix{$category->name}";
            $return = $return + self::forSelectTree($category->id, $level);
        }

        return $return;
    }

    /**
     * @param $id integer
     * @return mixed
     */
    public static function forSelectGroup($id = null)
    {
        $return = [];
        $categories = Category::find()->where(['parent_id' => $id])->orderBy(['sort_order' => SORT_ASC])->all();

        foreach ($categories as $category) {
            $child = self::forSelectGroup($category->id);
            if(is_array($child) && count($child)){
                $return[$category->name] = self::forSelectGroup($category->id);
            }else{
                $return[$category->id] = $category->name;
            }

        }

        return $return;
    }


    /**
     * @param $id integer
     * @param $result array
     * @return mixed
     */
    public static function getParentsCategoriesIds($id, $result = [])
    {
        $category = Category::findOne($id);
        if ($category && $category->parent_id) {
            $result = Category::getParentsCategoriesIds($category->parent_id, $result);
        }

        $result[] = $id;
        return $result;
    }

    /**
     * @param $id integer
     * @param $result array
     * @return mixed
     */
    public static function getChildCategoriesIds($id, $result = [])
    {
        $categories = Category::findAll(['parent_id' => $id]);
        foreach ($categories as $category) {
            $result = Category::getChildCategoriesIds($category->id, $result);
        }

        $result[] = $id;
        return $result;
    }

    /**
     * @param $id integer
     * @param $result Category[]
     * @return mixed
     */
    public static function getParentsCategories($id, $result = [])
    {
        $category = Category::findOne($id);
        if ($category && $category->parent_id) {
            $result = Category::getParentsCategories($category->parent_id, $result);
        }

        $result[] = $category;
        return $result;
    }

    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}
