<?php

namespace frontend\components;


use frontend\models\Comment;
use frontend\models\User;
use frontend\modules\comment\models\forms\CommentForm;
use Yii;

class CommentService
{

    public function add(int $postId, User $user, $text): bool
    {

        $comment = $this->create();
        $form = new CommentForm();
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $comment->post_id = $postId;
            $comment->user_id = $user->getId();
            $comment->username = $user->getUsername();
            $comment->text = $text;

            if ($comment->save(false)) {
                return true;
            }
        }

        return false;

    }

    private function create(): Comment
    {
        return new Comment();
    }

    public function edit()
    {

    }

    public function remove()
    {

    }

}