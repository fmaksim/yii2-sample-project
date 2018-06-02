<?php

namespace frontend\components\storage;

use yii\web\UploadedFile;

interface StorageInterface
{
    public function saveUploadedFile(UploadedFile $file);
    public function getFile(string $fileName);
}