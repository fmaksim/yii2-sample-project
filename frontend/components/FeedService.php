<?php

namespace frontend\components;

use frontend\components\storage\RedisStorage;
use frontend\models\Feed;
use yii\base\Component;
use Yii;
use yii\base\Event;

/**
 * Class FeedService - components provide storing method for handling the post_created event
 * @package frontend\components
 * @property \frontend\components\storage\RedisStorage $storage
 */
class FeedService extends Component
{

    protected $storage;

    public function __construct(RedisStorage $storage, $config = [])
    {
        $this->storage = $storage->getStorage();
        parent::__construct($config);
    }

    public function addToFeed(Event $event)
    {

        $user = $event->getUser();
        $post = $event->getPost();

        $followers = $user->getFollowers();

        if ($followers) {
            foreach ($followers as $follower) {

                $feed = new Feed();
                $feed->user_id = $follower['id'];
                $feed->author_id = $user->id;
                $feed->author_name = $user->username;
                $feed->author_nickname = $user->getNickname();
                $feed->author_picture = $user->getPicture();
                $feed->post_id = $post->id;
                $feed->post_filename = $post->filename;
                $feed->post_description = $post->description;
                $feed->post_created_at = $post->created_at;

                $feed->save();
            }
        }

    }


}