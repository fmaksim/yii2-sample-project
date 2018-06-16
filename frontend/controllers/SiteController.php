<?php
namespace frontend\controllers;

use frontend\components\ComplaintService;
use frontend\components\LikeService;
use frontend\components\PostService;
use common\components\storage\Storage;
use yii\web\Controller;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{

    protected $fileStorage;
    protected $likeService;
    protected $postService;
    protected $complaintService;

    public function __construct(
        $id,
        $module,
        PostService $postService,
        LikeService $likeService,
        Storage $fileStorage,
        ComplaintService $complaintService,
        array $config = []
    ) {
        $this->fileStorage = $fileStorage;
        $this->likeService = $likeService;
        $this->postService = $postService;
        $this->complaintService = $complaintService;
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
        $complaintService = $this->complaintService;
        $likeService = $this->likeService;
        $likeService->setType("post");

        $feedItems = $currentUser->getFeed($feedPostsLimit);

        return $this->render('index',
            [
                "feedItems" => $feedItems,
                "currentUser" => $currentUser,
                "fileStorage" => $fileStorage,
                "likeService" => $likeService,
                "postService" => $postService,
                "complaintService" => $complaintService
            ]
        );
    }

}
