<?php

namespace frontend\models;

use frontend\components\storage\Storage;
use frontend\components\LikeService;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $description
 * @property string $filename
 * @property int $user_id
 * @property int $complaints
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

    public function getId(): int
    {
        return $this->id;
    }

    public function isLiked(): bool
    {
        return $this->likeService->isLiked($this->getId());
    }

    public function getCountLikes(): int
    {
        return $this->likeService->getCount($this->getId());
    }

    public function getImage(): string
    {
        return $this->fileStorage->getFile($this->filename);
    }

    public function incComplaints()
    {
        $this->complaints++;
    }

    public static function instantiate($row)
    {
        return \Yii::$container->get(static::class);
    }

    public static function tableName()
    {
        return 'post';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

}