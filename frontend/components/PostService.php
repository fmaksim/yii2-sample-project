<?php

namespace frontend\components;

use frontend\components\storage\Storage;
use frontend\models\Post;
use yii\web\NotFoundHttpException;

class PostService
{

    protected $likeService;
    protected $fileStorage;

    public function __construct(LikeService $likeService, Storage $storage)
    {
        $this->likeService = $likeService;
        $this->fileStorage = $storage;
    }

    public function create(): Post
    {
        return new Post($this->likeService, $this->fileStorage);
    }

    public function findById(int $id): Post
    {

        if ($post = Post::findOne($id)) {
            return $post;
        }

        throw new NotFoundHttpException();

    }

    public function toggleLike(Post $post): bool
    {

        $this->likeService->setType('post');
        return $this->likeService->toggleLike($post);

    }

}