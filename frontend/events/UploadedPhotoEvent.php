<?php

namespace frontend\events;

use yii\base\Event;

class UploadedPhotoEvent extends Event
{

    public $tempFile;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

}