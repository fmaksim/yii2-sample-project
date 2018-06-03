<?php

namespace frontend\modules\comment\controllers;

use frontend\components\CommentService;
use frontend\components\PostService;
use yii\web\Controller;
use Yii;

/**
 * Default controller for the `comment` module
 */
class DefaultController extends Controller
{
    protected $postService;
    protected $commentService;

    public function __construct(
        $id,
        $module,
        PostService $postService,
        CommentService $commentService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->postService = $postService;
        $this->commentService = $commentService;
    }

    public function actionCreate(int $postId)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash("error", "You must login before posting comment");
            return $this->goHome();
        }
        $this->postService->findById($postId);

        try {

            $user = Yii::$app->user->identity;
            $text = Yii::$app->request->post("text");
            $result = $this->commentService->add($postId, $user, $text);

            if ($result) {
                Yii::$app->session->setFlash("success", "Comment succefully added!");
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash("error",
                    "Some error with saving comment, please contact our support service");
                return $this->redirect(Yii::$app->request->referrer);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
            return $this->goHome();
        }
    }

    public function actionDelete()
    {

        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash("error", "You must login before removing comment");
            return $this->goHome();
        }

        try {

        } catch (\Exception $e) {

        }
    }

    public function actionEdit(int $id)
    {

        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash("error", "You must login before editing comment");
            return $this->goHome();
        }
        $comment = $this->commentService->findById($id);

        if (Yii::$app->request->post()) {
            try {
                $text = Yii::$app->request->post("Comment")["text"];

                if ($this->commentService->edit($comment, $text)) {

                    Yii::$app->session->setFlash("success", "Comment succefully updated!");
                    return $this->redirect(Yii::$app->request->referrer);

                } else {

                    Yii::$app->session->setFlash("error",
                        "Some error with saving comment, please contact our support service");
                    return $this->redirect(Yii::$app->request->referrer);

                }

            } catch (\Exception $e) {

                Yii::$app->session->setFlash("error", $e->getMessage());
                return $this->refresh();

            }
        }

        return $this->render('edit', ["comment" => $comment]);
    }

}