<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Post;
use app\models\PostSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = Post::find()->with(['creator', 'visitors', 'tracks'])->where(['id' => $id])->one();
        $visitorsDataProvider = new ActiveDataProvider([
            'query' => \app\models\PostsVisitors::find()->where(['id_post' => $id]),
        ]);
        $tracksDataProvider = new ActiveDataProvider([
            'query' => \app\models\PostsTrack::find()->where(['id_post' => $id]),
        ]);

        return $this->render('view', [
            'model' => $model,
            'visitorsDataProvider' => $visitorsDataProvider,
            'tracksDataProvider' => $tracksDataProvider,
        ]);
    }
}