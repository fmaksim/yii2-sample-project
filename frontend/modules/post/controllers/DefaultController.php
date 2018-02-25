<?php

namespace frontend\modules\post\controllers;

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

        $post->setLikesStorage(Yii::$app->redis);

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

    public function actionLike()
    {

        if (Yii::$app->user->isGuest)
            return $this->goHome();

        Yii::$app->response->format = Response::FORMAT_JSON;

        $currentUser = Yii::$app->user->identity;
        $post = $this->findPostById(Yii::$app->request->post("id"));

        $post->setLikesStorage(Yii::$app->redis);

        if ($post->doLike($currentUser)) {
            return [
                "success" => true,
                "likesCount" => $post->countLikes()
            ];
        } else {
            return [
                "error" => true,
                "likesCount" => $post->countLikes()
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
        $post->setLikesStorage(Yii::$app->redis);

        if ($post->doUnlike($currentUser)) {
            return [
                "success" => true,
                "likesCount" => $post->countLikes()
            ];
        } else {
            return [
                "error" => true,
                "likesCount" => $post->countLikes()
            ];
        }
    }

}
