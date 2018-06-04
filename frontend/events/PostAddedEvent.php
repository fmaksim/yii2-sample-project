<?php

namespace frontend\events;

use yii\base\Component;
use yii\base\Event;

class PostAddedEvent extends Event
{

    public $tempFile;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

}