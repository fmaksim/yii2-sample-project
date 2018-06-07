<?php

namespace frontend\components;

use frontend\components\storage\RedisStorage;
use frontend\components\storage\Storage;
use frontend\models\Post;
use yii\web\NotFoundHttpException;

class PostService
{

    protected $likeService;
    protected $fileStorage;
    protected $redisStorage;

    public function __construct(LikeService $likeService, Storage $storage, RedisStorage $redisStorage)
    {
        $this->likeService = $likeService;
        $this->fileStorage = $storage;
        $this->redisStorage = $redisStorage->getStorage();
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

    public function isExist(int $id): bool
    {
        if ($this->findById($id)) {
            return true;
        } else {
            return false;
        }
    }

    public function toggleLike(Post $post): bool
    {

        $this->likeService->setType('post');
        return $this->likeService->toggleLike($post);

    }

    public function getCommentsCount(int $postId): int
    {
        return $this->redisStorage->get("post:{$postId}:comments");
    }

}