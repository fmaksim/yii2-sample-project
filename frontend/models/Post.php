<?php

namespace frontend\models;

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

    private $likesStorage;

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

    public function setLikesStorage(Component $storage)
    {
        $this->likesStorage = $storage;
    }

    public function doLike(User $user)
    {

        if (!$this->isSetLikesStorage())
            return false;

        $this->likesStorage->sadd("post:{$this->getId()}:likes", $user->getId());
        $this->likesStorage->sadd("user::{$user->getId()}likes", $this->getId());

        return true;
    }

    private function isSetLikesStorage()
    {
        return $this->likesStorage ? true : false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function doUnlike(User $user)
    {
        if (!$this->isSetLikesStorage())
            return false;

        $this->likesStorage->srem("post:{$this->getId()}:likes", $user->getId());
        $this->likesStorage->srem("user::{$user->getId()}likes", $this->getId());

        return true;
    }

    public function countLikes()
    {
        if (!$this->isSetLikesStorage())
            return false;

        return $this->likesStorage->scard("post:{$this->getId()}:likes");
    }

    public function isLikedBy(User $user)
    {
        if (!$this->isSetLikesStorage())
            return false;

        return $this->likesStorage->sismember("post:{$this->getId()}:likes", $user->getId()) ? true : false;
    }

    public function getImage()
    {
        return Yii::$app->fileStorage->getFile($this->filename);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
