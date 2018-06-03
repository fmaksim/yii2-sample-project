<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $parent_id
 * @property string $text
 * @property string $username
 * @property int $status
 * @property int $created_at
 */
class Comment extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    public function getDate()
    {
        return date('d.m.Y H:i', $this->created_at);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'post_id' => 'Post ID',
            'parent_id' => 'Parent ID',
            'text' => 'Text',
            'username' => 'Username',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
