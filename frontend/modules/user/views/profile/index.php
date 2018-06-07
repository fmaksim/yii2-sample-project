<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use dosamigos\fileupload\FileUpload;

?>

<div class="page-posts no-padding">
    <div class="row">
        <div class="page page-post col-sm-12 col-xs-12 post-82">

            <div class="blog-posts blog-posts-large">

                <div class="row">

                    <!-- profile -->
                    <article class="profile col-sm-12 col-xs-12">
                        <div class="profile-title">
                            <img src="<?php echo $user->getPicture() ?? Yii::$app->params["defaultProfileImage"]; ?>"
                                 class="author-image"/>
                            <div class="author-name"><?php echo Html::encode($user->username); ?></div>
                            <?php if ($currentUser && $currentUser->isIAm($user)): ?>
                                <?= FileUpload::widget([
                                    'model' => $pictureModel,
                                    'attribute' => 'picture',
                                    'url' => ['/user/profile/upload-photo'],
                                    // your url, this is just for demo purposes,
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
                                <a href="#" class="btn btn-default">Edit profile</a>
                                <?php if ($currentUser->getPicture()): ?>
                                    <?php echo Html::a("Remove photo", ["/user/profile/delete-photo/"],
                                        ["class" => "btn btn-danger"]); ?>
                                <?php endif; ?>
                                <hr>
                            <?php else: ?>
                                <?php if ($currentUser && !$subscriptionService->isFollowed($user)) {
                                    echo Html::a(
                                        $subscriptionService->isFollowed($user) ? "UnSubscribe" : "Subscribe",
                                        ["/user/profile/toggle-subscribe", "id" => $user->id], [
                                        "class" => "btn btn-primary"
                                    ]);
                                } ?>
                                <?php if ($currentUser && $subscriptionService->isFollowed($user)) {
                                    echo Html::a("UnSubscribe",
                                        ["/user/profile/toggle-subscribe", "id" => $user->id], [
                                            "class" => "btn btn-primary",
                                            "style" => "margin-left:5px;"
                                        ]);
                                }
                                ?>
                                <?php if ($currentUser and $currentUser->isShowFollowBlock($mutuals = $currentUser->getMutualSubscriptionsTo($user))): ?>
                                    <h5>Users, who also followed <?php echo Html::encode($user->username); ?></h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php foreach ($mutuals as $mutual): ?>
                                                <?php echo Html::a(Html::encode($mutual["username"]), [
                                                    "/user/profile/view/",
                                                    "nickname" => $mutual["nickname"] ? $mutual["nickname"] : $mutual["id"]
                                                ]); ?>
                                                <br>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($user->about): ?>
                            <div class="profile-description">
                                <?php echo HTMLPurifier::process($user->about); ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-bottom">
                            <div class="profile-post-count">
                                <span>16 posts</span>
                            </div>
                            <div class="profile-followers">
                                <a data-target="#followers"
                                   data-toggle="modal"><?php echo $user->getFollowersCount(); ?> followers</a>
                            </div>
                            <div class="profile-following">
                                <a data-target="#subscriptions"
                                   data-toggle="modal"><?php echo $user->getSubscriptionsCount(); ?> following</a>
                            </div>
                        </div>
                    </article>

                    <div class="col-sm-12 col-xs-12">
                        <div class="row profile-posts">
                            <div class="col-md-4 profile-post">
                                <a href="#"><img src="img/demo/car.jpg" class="author-image"/></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <?php echo Html::a(Html::encode($subscription["username"]), [
                        "/user/profile/view/",
                        "nickname" => $subscription["nickname"] ? $subscription["nickname"] : $subscription["id"]
                    ]); ?>
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
                    <?php echo Html::a(Html::encode($follower["username"]), [
                        "/user/profile/view/",
                        "nickname" => $follower["nickname"] ? $follower["nickname"] : $follower["id"]
                    ]); ?>
                    <br>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>