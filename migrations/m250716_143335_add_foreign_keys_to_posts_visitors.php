<?php
use yii\db\Migration;

class m250716_143335_add_foreign_keys_to_posts_visitors extends Migration
{
    public function safeUp()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->addForeignKey(
            'fk-posts_visitors-id_post',
            '{{%posts_visitors}}',
            'id_post',
            '{{%posts}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-posts_visitors-id_visitor',
            '{{%posts_visitors}}',
            'id_visitor',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-posts_visitors-id_post', '{{%posts_visitors}}');
        $this->dropForeignKey('fk-posts_visitors-id_visitor', '{{%posts_visitors}}');
    }
}