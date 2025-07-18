<?php
namespace app\models;

use yii\db\ActiveRecord;

class PostsTrack extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%posts_track}}';
    }

    public function getUser()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'id_user']);
    }
}