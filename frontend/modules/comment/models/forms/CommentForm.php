<?php

namespace frontend\modules\comment\models\forms;

use yii\base\Model;

class CommentForm extends Model
{
    const MAX_TEXT_LENGTH = 3000;

    public $text;

    public function setText($text)
    {
        $this->text = $text;
    }

    public function rules()
    {
        return [
            ["text", "string", "max" => self::MAX_TEXT_LENGTH],
        ];
    }

    public function formName()
    {
        return '';
    }


}