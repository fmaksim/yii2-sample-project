<?php

namespace frontend\components;

use frontend\components\storage\LikeStorage;
use yii\base\Component;
use yii\base\InvalidCallException;
use Yii;

class LikeService extends Component
{

    const AVAILABLE_TYPES = ["post", "comment"];

    protected $storage;
    protected $type;
    protected $userId;

    public function __construct(LikeStorage $storage, $config = [])
    {
        $this->storage = $storage->getStorage();
        $this->userId = Yii::$app->user->identity ? Yii::$app->user->identity->getId() : null;
        parent::__construct($config);
    }

    public function setType($type): bool
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

        if ($this->isLiked($likebleObject)) {
            return $this->unlike($likebleObject);
        }

        return $this->like($likebleObject);

    }

    private function isTypeNotExist(): bool
    {
        return $this->type ? false : true;
    }

    public function isLiked($likebleObject): bool
    {
        return $this->storage->sismember("{$this->type}:{$likebleObject->getId()}:likes", $this->userId) ?
            true :
            false;
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

    public function getCount($likebleObject): int
    {
        return $this->storage->scard("{$this->type}:{$likebleObject->getId()}:likes");
    }

}