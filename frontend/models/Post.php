<?php

namespace frontend\models;

use frontend\components\LikeStorage;
use frontend\components\Storage;
use frontend\services\LikeService;
use Yii;
use frontend\models\User;
use yii\base\Component;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $description
 * @property string $filename
 * @property int $user_id
 * @property int $created_at
 */
class Post extends \yii\db\ActiveRecord
{

    protected $likeService;
    protected $fileStorage;

    public function __construct(LikeService $likeService, Storage $fileStorage, array $config = [])
    {
        $this->likeService = $likeService;
        $this->likeService->setType("post");
        $this->fileStorage = $fileStorage;

        parent::__construct($config);
    }

    public static function instantiate($row)
    {
        return \Yii::$container->get(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'filename' => 'Filename',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCountLikes()
    {
        return $this->likeService->getCount($this);
    }

    public function getImage()
    {
        return $this->fileStorage->getFile($this->filename);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function isLiked()
    {
        return $this->likeService->isLiked($this);
    }

}
