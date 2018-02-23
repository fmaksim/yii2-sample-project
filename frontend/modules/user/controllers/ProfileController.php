<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\User;
use yii\web\NotFoundHttpException;
use frontend\modules\user\models\forms\PictureForm;
use yii\web\UploadedFile;

/**
 * Default controller for the `user` module
 */
class ProfileController extends Controller
{

    /**
     * Renders the index view for the profile page
     * @return string
     */
    public function actionView($nickname)
    {
        $pictureModel = new PictureForm();

        return $this->render('index', [
            "user" => $this->findUser($nickname),
            "currentUser" => Yii::$app->user->identity,
            "pictureModel" => $pictureModel,
        ]);
    }

    protected function findUser($nickname)
    {
        return User::find()->where(["id" => $nickname])->orWhere(["nickname" => $nickname])->one();
    }

    public function actionUploadPhoto()
    {

        $pictureModel = new PictureForm();
        $pictureModel->picture = UploadedFile::getInstance($pictureModel, "picture");

        if ($pictureModel->validate()) {
            echo "OK";
            die();
        }

        print_r($pictureModel->getErrors());
        die();

    }

    public function actionSubscribe($id)
    {

        if (Yii::$app->user->isGuest)
            return $this->redirect(["/user/default/login"]);

        $currentUser = Yii::$app->user->identity;
        $followedUser = $this->findUserById($id);

        $currentUser->followUser($followedUser);

        return $this->redirect(["/user/profile/view", "nickname" => $followedUser->getId()]);

    }

    protected function findUserById($id)
    {
        if ($user = User::findOne($id)) {
            return $user;
        }
        throw new NotFoundHttpException();
    }

    /*public function actionTest()
    {
        $faker = \Faker\Factory::create();

        for($i = 0; $i < 1000; $i++){
            $user = new User();
            $user->about = $faker->text(200);
            $user->nickname = $faker->regexify("[A-Za-z0-9_]{5,15}");
            $user->username = $faker->name;
            $user->password_hash = Yii::$app->security->generateRandomString();
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->created_at = time();
            $user->updated_at = time();
            $user->email = $faker->email;

            $user->save(false);
        }

    }*/

    public function actionUnsubscribe($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(["/user/default/login"]);

        $currentUser = Yii::$app->user->identity;
        $followedUser = $this->findUserById($id);

        $currentUser->unFollowUser($followedUser);

        return $this->redirect(["/user/profile/view", "nickname" => $currentUser->getId()]);
    }


}
