<?php

namespace frontend\components;

use frontend\components\storage\RedisStorage;
use frontend\components\storage\Storage;
use frontend\models\Post;
use frontend\models\User;
use yii\web\NotFoundHttpException;

/**
 * Class PostService - business logic for managing posts
 * @package frontend\components
 * @property \frontend\components\LikeService $likeService
 * @property \frontend\components\storage\Storage $fileStorage
 * @property \frontend\components\storage\RedisStorage $redisStorage
 * @property \frontend\components\ComplaintService $complaintService
 */
class PostService
{

    protected $likeService;
    protected $fileStorage;
    protected $redisStorage;
    protected $complaintService;

    public function __construct(
        LikeService $likeService,
        Storage $storage,
        RedisStorage $redisStorage,
        ComplaintService $complaintService
    )
    {
        $this->likeService = $likeService;
        $this->fileStorage = $storage;
        $this->redisStorage = $redisStorage->getStorage();
        $this->complaintService = $complaintService;
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

    public function complain(User $user, $postId): bool
    {

        if ($post = $this->findById($postId)) {
            $userId = $user->getId();
            return $this->complaintService->complain($post, $userId);
        }

        return false;

    }

    public function getCommentsCount(int $postId): int
    {
        return $this->redisStorage->get("post:{$postId}:comments");
    }

}