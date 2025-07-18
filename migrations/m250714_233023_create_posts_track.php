<?php
use yii\db\Migration;

class m250714_233023_create_posts_track extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%posts_track}}') === null) {
            $this->createTable('{{%posts_track}}', [
                'id' => $this->primaryKey(),
                'id_post' => $this->integer()->notNull(),
                'id_user' => $this->integer()->notNull(),
                'track_at' => $this->dateTime()->notNull(),
            ], 'ENGINE=InnoDB');
        } else {
            $this->execute('TRUNCATE TABLE {{%posts_track}}');
        }

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute('SET UNIQUE_CHECKS=0');
        $this->execute('SET AUTOCOMMIT=0');

        $posts = (new \yii\db\Query())->select('id')->from('{{%posts}}')->limit(1000000)->column();
        $users = (new \yii\db\Query())->select('id')->from('{{%user}}')->limit(100000)->column();
        if (empty($posts) || empty($users)) {
            throw new \Exception('No posts or users found');
        }

        $chunkSize = 5000;
        $totalChunks = ceil(count($posts) / $chunkSize);
        $now = time();
        $file = 'E:/mysql/Uploads/posts_track.csv';
        file_put_contents($file, '');
        $totalInserted = 0;

        for ($chunkId = 0; $chunkId < $totalChunks; $chunkId++) {
            $chunkPosts = array_slice($posts, $chunkId * $chunkSize, $chunkSize);
            if (empty($chunkPosts)) {
                continue;
            }

            $buffer = '';
            $totalToInsert = 0;
            foreach ($chunkPosts as $postId) {
                $trackCount = rand(10, 24);
                $selectedUsers = array_rand($users, $trackCount);
                if (!is_array($selectedUsers)) {
                    $selectedUsers = [$selectedUsers];
                }

                foreach ($selectedUsers as $userIndex) {
                    $userId = $users[$userIndex];
                    $timestamp = date('Y-m-d H:i:s', rand(strtotime('2020-01-01'), $now));
                    $buffer .= "$postId,$userId,\"$timestamp\"\n";
                    $totalToInsert++;
                }
            }

            file_put_contents($file, $buffer, FILE_APPEND);
            $totalInserted += $totalToInsert;
            echo "Prepared $totalToInsert rows in chunk $chunkId of $totalChunks\n";
            gc_collect_cycles();
        }

        $this->execute("LOAD DATA INFILE '$file' INTO TABLE {{%posts_track}} FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' (id_post, id_user, track_at)");
        unlink($file);

        $this->execute('SET FOREIGN_KEY_CHECKS=1');
        $this->execute('SET UNIQUE_CHECKS=1');
        $this->execute('SET AUTOCOMMIT=1');

        $this->addForeignKey(
            'fk-posts_track-id_post',
            '{{%posts_track}}',
            'id_post',
            '{{%posts}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-posts_track-id_user',
            '{{%posts_track}}',
            'id_user',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        echo "Inserted ~{$totalInserted} rows\n";
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-posts_track-id_user', '{{%posts_track}}');
        $this->dropForeignKey('fk-posts_track-id_post', '{{%posts_track}}');
        $this->dropTable('{{%posts_track}}');
    }
}