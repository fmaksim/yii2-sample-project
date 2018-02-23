<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>

        <?= yii\authclient\widgets\AuthChoice::widget([
            'baseAuthUrl' => ['user/default/auth'],
            'popupMode' => false,
        ]) ?>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <h2>Users</h2>
                <?php foreach ($users as $user): ?>
                    <?php echo Html::a($user->username, ['/user/profile/view', 'nickname' => $user->id], ['class' => 'profile-link']) ?>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
