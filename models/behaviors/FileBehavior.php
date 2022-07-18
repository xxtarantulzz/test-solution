<?php

namespace app\models\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\image\drivers\Image;

class FileBehavior extends Behavior
{
    public $options;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
        ];
    }

    static public function saveFile($path, $file, $dir, $options)
    {
        $user_id = self::getUserId();

        $name = $options['name'];
        $type = $options['type'];

        if (isset($options['directory']) && $options['directory']) {
            BaseFileHelper::createDirectory(Yii::getAlias('@app/web') . '/upload/' . $options['directory']);
        } else {
            BaseFileHelper::createDirectory(Yii::getAlias('@app/web') . '/upload/' . $dir . '/' . $user_id);
        }
        $file->saveAs(Yii::getAlias('@app/web') . $path, true);

        if ($type == 'image') {
            $image = Yii::$app->image->load(Yii::getAlias('@app/web') . $path);

            if ($image->mime == 'image/jpeg') {
                if (function_exists("exif_read_data")) {
                    $fp = fopen(Yii::getAlias('@app/web') . $path, 'rb');
                    if ($fp) {
                        $exif = @exif_read_data(Yii::getAlias('@app/web') . $path);
                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 3:
                                    $image->rotate(180);
                                    break;
                                case 6:
                                    $image->rotate(90);
                                    break;
                                case 8:
                                    $image->rotate(-90);
                                    break;
                            }

                            $image->save(Yii::getAlias('@app/web') . $path, 100);
                        }
                    }
                }
            }

            //создадим миниатюру
            if (isset($options['directory']) && $options['directory']) {
                BaseFileHelper::createDirectory(Yii::getAlias('@app/web') . '/upload/' . $options['directory'] . "/mini");
            } else {
                BaseFileHelper::createDirectory(Yii::getAlias('@app/web') . '/upload/' . $dir . '/' . $user_id . "/mini");
            }

            if (isset($options['width']) || isset($options['height'])) {
                $tmp_array = explode('/', $path);
                $name = $tmp_array[count($tmp_array) - 1];
                if (isset($options['width']) && isset($options['height'])) {
                    $image->resize($options['width'], $options['height'], Image::CROP);
                } elseif (isset($options['width'])) {
                    $image->resize($options['width'], NULL, Image::WIDTH, 100);
                } else {
                    $image->resize($options['height'], NULL, Image::HEIGHT, 100);
                }
            } else {
                $image->resize(200, NULL, Image::WIDTH, 100);
            }

            if (isset($options['directory']) && $options['directory']) {
                $image->save(Yii::getAlias('@app/web') . '/upload/' . $options['directory'] . "/mini/" . $name, 100);
            } else {
                $image->save(Yii::getAlias('@app/web') . '/upload/' . $dir . '/' . $user_id . "/mini/" . $name, 100);
            }
        }
    }

    public function saveUploads()
    {
        foreach ($this->options as $key => $value) {
            if (!isset($value['skip']) || !$value['skip']) {
                $name = $value['name'];

                if ($this->owner->$name) {
                    if (isset($value['multiple']) && $value['multiple']) {
                        if (count($this->owner->$key)) {
                            $names = explode(', ', $this->owner->$name);
                            foreach ($this->owner->$key as $key2 => $value2) {
                                if (!file_exists(Yii::getAlias('@app/web') . $names[$key2])) {
                                    self::saveFile($names[$key2], $value2, $this->getDirName(), $value);
                                }
                            }
                        }
                    } else {
                        if ($this->owner->$key) {
                            if (!file_exists(Yii::getAlias('@app/web') . $this->owner->$name)) {
                                self::saveFile($this->owner->$name, $this->owner->$key, $this->getDirName(), $value);
                            }
                        }
                    }
                }
            }
        }
    }

    public function beforeValidate()
    {
        $user_id = self::getUserId();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $modelClass = $this->owner;
            $name = str_replace("\\", "/", $modelClass::className());
            $array = explode('/', $name);
            $class = $array[count($array) - 1];

            foreach ($this->options as $key => $value) {
                if (!isset($value['skip']) || !$value['skip']) {
                    $name = $value['name'];
                    $field_name = str_replace('_upload', '', $name);

                    if (isset($post[$class][$key])) {
                        //удаление фото
                        $delete = false;
                        if (isset($value['multiple']) && $value['multiple']) {
                            if (isset($post[$class][$key][0]) && $post[$class][$key][0] == 'delete') {
                                $this->owner->$name = null;
                                $delete = true;
                            }
                        } else {
                            if ($post[$class][$key] == 'delete') {
                                $this->owner->$name = null;
                                $delete = true;
                            }
                        }

                        if (!$delete) {
                            //добавление фото
                            $this->owner->$key = null;
                            if (isset($value['multiple']) && $value['multiple']) {
                                $this->owner->$key = UploadedFile::getInstances($this->owner, $key);

                                if (count($this->owner->$key)) {
                                    $files = '';
                                    foreach ($this->owner->$key as $tmp) {
                                        $name_file = self::getGenerateNameFile($tmp);
                                        if (isset($value['directory']) && $value['directory']) {
                                            $path = '/upload/' . $value['directory'] . "/" . $name_file;
                                        } else {
                                            $path = '/upload/' . $this->getDirName() . '/' . $user_id . "/" . $name_file;
                                        }
                                        if ($files != '') $files = $files . ", ";
                                        $files = $files . $path;
                                    }
                                    $this->owner->$name = $files;
                                }
                            } else {
                                $this->owner->$key = UploadedFile::getInstance($this->owner, $key);

                                if ($this->owner->$key) {
                                    $files = '';
                                    $name_file = self::getGenerateNameFile($this->owner->$key);

                                    if (isset($value['directory']) && $value['directory']) {
                                        $path = '/upload/' . $value['directory'] . "/" . $name_file;
                                    } else {
                                        $path = '/upload/' . $this->getDirName() . '/' . $user_id . "/" . $name_file;
                                    }
                                    if ($files != '') $files = $files . ", ";
                                    $files = $files . $path;

                                    $this->owner->$name = $files;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    static public function getUserId()
    {
        $user_id = 0;
        if (isset(Yii::$app->user)) {
            if (!Yii::$app->user->getIsGuest()) {
                $user_id = Yii::$app->user->getId();
            }
        }

        return $user_id;
    }

    //отдельный для аякс загрузки
    static public function beforeSaveFile($name_model_lower, $file, $options)
    {
        $user_id = self::getUserId();

        $name_file = $file->size . "_" . time() . '.' . $file->extension;
        self::getGenerateNameFile($file);

        if (isset($options['directory']) && $options['directory']) {
            $path = '/upload/' . $options['directory'] . "/" . $name_file;
            $dir = $options['directory'];
        } else {
            $path = '/upload/' . $name_model_lower . '/' . $user_id . "/" . $name_file;
            $dir = $name_model_lower;
        }

        if (!file_exists(Yii::getAlias('@app/web') . $path)) {
            self::saveFile($path, $file, $dir, $options);
        }

        return $path;
    }

    protected function getDirName()
    {
        $modelClass = $this->owner;
        $name = str_replace("\\", "/", $modelClass::className());
        $array = explode('/', $name);

        return strtolower($array[count($array) - 1]);
    }

    static public function getGenerateNameFile($file)
    {
        return $file->size . "_" . time() . '.' . $file->extension;
    }

    static public function deleteFilesInPost($model, $post)
    {
        $modelClass = $model;
        $name = str_replace("\\", "/", $modelClass::className());
        $array = explode('/', $name);
        $name = $array[count($array) - 1];

        foreach ($post[$name] as $key => $value) {
            if (strpos($key, '_upload') !== false) {
                if (is_array($value)) {
                    if ($value[0]) {
                        $new_key = str_replace('_upload', '', $key);
                        $post[$name][$new_key] = $value[0];
                        unset($post[$name][$key]);
                    }
                } else {
                    if ($value) {
                        $new_key = str_replace('_upload', '', $key);
                        $post[$name][$new_key] = $value;
                        unset($post[$name][$key]);
                    }
                }
            }
        }

        return $post;
    }
}