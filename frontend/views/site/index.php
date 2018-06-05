<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User */
/* @var $feedItems [] frontend\models\Feed */
/* @var $fileStorage frontend\components\storage\Storage */

$this->title = 'My Yii Application';

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

?>
<div class="site-index">
    <?php if ($feedItems): ?>

        <?php foreach ($feedItems as $feedItem): ?>
            <div class="col-md-12">

                <div class="col-md-12">
                    <img src="<?php echo $feedItem->author_picture; ?>" width="30" height="30">
                    <a href="<?php echo Url::to([
                        '/user/profile/view',
                        'nickname' => ($feedItem->author_nickname) ? $feedItem->author_nickname : $feedItem->author_id
                    ]) ?>">
                        <?php echo Html::encode($feedItem->author_name); ?>
                    </a>
                </div>
                <img src="<?php echo $fileStorage->getFile($feedItem->post_filename); ?>">
                <div class="col-md-12">
                    <?php echo HtmlPurifier::process($feedItem->post_description); ?>
                </div>
                <div class="col-md-12">
                    <?php echo Yii::$app->formatter->asDatetime($feedItem->post_created_at); ?>
                </div>
            </div>
            <div class="col-md-12">
                <hr/>
            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="col-md-12">
            Nobody posted yet!
        </div>

    <?php endif; ?>
</div>
