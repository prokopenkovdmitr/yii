<?php
use yii\db\Migration;

class m250717_205816_add_admin_user extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%user}}', [
            'id' => 100001,
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('password'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'confirmed_at' => time(),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%user}}', ['username' => 'admin']);
    }
}