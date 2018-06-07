<?php
namespace frontend\controllers;

use frontend\components\LikeService;
use frontend\components\PostService;
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
    protected $likeService;
    protected $postService;

    public function __construct(
        $id,
        $module,
        PostService $postService,
        LikeService $likeService,
        Storage $fileStorage,
        array $config = []
    )
    {
        $this->fileStorage = $fileStorage;
        $this->likeService = $likeService;
        $this->postService = $postService;
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
        $postService = $this->postService;
        $likeService = $this->likeService;
        $likeService->setType("post");

        $feedItems = $currentUser->getFeed($feedPostsLimit);

        return $this->render('index',
            [
                "feedItems" => $feedItems,
                "currentUser" => $currentUser,
                "fileStorage" => $fileStorage,
                "likeService" => $likeService,
                "postService" => $postService
            ]
        );
    }

}
