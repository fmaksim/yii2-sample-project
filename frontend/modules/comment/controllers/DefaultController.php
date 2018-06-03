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

    public function behaviors()
    {
        return [
            'auth' => [
                'class' => 'frontend\components\behaviors\Guest',
            ]
        ];
    }

    public function actionCreate(int $postId)
    {

        try {

            if ($this->postService->isExist($postId)) {
                $user = Yii::$app->user->identity;
                $text = Yii::$app->request->post("text");

                if ($this->commentService->add($postId, $user, $text)) {

                    Yii::$app->session->setFlash("success", "Comment succefully added!");
                    return $this->redirect(Yii::$app->request->referrer);

                } else {

                    Yii::$app->session->setFlash("error",
                        "Some error with saving comment, please contact our support service");
                    return $this->redirect(Yii::$app->request->referrer);

                }
            }

        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
            return $this->goHome();
        }
    }

    public function actionDelete(int $id)
    {

        $comment = $this->commentService->findById($id);

        try {

        } catch (\Exception $e) {

        }

    }

    public function actionEdit(int $id)
    {

        $comment = $this->commentService->findById($id);

        if (Yii::$app->request->isPost) {
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