<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use dosamigos\fileupload\FileUpload;

?>

<h3><?php echo "Hello, " . Html::encode($user->username); ?></h3>
<br>
<?php echo HTMLPurifier::process($user->about); ?>
<img id="profile-picture" src="<?php echo $user->getPicture(); ?>">

<?php if ($currentUser && $currentUser->isIAm($user)): ?>
    <div class="alert alert-success display-none" id="profile-image-success">Profile image updated</div>
    <div class="alert alert-danger display-none" id="profile-image-fail"></div>
    <?= FileUpload::widget([
        'model' => $pictureModel,
        'attribute' => 'picture',
        'url' => ['/user/profile/upload-photo'], // your url, this is just for demo purposes,
        'options' => ['enctype' => 'multipart/form-data', 'accept' => 'image/*'],
        'clientEvents' => [
            'fileuploaddone' => 'function(e, data) {
                if (data.result.success) {
                    $("#profile-picture").attr("src", data.result.pictureUri);
                    $("#alert-success").show();
                    $("#profile-image-fail").hide();
                    location.reload();
                } else {
                    $("#profile-image-fail").html(data.result.errors.picture).show();
                }
            }',

        ],
    ]); ?>
    <?php if ($currentUser->getPicture() !== $currentUser::DEFAULT_IMAGE): ?>
        <?php echo Html::a("Remove photo", ["/user/profile/delete-photo/"], ["class" => "btn btn-danger"]); ?>
    <?php endif; ?>
    <hr>
<?php else: ?>
    <?php if ($currentUser && $currentUser->isCanSubscribe($user)) echo Html::a("Subscribe",
        ["/user/profile/subscribe", "id" => $user->id], [
        "class" => "btn btn-primary"
    ]); ?>
    <?php if ($currentUser && $currentUser->isCanUnSubscribe($user)) echo Html::a("UnSubscribe",
        ["/user/profile/unsubscribe", "id" => $user->id], [
        "class" => "btn btn-primary",
        "style" => "margin-left:5px;"
    ]);
    ?>
    <?php if ($currentUser and $currentUser->isShowFollowBlock($mutuals = $currentUser->getMutualSubscriptionsTo($user))): ?>
        <h5>Users, who also followed <?php echo Html::encode($user->username); ?></h5>
        <div class="row">
            <div class="col-md-12">
                <?php foreach ($mutuals as $mutual): ?>
                    <?php echo Html::a(Html::encode($mutual["username"]), ["/user/profile/view/", "nickname" => $mutual["nickname"] ? $mutual["nickname"] : $mutual["id"]]); ?>
                    <br>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php echo Html::button("Subscriptions " . $user->getSubscriptionsCount(), [
    "class" => "btn btn-primary",
    "data-toggle" => "modal",
    "data-target" => "#subscriptions"
]); ?>
<?php echo Html::button("Followers " . $user->getFollowersCount(), [
    "class" => "btn btn-primary",
    "style" => "margin-left:5px;",
    "data-toggle" => "modal",
    "data-target" => "#followers"
]); ?>

<!-- Modal -->
<div class="modal fade" id="subscriptions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Subscriptions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php foreach ($user->getSubscriptions() as $subscription): ?>
                    <?php echo Html::a(Html::encode($subscription["username"]), ["/user/profile/view/", "nickname" => $subscription["nickname"] ? $subscription["nickname"] : $subscription["id"]]); ?>
                    <br>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="followers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Followers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php foreach ($user->getFollowers() as $follower): ?>
                    <?php echo Html::a(Html::encode($follower["username"]), ["/user/profile/view/", "nickname" => $follower["nickname"] ? $follower["nickname"] : $follower["id"]]); ?>
                    <br>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>