<?php

namespace frontend\modules\comment\models\forms;

use yii\base\Model;

class CommentForm extends Model
{
    const MAX_MESSAGE_LENGTH = 3000;

    public $message;

    public function rules()
    {
        return [
            ["message", "string", "max" => self::MAX_MESSAGE_LENGTH],
        ];
    }


}