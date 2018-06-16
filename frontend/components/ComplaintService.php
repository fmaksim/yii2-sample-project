<?php

namespace frontend\components;

use common\components\storage\RedisStorage;
use frontend\models\Post;
use yii\base\Component;

/**
 * Class ComplaintService - components provide reporting a complain for the post
 * @package frontend\components
 * @property \common\components\storage\RedisStorage $redisStorage
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

    public function approveComplaint(\backend\models\Post $post): bool
    {
        $post->setComplaints(0);
        if ($post->save(false, ["complaints"])) {
            $key = "post:{$post->id}:complaints";
            $this->redisStorage->del($key);
            return true;
        }

        return false;
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