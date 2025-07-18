<?php
namespace app\models;
use Yii;
use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public $visitors_count;
    public $tracks_count;
    public $creator_username;

    public static function tableName()
    {
        return '{{%posts}}';
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getVisitors()
    {
        return $this->hasMany(PostsVisitors::class, ['id_post' => 'id']);
    }

    public function getTracks()
    {
        return $this->hasMany(PostsTrack::class, ['id_post' => 'id']);
    }

    public function getVisitorsCount()
    {
        return $this->visitors_count ?? $this->getVisitors()->count();
    }

    public function getTracksCount()
    {
        return $this->tracks_count ?? $this->getTracks()->count();
    }

    public function getCreatorUsername()
    {
        return $this->creator_username ?? ($this->creator ? $this->creator->username : 'Unknown');
    }
}