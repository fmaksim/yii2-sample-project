<?php

namespace frontend\components;

use frontend\models\User;
use yii\web\NotFoundHttpException;

/**
 * Class ProfileService
 * @package frontend\components
 */
class ProfileService
{

    public function findByNickname($nickname): User
    {
        return User::find()
            ->where(["id" => $nickname])
            ->orWhere(["nickname" => $nickname])
            ->one();

        throw new NotFoundHttpException();
    }

    public function findById(int $id): User
    {
        if ($user = User::findOne($id)) {
            return $user;
        }

        throw new NotFoundHttpException();
    }

}