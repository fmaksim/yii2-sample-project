<?php

namespace frontend\modules\user\controllers;

use frontend\components\storage\Storage;
use frontend\components\SubscriptionService;
use Yii;
use yii\web\Controller;
use frontend\models\User;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\modules\user\models\forms\PictureForm;

/**
 * Default controller for the `user` module
 */
class ProfileController extends Controller
{

    protected $subscriptionService;
    protected $fileStorage;

    public function __construct(
        $id,
        $module,
        Storage $fileStorage,
        SubscriptionService $subscriptionService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->subscriptionService = $subscriptionService;
        $this->fileStorage = $fileStorage;
    }

    /**
     * Renders the index view for the profile page
     * @return string
     */
    public function actionView($nickname)
    {
        $pictureModel = new PictureForm($this->fileStorage);

        return $this->render('index', [
            "user" => $this->findUser($nickname),
            "currentUser" => Yii::$app->user->identity,
            "pictureModel" => $pictureModel,
            "subscriptionService" => $this->subscriptionService,
        ]);
    }

    protected function findUser($nickname)
    {
        return User::find()->where(["id" => $nickname])->orWhere(["nickname" => $nickname])->one();
    }

    public function actionUploadPhoto()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $pictureModel = new PictureForm($this->fileStorage);
        $user = Yii::$app->user->identity;

        try {
            $pictureModel->user = $user;
            $pictureModel->picture = UploadedFile::getInstance($pictureModel, "picture");

            if ($pictureModel->save()) {
                return [
                    "success" => true,
                    "pictureUri" => $this->fileStorage->getFile($user->picture),
                ];
            }

            return [
                "success" => false,
                "errors" => $pictureModel->getErrors(),
            ];

        } catch (\Exception $e) {
            return [
                "success" => false,
                "errors" => $e->getMessage(),
            ];
        }

    }

    public function actionDeletePhoto()
    {

        if (Yii::$app->user->isGuest)
            return $this->redirect(["/"]);

        $currentUser = Yii::$app->user->identity;

        if (Yii::$app->fileStorage->deleteFile($currentUser->picture)) {
            Yii::$app->session->setFlash("success", "File has been removed successfully!");
            $currentUser->picture = null;
            $currentUser->save(false, ["picture"]);
        } else {
            Yii::$app->session->setFlash("error", "Permission denied!");
        }

        return $this->redirect(["/user/profile/view", "nickname" => $currentUser->getNickname()]);
    }

    public function actionToggleSubscribe($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(["/user/default/login"]);

        try {
            $followedUser = $this->findUserById($id);

            if (Yii::$app->user->identity->isIAm($followedUser) || !$this->subscriptionService->toggleSubscribe($followedUser)) {
                Yii::$app->session->setFlash("error", "Subscription error, please try later!");
            }

            return $this->redirect(["/user/profile/view", "nickname" => $followedUser->getId()]);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
        }

    }

    protected function findUserById($id)
    {
        if ($user = User::findOne($id)) {
            return $user;
        }
        throw new NotFoundHttpException();
    }


}
