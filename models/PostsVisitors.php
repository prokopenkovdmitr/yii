<?php
namespace app\models;

use yii\db\ActiveRecord;

class PostsVisitors extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%posts_visitors}}';
    }

    public static function primaryKey()
    {
        return ['id_post', 'id_visitor', 'view_at'];
    }

    public function rules()
    {
        return [
            [['id_post', 'id_visitor', 'view_at'], 'required'],
            [['id_post', 'id_visitor'], 'integer'],
            [['view_at'], 'safe'],
        ];
    }

    public function getVisitor()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'id_visitor']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'id_post']);
    }
}