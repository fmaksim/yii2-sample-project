<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User */
/* @var $feedItems [] frontend\models\Feed */
/* @var $fileStorage frontend\components\storage\Storage */
/* @var $likeService frontend\components\LikeService */
/* @var $postService frontend\components\PostService */
/* @var $complaintService frontend\components\ComplaintService */

$this->title = 'My Yii Application';

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\JqueryAsset;

?>

    <div class="page-posts no-padding">
        <div class="row">
            <div class="page page-post col-sm-12 col-xs-12">
                <div class="blog-posts blog-posts-large">

                    <div class="row">
                        <?php if ($feedItems): ?>

                            <?php foreach ($feedItems as $feedItem): ?>
                                <article class="post col-sm-12 col-xs-12">
                                    <div class="post-meta">
                                        <div class="post-title">
                                            <img src="<?php echo $feedItem->author_picture ?? Yii::$app->params["defaultProfileImage"]; ?>"
                                                 class="author-image"/>
                                            <div class="author-name"><a href="<?php echo Url::to([
                                                    '/user/profile/view',
                                                    'nickname' => ($feedItem->author_nickname) ?? $feedItem->author_id
                                                ]) ?>"><?php echo $feedItem->author_name; ?></a></div>
                                        </div>
                                    </div>
                                    <div class="post-type-image">
                                        <a href="<?php echo Url::to([
                                            "/post/default/view",
                                            "id" => $feedItem->post_id
                                        ]) ?>">
                                            <img src="<?php echo $fileStorage->getFile($feedItem->post_filename); ?>"
                                                 alt="">
                                        </a>
                                    </div>
                                    <div class="post-description">
                                        <?php echo HtmlPurifier::process($feedItem->post_description); ?>
                                    </div>
                                    <div class="post-bottom">
                                        <div class="post-likes">
                                            <a href="#" class="btn btn-secondary"><i
                                                        class="fa fa-lg fa-heart-o"></i></a>

                                            <span class="likes-count"><?php echo $likeService->getCount($feedItem->post_id); ?>
                                                Likes</span>

                                            <a href="#"
                                               class="btn btn-default like button-unlike <?php echo ($currentUser && $likeService->isLiked($feedItem->post_id)) ? "" : "display-none"; ?>"
                                               data-id="<?php echo $feedItem->post_id; ?>">
                                                Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
                                            </a>
                                            <a href="#"
                                               class="btn btn-default like button-like <?php echo ($currentUser && $likeService->isLiked($feedItem->post_id)) ? "display-none" : ""; ?>"
                                               data-id="<?php echo $feedItem->post_id; ?>">
                                                Like&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
                                            </a>
                                        </div>
                                        <div class="post-comments">
                                            <a href="<?php echo Url::to([
                                                "/post/default/view",
                                                "id" => $feedItem->post_id
                                            ]) ?>#comments"><?php echo $postService->getCommentsCount($feedItem->post_id); ?>
                                                Comments</a>

                                        </div>
                                        <div class="post-date">
                                            <span><?php echo Yii::$app->formatter->asDatetime($feedItem->post_created_at); ?></span>
                                        </div>
                                        <div class="post-report">
                                            <?php if ($complaintService->isComplain($feedItem->post_id,
                                                $currentUser->getId())): ?>
                                                <span>You already complained!</span>
                                            <?php else: ?>
                                                <a href="javascript:;" class="btn btn-default button-complain"
                                                   data-id="<?php echo $feedItem->post_id; ?>">
                                                    Report post <i class="fa fa-cog fa-spin fa-fw icon-placeholder"
                                                                   style="display: none;"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="col-md-12">
                                Nobody posted yet!
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/likes.js', ["depends" => JqueryAsset::className()]); ?>
<?php $this->registerJsFile('@web/js/complain.js', ["depends" => JqueryAsset::className()]); ?>
