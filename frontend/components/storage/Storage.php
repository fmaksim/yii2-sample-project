<?php

namespace frontend\components\storage;

use Intervention\Image\ImageManager;
use Yii;
use yii\base\Component;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * Class Storage - component provide basic methods for manipulate files (resize, store, delete)
 * @package frontend\components\storage
 * @property string $fileName
 */
class Storage extends Component implements StorageInterface
{
    private $fileName;

    public function saveUploadedFile(UploadedFile $file): string
    {

        $path = $this->preparePath($file);

        if ($path && $file->saveAs($path))
            return $this->fileName;

    }

    public function getFile(string $fileName): string
    {
        return Yii::$app->params['storageUrl'] . $fileName;
    }

    public function deleteFile(string $fileName): bool
    {
        $file = $this->getStoragePath() . $fileName;

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function resize(UploadedFile $file): void
    {

        $maxWidth = Yii::$app->params['imgSize']['maxWidth'];
        $maxHeight = Yii::$app->params['imgSize']['maxHeight'];

        $manager = new ImageManager(['driver' => 'imagick']);
        $image = $manager->make($file->tempFile);

        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save();

    }

    /**
     * @param UploadedFile $file
     */
    protected function preparePath(UploadedFile $file): string
    {

        $this->fileName = $this->getFileName($file);
        $path = $this->getStoragePath() . $this->fileName;
        $path = FileHelper::normalizePath($path);

        if (FileHelper::createDirectory(dirname($path))) {
            return $path;
        }

    }

    /**
     * @return string
     */
    protected function getStoragePath(): string
    {
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

    private function getFileName(UploadedFile $file): string
    {

        $hash = sha1_file($file->tempName);

        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);
        return $name . '.' . $file->extension;
    }

}