<?php

namespace frontend\modules\post\models\forms;

use Yii;
use yii\base\Model;
use frontend\models\User;
use frontend\components\storage\StorageInterface;
use frontend\models\Post;

class PostForm extends Model
{
    const MAX_DESCRIPTION_LENGTH = 1000;

    public $description;
    public $filename;

    private $user;
    private $storageService;
    private $post;

    public function __construct(User $user, StorageInterface $storage, Post $post)
    {
        $this->user = $user;
        $this->storageService = $storage;
        $this->post = $post;
    }

    public function rules()
    {
        return [
            ["description", "string", "max" => self::MAX_DESCRIPTION_LENGTH],
            ["filename", "file",
                "skipOnEmpty" => false,
                "extensions" => "png, jpg, jpeg",
                "maxSize" => $this->getMaxFileSize(),
                "checkExtensionByMimeType" => true
            ]
        ];
    }

    private function getMaxFileSize()
    {
        return Yii::$app->params["maxFileSize"];
    }

    public function save()
    {

        if ($this->validate()) {
            $this->post->filename = $this->storageService->saveUploadedFile($this->filename);
            $this->post->description = $this->description;
            $this->post->user_id = $this->user->getId();
            $this->post->created_at = time();

            if ($this->post->save(false)) {
                return true;
            }

            return false;
        }

        return false;

    }


}