<?php

/* @var $this yii\base\View */
/* @var $currentUser frontend\models\User */
/* @var $post frontend\models\Post */

/* @var $complaintService frontend\components\ComplaintService */


use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;

?>
    <div class="page-posts no-padding">
        <div class="row">
            <div class="page page-post col-sm-12 col-xs-12">
                <div class="blog-posts blog-posts-large">

                    <div class="row">

                        <article class="post col-sm-12 col-xs-12">
                            <div class="post-meta">
                                <div class="post-title">
                                    <img src="<?php echo $post->user->getPicture() ?? Yii::$app->params["defaultProfileImage"]; ?>"
                                         class="author-image"/>
                                    <div class="author-name"><a href="<?php echo Url::to([
                                            '/user/profile/view',
                                            'nickname' => ($post->user->nickname) ?? $post->user->id
                                        ]) ?>"><?php echo $post->user->username; ?></a></div>
                                </div>
                            </div>
                            <div class="post-type-image">
                                <a href="<?php echo Url::to([
                                    "/post/default/view",
                                    "id" => $post->id
                                ]) ?>">
                                    <img src="<?php echo $post->getImage(); ?>" alt="">
                                </a>
                            </div>
                            <div class="post-description">
                                <?php echo HtmlPurifier::process($post->description); ?>
                            </div>
                            <div class="post-bottom">
                                <div class="post-likes">
                                    <a href="#" class="btn btn-secondary"><i
                                                class="fa fa-lg fa-heart-o"></i></a>

                                    <span class="likes-count"><?php echo $post->getCountLikes(); ?>
                                        Likes</span>

                                    <a href="#"
                                       class="btn btn-default like button-unlike <?php echo ($currentUser && $post->isLiked()) ? "" : "display-none"; ?>"
                                       data-id="<?php echo $post->id; ?>">
                                        Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
                                    </a>
                                    <a href="#"
                                       class="btn btn-default like button-like <?php echo ($currentUser && $post->isLiked()) ? "display-none" : ""; ?>"
                                       data-id="<?php echo $post->id; ?>">
                                        Like&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
                                    </a>
                                </div>
                                <div class="post-comments">
                                    <a href="<?php echo Url::to([
                                        "/post/default/view",
                                        "id" => $post->id
                                    ]) ?>#comments"><?php echo count($post->comments); ?> <?= Yii::t('post',
                                            'Comments'); ?></a>

                                </div>
                                <div class="post-date">
                                    <span><?php echo Yii::$app->formatter->asDatetime($post->created_at); ?></span>
                                </div>
                                <div class="post-report">
                                    <?php if ($complaintService->isComplain($post->getId(), $currentUser->getId())): ?>
                                        <span><?= Yii::t('post', 'You already complained!'); ?></span>
                                    <?php else: ?>
                                        <a href="javascript:;" class="btn btn-default button-complain"
                                           data-id="<?php echo $post->getId(); ?>">
                                            <?= Yii::t('post', 'Report post'); ?> <i
                                                    class="fa fa-cog fa-spin fa-fw icon-placeholder"
                                                    style="display: none;"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div id="comments">
                        <div class="row">

                            <?php if (!Yii::$app->user->isGuest): ?>
                                <div class="col-md-12">
                                    <?php $commentForm = ActiveForm::begin(["action" => "/post/" . $post->id . "/comment"]); ?>
                                    <div class="form-group">
                                        <h3><?= Yii::t('comment', 'Post a comment'); ?></h3>
                                    </div>
                                    <div class="form-group">
                                        <label for="post-comment"><?= Yii::t('comment', 'Your comment'); ?></label>
                                        <textarea name="text" class="form-control" id="post-comment"
                                                  rows="3"></textarea>
                                    </div>
                                    <?php echo Html::submitButton(Yii::t('comment', 'Add comment'),
                                        ["class" => "btn btn-primary"]); ?>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (count($post->comments) > 0): ?>
                                <div class="col-md-12">
                                    <h3><?= Yii::t('comment', 'Comments'); ?>:</h3>
                                    <?php foreach ($post->comments as $comment): ?>

                                        <div class="well">
                                            <?php echo $comment->getDate() . " " . $comment->username; ?>
                                            <?php if (Yii::$app->user->identity && $comment->user_id === Yii::$app->user->identity->getId()): ?>
                                                <?php echo Html::a(Yii::t('comment', 'Edit'),
                                                    ["/comment/edit/" . $comment->id],
                                                    ["class" => "pull-right"]) ?>
                                                <?php $deleteCommentForm = ActiveForm::begin([
                                                    "action" => "/comment/delete/" . $comment->id,
                                                    "id" => "form" . $comment->id,
                                                    'options' => [
                                                        'class' => 'pull-right'
                                                    ],
                                                ]); ?>
                                                <?php echo Html::a(Yii::t('comment', 'Remove'),
                                                    ["/comment/delete/" . $comment->id],
                                                    [
                                                        "class" => "remove-comment",
                                                        "data-form" => "form" . $comment->id
                                                    ]) ?>
                                                <?php ActiveForm::end(); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="blockquote"><?php echo Html::encode($comment->text); ?></div>
                                        <hr>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/likes.js', ["depends" => JqueryAsset::className()]); ?>
<?php $this->registerJsFile('@web/js/remove-comment.js', ["depends" => JqueryAsset::className()]); ?>
<?php $this->registerJsFile('@web/js/complain.js', ["depends" => JqueryAsset::className()]); ?>