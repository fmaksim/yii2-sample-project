<?php

namespace frontend\components;

use frontend\models\Comment;
use frontend\models\User;
use frontend\modules\comment\models\forms\CommentForm;
use yii\web\NotFoundHttpException;

class CommentService
{

    public function add(int $postId, User $user, $text): bool
    {

        $comment = $this->create();
        $form = new CommentForm();
        $form->setText($text);

        if ($form->validate()) {
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

    public function edit(Comment $comment, $text): bool
    {

        $form = new CommentForm();
        $form->setText($text);

        if ($form->validate()) {
            $comment->text = $text;

            if ($comment->save(false)) {
                return true;
            }

        }

        return false;

    }

    public function remove(int $id): bool
    {
        $comment = $this->findById($id);
        if ($comment) {
            if ($comment->delete()) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function findById(int $id): Comment
    {
        if ($comment = Comment::findOne($id)) {
            return $comment;
        }

        throw new NotFoundHttpException();
    }

    private function create(): Comment
    {
        return new Comment();
    }

}