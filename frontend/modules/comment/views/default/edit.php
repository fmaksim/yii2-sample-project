<?php

/**
 * @var $this yii\web\View
 * @var $comment frontend\models\Comment;
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="post-default-index">
    <h1>Edit comment</h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($comment, "text")->textarea(['rows' => 5]); ?>

    <?php echo Html::submitButton("Edit comment", ["class" => "btn btn-primary"]); ?>

    <?php ActiveForm::end(); ?>

</div>

