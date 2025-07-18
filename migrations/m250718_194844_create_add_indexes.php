<?php
use yii\db\Migration;


class m250718_194844_create_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_posts_created_by', '{{%posts}}', 'created_by');
        $this->createIndex('idx_posts_visitors_id_post', '{{%posts_visitors}}', 'id_post');
        $this->createIndex('idx_posts_track_id_post', '{{%posts_track}}', 'id_post');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_posts_created_by', '{{%posts}}');
        $this->dropIndex('idx_posts_visitors_id_post', '{{%posts_visitors}}');
        $this->dropIndex('idx_posts_track_id_post', '{{%posts_track}}');
    }
}