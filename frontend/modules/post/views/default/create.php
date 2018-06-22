<?php

/**
 * @var $this yii\web\View
 * @var $post frontend\models\Post;
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="post-default-index">
    <h1><?= Yii::t('create-post', 'Create new Post'); ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($post, 'description'); ?>
    <?php echo $form->field($post, "filename")->fileInput(); ?>

    <?php echo Html::submitButton(Yii::t('create-post', 'Add post'), ["class" => "btn btn-primary"]); ?>

    <?php ActiveForm::end(); ?>
    <br>
</div>

