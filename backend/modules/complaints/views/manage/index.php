<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $fileStorage common\components\storage\Storage */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($post) {
                    return Html::a($post->id, ['view', 'id' => $post->id]);
                },
            ],
            'description:ntext',
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function ($post) {
                    return Html::img($post->getImage(), ['width' => '130px']);
                },
            ],
            'user_id',
            'created_at:datetime',
            'complaints',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {approve} {delete}',
                'buttons' => [
                    'approve' => function ($url, $post) {
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', ['approve', 'id' => $post->id]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
