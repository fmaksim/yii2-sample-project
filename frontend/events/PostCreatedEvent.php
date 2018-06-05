<?php

namespace frontend\events;

use frontend\models\Post;
use frontend\models\User;
use yii\base\Event;

class PostCreatedEvent extends Event
{

    public $user;
    public $post;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

}