<?php

namespace frontend\components\behaviors;

use yii\base\Behavior;
use yii\web\Controller;
use Yii;

class Guest extends Behavior
{

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'isGuest'
        ];
    }

    public function isGuest()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash("error", "You must login before posting comment");
            return Yii::$app->getResponse()->redirect("/");
        }
    }

}