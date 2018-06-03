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

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
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
            $message = Yii::$app->request->post("message");
            $result = $this->commentService->add($postId, $user, $message);

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
        try {

        } catch (\Exception $e) {

        }
    }

    public function actionEdit()
    {
        try {

        } catch (\Exception $e) {

        }
    }

}