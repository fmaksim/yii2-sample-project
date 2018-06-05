<?php

namespace frontend\modules\post\controllers;

use frontend\components\FeedService;
use frontend\components\storage\Storage;
use Yii;
use yii\web\Controller;
use frontend\modules\post\models\forms\PostForm;
use yii\web\UploadedFile;
use yii\web\Response;
use frontend\components\PostService;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{

    protected $postService;
    protected $fileStorage;
    protected $feedService;

    public function __construct(
        $id,
        $module,
        PostService $postService,
        Storage $fileStorage,
        FeedService $feedService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->postService = $postService;
        $this->fileStorage = $fileStorage;
        $this->feedService = $feedService;
    }

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = Yii::$app->user->identity;
        $storage = $this->fileStorage;
        $post = $this->postService->create();
        $feedService = $this->feedService;
        $postForm = new PostForm($user, $storage, $post, $feedService);

        try {
            if ($postForm->load(Yii::$app->request->post())) {
                $postForm->filename = UploadedFile::getInstance($postForm, "filename");

                if ($postForm->save()) {
                    Yii::$app->session->setFlash("success", "Post has been added!");
                    return $this->goHome();
                }
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
            return $this->goHome();
        }

        return $this->render('create', ["post" => $postForm]);
    }

    public function actionView($id)
    {
        $post = $this->postService->findById($id);

        return $this->render('view', [
                "post" => $post,
                "currentUser" => Yii::$app->user->identity,
            ]
        );
    }

    public function actionToggleLike()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            $post = $this->postService->findById(Yii::$app->request->post("id"));
            $result = $this->postService->toggleLike($post);

            return [
                "success" => $result ? true : false,
                "likesCount" => $post->getCountLikes()
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "likesCount" => $post->getCountLikes()
            ];
        }
    }

}