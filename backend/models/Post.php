<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $description
 * @property string $filename
 * @property int $user_id
 * @property int $created_at
 * @property int $complaints
 */
class Post extends \yii\db\ActiveRecord
{

    protected $fileStorage;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->fileStorage = Yii::createObject(['class' => 'common\components\storage\Storage']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    public static function findComplaints()
    {
        return Post::find()
            ->where('complaints > 0')
            ->orderBy('complaints DESC');
    }

    public function setComplaints(int $value)
    {
        $this->complaints = $value;
    }

    public function getImage()
    {
        return $this->fileStorage->getFile($this->filename);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'filename' => 'Filename',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'complaints' => 'Complaints',
        ];
    }
}
