<?php

namespace frontend\components;

use common\components\storage\RedisStorage;
use frontend\models\User;
use yii\base\Component;
use Yii;

/**
 * Class SubscriptionService - component implements the subscription mechanism for users
 * @package frontend\components
 * @property \common\components\storage\RedisStorage $storage
 * @property \frontend\models\User $currentUser
 */
class SubscriptionService extends Component
{

    protected $storage;
    protected $currentUser;

    public function __construct(RedisStorage $storage, $config = [])
    {
        $this->storage = $storage->getStorage();
        $this->currentUser = Yii::$app->user->identity ? Yii::$app->user->identity : null;
        parent::__construct($config);
    }

    public function toggleSubscribe(User $followedUser): bool
    {

        if ($this->isFollowed($followedUser)) {
            return $this->unFollowUser($followedUser);
        }

        return $this->followUser($followedUser);
    }

    public function isFollowed(User $followedUser): bool
    {
        $ids = $this->storage->smembers("user:{$followedUser->getId()}:followers");
        return in_array($this->currentUser->getId(), $ids) ? true : false;
    }

    private function unFollowUser(User $followedUser): bool
    {
        $this->storage->srem("user:{$followedUser->getId()}:followers", $this->currentUser->getId());
        $this->storage->srem("user:{$this->currentUser->getId()}:subscriptions", $followedUser->getId());

        return true;
    }

    private function followUser(User $followedUser): bool
    {
        $this->storage->sadd("user:" . $followedUser->getId() . ":followers", $this->currentUser->getId());
        $this->storage->sadd("user:" . $this->currentUser->getId() . ":subscriptions", $followedUser->getId());

        return true;
    }

}