<?php

namespace frontend\modules\post\controllers;

use frontend\services\LikeService;
use Yii;
use yii\web\Controller;
use frontend\models\Post;
use frontend\modules\post\models\forms\PostForm;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\Response;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
            return $this->goHome();

        $user = Yii::$app->user->identity;
        $storage = Yii::$app->fileStorage;
        $post = new Post();

        $postForm = new PostForm($user, $storage, $post);

        if ($postForm->load(Yii::$app->request->post())) {
            $postForm->filename = UploadedFile::getInstance($postForm, "filename");

            if ($postForm->save()) {
                Yii::$app->session->setFlash("success", "Post has been added!");
                return $this->goHome();
            }
        }

        return $this->render('create', ["post" => $postForm]);
    }

    public function actionView($id)
    {
        $post = $this->findPostById($id);

        return $this->render('view', [
                "post" => $post,
                "currentUser" => Yii::$app->user->identity,
            ]
        );
    }

    private function findPostById($id)
    {

        if ($post = Post::findOne($id)) {
            return $post;
        }

        throw new NotFoundHttpException();
    }

    public function actionToggleLike()
    {
        if (Yii::$app->user->isGuest)
            return $this->goHome();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = $this->findPostById(Yii::$app->request->post("id"));

        try {
            Yii::$app->likeService->setType("post");
            $result = Yii::$app->likeService->toggleLike($post);
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

    public function actionUnlike()
    {

        if (Yii::$app->user->isGuest)
            return $this->goHome();

        Yii::$app->response->format = Response::FORMAT_JSON;

        $currentUser = Yii::$app->user->identity;

        $post = $this->findPostById(Yii::$app->request->post("id"));

        if ($post->doUnlike($currentUser)) {
            return [
                "success" => true,
                "likesCount" => $post->getCountLikes()
            ];
        } else {
            return [
                "error" => true,
                "likesCount" => $post->getCountLikes()
            ];
        }
    }

}
