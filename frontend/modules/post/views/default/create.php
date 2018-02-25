<?php

/**
 * @var $this yii\web\View
 * @var $post frontend\models\Post;
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="post-default-index">
    <h1>Create new Post</h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($post, "description"); ?>
    <?php echo $form->field($post, "filename")->fileInput(); ?>

    <?php echo Html::submitButton("Add post", ["class" => "btn btn-primary"]); ?>

    <?php ActiveForm::end(); ?>

</div>

