<?php

namespace console\controllers;

use yii\console\Controller;
use frontend\models\User;


class UserSeederController extends Controller
{

    public function actionGenerate()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 1000; ++$i) {
            (new User([
                'username' => $faker->name,
                'email' => $faker->email,
                'about' => $faker->text(200),
                'nickname' => $faker->regexify('[A-Za-z0-9_]{5,15}'),
                'auth_key' => \Yii::$app->security->generateRandomString(),
                'password_hash' => \Yii::$app->security->generateRandomString(),
                'created_at' => time(),
                'updated_at' => time(),
            ]))->save(false);
        }
    }

}
