<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PostSearch extends Post
{
    public function rules()
    {
        return [];
    }

    public function search($params)
    {
        $query = Post::find()->leftJoin('{{%user}}', '{{%user}}.id = {{%posts}}.created_by');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'created_at',
                    'created_by' => [
                        'asc' => ['{{%user}}.username' => SORT_ASC],
                        'desc' => ['{{%user}}.username' => SORT_DESC],
                    ],
                    'visitorsCount' => [
                        'asc' => ['(SELECT COUNT(*) FROM {{%posts_visitors}} WHERE {{%posts_visitors}}.id_post = {{%posts}}.id)' => SORT_ASC],
                        'desc' => ['(SELECT COUNT(*) FROM {{%posts_visitors}} WHERE {{%posts_visitors}}.id_post = {{%posts}}.id)' => SORT_DESC],
                    ],
                    'tracksCount' => [
                        'asc' => ['(SELECT COUNT(*) FROM {{%posts_track}} WHERE {{%posts_track}}.id_post = {{%posts}}.id)' => SORT_ASC],
                        'desc' => ['(SELECT COUNT(*) FROM {{%posts_track}} WHERE {{%posts_track}}.id_post = {{%posts}}.id)' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        return $dataProvider;
    }
}