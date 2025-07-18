<?php
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = $model->name;
?>

<h1><?= Html::encode($model->name) ?></h1>

<p><strong>Text:</strong> <?= Html::encode($model->text) ?></p>
<p><strong>Created by:</strong> <?= $model->creator ? Html::encode($model->creator->username) : 'Unknown' ?></p>
<p><strong>Created at:</strong> <?= Html::encode($model->created_at) ?></p>

<h2>Visitors</h2>
<?= GridView::widget([
    'dataProvider' => $visitorsDataProvider,
    'columns' => [
        [
            'label' => 'Visitor',
            'value' => function($model) {
                return $model->visitor ? $model->visitor->username : 'Unknown';
            }
        ],
        ['attribute' => 'view_at'],
    ],
]) ?>

<h2>Tracks</h2>
<?= GridView::widget([
    'dataProvider' => $tracksDataProvider,
    'columns' => [
        [
            'label' => 'User',
            'value' => function($model) {
                return $model->user ? $model->user->username : 'Unknown';
            }
        ],
        ['attribute' => 'track_at'],
    ],
]) ?>