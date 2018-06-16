<?php

namespace frontend\modules\user\models\forms;

use common\components\storage\Storage;
use frontend\events\UploadedPhotoEvent;
use Yii;
use yii\base\Model;

class PictureForm extends Model
{

    const EVENT_UPLOADED_PHOTO = 'photo_uploaded';

    public $picture;
    public $user;
    protected $fileStorage;

    public function __construct(Storage $fileStorage)
    {
        $this->on(self::EVENT_UPLOADED_PHOTO, [$fileStorage, 'resize']);
        $this->fileStorage = $fileStorage;
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

    public function save(): bool
    {
        if ($this->validate()) {

            $uploadedPhotoEvent = new UploadedPhotoEvent();
            $uploadedPhotoEvent->tempFile = $this->picture->tempName;
            $this->trigger(self::EVENT_UPLOADED_PHOTO, $uploadedPhotoEvent);

            $this->user->picture = $this->fileStorage->saveUploadedFile($this->picture);

            if ($this->user->save(false, ["picture"])) {
                return true;
            }

            return false;

        }

        return false;
    }

    private function getMaxFileSize()
    {
        return Yii::$app->params["maxFileSize"];
    }

}