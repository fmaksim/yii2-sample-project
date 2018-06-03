<?php
/**
 * @var $this yii\base\View
 * @var $currentUser frontend\models\User
 * @var $post frontend\models\Post
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;

?>
<div class="post-default-index">
    <div class="row">
        <div class="col-md-12">
            <?php if ($post->user): ?>
                <?php echo $post->user->username; ?>
            <?php endif; ?>
        </div>
        <div class="col-md-12">
            <?php echo Html::encode($post->description); ?>
        </div>
        <div class="col-md-12">
            <img id="profile-picture" src="<?php echo $post->getImage(); ?>">
        </div>

        <div class="col-md-12">
            Likes: <span class="likes-count"><?php echo $post->getCountLikes(); ?></span>

            <a href="#"
               class="btn btn-primary button-unlike <?php echo ($currentUser && $post->isLiked()) ? "" : "display-none"; ?>"
               data-id="<?php echo $post->id; ?>">
                Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
            </a>
            <a href="#"
               class="btn btn-primary button-like <?php echo ($currentUser && $post->isLiked()) ? "display-none" : ""; ?>"
               data-id="<?php echo $post->id; ?>">
                Like&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
            </a>

        </div>
        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(["action" => "/post/" . $post->id . "/comment"]); ?>
                <div class="form-group">
                    <h3>Post a comment</h3>
                </div>
                <div class="form-group">
                    <label for="post-comment">Your comment</label>
                    <textarea name="text" class="form-control" id="post-comment" rows="3"></textarea>
                </div>
                <?php echo Html::submitButton("Add comment", ["class" => "btn btn-primary"]); ?>
                <?php ActiveForm::end(); ?>
            </div>
        <?php endif; ?>

        <?php if (count($post->comments) > 0): ?>
            <div class="col-md-12">
                <h3>Comments:</h3>
                <?php foreach ($post->comments as $comment): ?>
                    <div class="row">
                        <div class="well well-lg">
                            <?php echo $comment->getDate() . " " . $comment->username; ?>
                            <?php if (Yii::$app->user->identity && $comment->user_id === Yii::$app->user->identity->getId()): ?>
                                <?php echo Html::a('Edit', ['/comment/edit/' . $comment->id]) ?>
                            <?php endif; ?>
                        </div>
                        <div><?php echo Html::encode($comment->text); ?></div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->registerJsFile('@web/js/likes.js', ["depends" => JqueryAsset::className()]); ?>