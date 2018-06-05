<?php
namespace frontend\controllers;

use frontend\components\storage\Storage;
use yii\web\Controller;
use frontend\models\User;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{

    protected $fileStorage;

    public function __construct($id, $module, Storage $fileStorage, array $config = [])
    {
        $this->fileStorage = $fileStorage;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }

        $currentUser = Yii::$app->user->identity;
        $feedPostsLimit = Yii::$app->params['postsFeedLimit'];
        $fileStorage = $this->fileStorage;

        $feedItems = $currentUser->getFeed($feedPostsLimit);

        return $this->render('index',
            [
                "feedItems" => $feedItems,
                "currentUser" => $currentUser,
                "fileStorage" => $fileStorage
            ]
        );
    }

}
