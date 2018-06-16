<?php

namespace common\components\storage;

use yii\base\Component;
use Yii;

class RedisStorage extends Component
{

    protected $storage;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->storage = Yii::$app->redis;
    }

    public function getStorage()
    {
        return $this->storage;
    }

}