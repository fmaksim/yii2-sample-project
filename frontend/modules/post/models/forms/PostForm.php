<?php

namespace frontend\modules\post\models\forms;

use frontend\events\PostAddedEvent;
use Yii;
use yii\base\Model;
use frontend\models\User;
use frontend\components\storage\StorageInterface;
use frontend\models\Post;

class PostForm extends Model
{
    const MAX_DESCRIPTION_LENGTH = 1000;
    const EVENT_NEW_POST = 'newPost';

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
        $this->on(self::EVENT_NEW_POST, [$this->storageService, 'resize']);
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

    public function save()
    {

        if ($this->validate()) {

            $event = new PostAddedEvent();
            $event->tempFile = $this->filename->tempName;
            $this->trigger(self::EVENT_NEW_POST, $event);

            $this->post->filename = $this->storageService->saveUploadedFile($this->filename);
            $this->post->description = $this->description;
            $this->post->user_id = $this->user->getId();

            if ($this->post->save(false)) {
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