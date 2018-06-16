<?php

/* @var $this yii\web\View */

$this->title = 'Admin panel';

use yii\helpers\Url;

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Admin panel</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Complaints</h2>

                <p><a class="btn btn-default" href="<?php echo Url::to(['/complaints/manage/']); ?>">Manage</a></p>

            </div>
            <div class="col-lg-4">

            </div>
            <div class="col-lg-4">

            </div>
        </div>

    </div>
</div>
