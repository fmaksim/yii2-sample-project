<?php
/**
 * Created by PhpStorm.
 * User: famin
 * Date: 23.2.18
 * Time: 16.11
 */

namespace frontend\components;

use yii\base\Component;
use yii\web\UploadedFile;

class Storage extends Component implements StorageInterface
{
    private $fileName;

    public function saveUploadedFile(UploadedFile $file)
    {

    }

    public function getFile(string $fileName)
    {

    }


    /**
     * @param UploadedFile $file
     */
    protected function preparePath(UploadedFile $file)
    {


    }


}