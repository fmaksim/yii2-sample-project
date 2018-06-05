<?php

namespace frontend\modules\post\models\forms;

use frontend\components\FeedService;
use frontend\events\PostCreatedEvent;
use frontend\events\UploadedPhotoEvent;
use Yii;
use yii\base\Model;
use frontend\models\User;
use frontend\components\storage\StorageInterface;
use frontend\models\Post;

class PostForm extends Model
{
    const MAX_DESCRIPTION_LENGTH = 1000;
    const EVENT_UPLOADED_PHOTO = 'photo_uploaded';
    const EVENT_CREATED_POST = 'post_created';

    public $description;
    public $filename;

    private $user;
    private $storageService;
    private $feedService;
    private $post;

    public function __construct(User $user, StorageInterface $storage, Post $post, FeedService $feedService)
    {
        $this->user = $user;
        $this->storageService = $storage;
        $this->post = $post;
        $this->feedService = $feedService;

        $this->on(self::EVENT_UPLOADED_PHOTO, [$this->storageService, 'resize']);
        $this->on(self::EVENT_CREATED_POST, [$this->feedService, 'addToFeed']);
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

            $uploadedPhotoEvent = new UploadedPhotoEvent();
            $uploadedPhotoEvent->tempFile = $this->filename->tempName;
            $this->trigger(self::EVENT_UPLOADED_PHOTO, $uploadedPhotoEvent);

            $this->post->filename = $this->storageService->saveUploadedFile($this->filename);
            $this->post->description = $this->description;
            $this->post->user_id = $this->user->getId();

            if ($this->post->save(false)) {
                $postCreatedEvent = new PostCreatedEvent();
                $postCreatedEvent->user = $this->user;
                $postCreatedEvent->post = $this->post;

                $this->trigger(self::EVENT_CREATED_POST, $postCreatedEvent);
                return true;
            }
        }

        return false;

    }

    private function getMaxFileSize()
    {
        return Yii::$app->params["maxFileSize"];
    }

}