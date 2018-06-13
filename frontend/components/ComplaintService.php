<?php

namespace frontend\components;

use frontend\components\storage\RedisStorage;
use frontend\models\Feed;
use frontend\models\Post;
use yii\base\Component;
use Yii;
use yii\base\Event;

/**
 * Class ComplaintService - components provide reporting a complain for the post
 * @package frontend\components
 * @property \frontend\components\RedisStorage $redisStorage
 */
class ComplaintService extends Component
{

    protected $redisStorage;

    public function __construct(RedisStorage $redisStorage, $config = [])
    {
        $this->redisStorage = $redisStorage->getStorage();
        parent::__construct($config);
    }

    public function complain(Post $post, int $userId): bool
    {

        if (!$this->isComplain($post->getId(), $userId)) {
            return $this->add($post, $userId);
        }

        return false;

    }

    public function isComplain(int $postId, int $userId): bool
    {
        $key = $this->getStoreKey($postId);
        return $this->redisStorage->sismember($key, $userId);
    }

    private function getStoreKey(int $postId)
    {
        return "post:{$postId}:complaints";
    }

    private function add(Post $post, int $userId): bool
    {
        $key = $this->getStoreKey($post->getId());
        $this->redisStorage->sadd($key, $userId);

        $post->incComplaints();
        return $post->save(false, ['complaints']);

    }

}