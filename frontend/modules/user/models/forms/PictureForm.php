<?php

namespace frontend\modules\user\models\forms;

use Yii;
use yii\base\Model;
use Intervention\Image\ImageManager;

class PictureForm extends Model
{

    public $picture;

    public function __construct()
    {
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, "resize"]);
    }

    public function rules()
    {
        return [
            [['picture'], 'file',
                'extensions' => 'png, jpg, jpeg',
                'maxSize' => $this->getMaxFileSize(),
                'checkExtensionByMimeType' => true
            ],
        ];
    }

    public function resize()
    {

        $width = Yii::$app->params['imgSize']['maxWidth'];
        $height = Yii::$app->params['imgSize']['maxHeight'];

        $manager = new ImageManager(array('driver' => 'imagick'));
        $image = $manager->make($this->picture->tempName);

        $image->resize($width, $height, function ($constraint) {

            $constraint->aspectRatio();

            $constraint->upsize();

        })->save();

    }

    private function getMaxFileSize()
    {
        return Yii::$app->params["maxFileSize"];
    }

}