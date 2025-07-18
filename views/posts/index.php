<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Posts';
?>

<h1>Posts</h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID',
        ],
        [
            'attribute' => 'name',
            'label' => 'Name',
        ],
        [
            'attribute' => 'visitorsCount',
            'label' => 'Views',
            'value' => function($model) {
                return $model->getVisitorsCount();
            },
        ],
        [
            'attribute' => 'tracksCount',
            'label' => 'Tracks',
            'value' => function($model) {
                return $model->getTracksCount();
            },
        ],
        [
            'attribute' => 'created_by',
            'label' => 'Creator',
            'value' => function($model) {
                return $model->getCreatorUsername();
            },
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Created At',
        ],
        [
            'format' => 'raw',
            'value' => function($model) {
                return Html::a('View', ['view', 'id' => $model->id]);
            },
        ],
    ],
]) ?>