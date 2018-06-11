<?php

namespace frontend\components;

use frontend\components\storage\RedisStorage;
use yii\base\Component;
use yii\base\InvalidCallException;
use Yii;

/**
 * Class LikeService -the component provides a unified "like-dislike" mechanism for different types of objects
 * @package frontend\components
 * @property \frontend\components\storage\RedisStorage $storage
 * @property string $type post|comment
 * @property int $userId
 */
class LikeService extends Component
{

    const AVAILABLE_TYPES = ["post", "comment"];

    protected $storage;
    protected $type;
    protected $userId;

    public function __construct(RedisStorage $storage, $config = [])
    {
        $this->storage = $storage->getStorage();
        $this->userId = Yii::$app->user->identity ? Yii::$app->user->identity->getId() : null;
        parent::__construct($config);
    }

    public function setType(string $type): bool
    {
        if (in_array($type, self::AVAILABLE_TYPES)) {
            $this->type = $type;
            return true;
        }

        throw new InvalidCallException("Incorrect type!");
    }

    public function toggleLike($likebleObject): bool
    {
        if ($this->isTypeNotExist()) {
            return false;
        }

        if ($this->isLiked($likebleObject->getId())) {
            return $this->unlike($likebleObject);
        }

        return $this->like($likebleObject);

    }

    public function getCount(int $likebleObjectId): int
    {
        return $this->storage->scard("{$this->type}:{$likebleObjectId}:likes");
    }

    public function isLiked(int $likebleObjectId): bool
    {
        return $this->storage->sismember("{$this->type}:{$likebleObjectId}:likes", $this->userId) ?
            true :
            false;
    }

    private function isTypeNotExist(): bool
    {
        return $this->type ? false : true;
    }

    private function unlike($likebleObject): bool
    {
        try {
            $this->storage->srem("{$this->type}:{$likebleObject->getId()}:likes", $this->userId);
            $this->storage->srem("user::{$this->userId}likes", $likebleObject->getId());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function like($likebleObject): bool
    {
        try {
            $this->storage->sadd("{$this->type}:{$likebleObject->getId()}:likes", $this->userId);
            $this->storage->sadd("user::{$this->userId}likes", $likebleObject->getId());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}