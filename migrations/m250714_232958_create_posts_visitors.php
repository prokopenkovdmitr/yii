<?php
use yii\db\Migration;

class m250714_232958_create_posts_visitors extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%posts_visitors}}') === null) {
            $this->createTable('{{%posts_visitors}}', [
                'id_post' => $this->integer()->notNull(),
                'id_visitor' => $this->integer()->notNull(),
                'view_at' => $this->dateTime()->notNull(),
                'PRIMARY KEY(id_post, id_visitor, view_at)',
            ], 'ENGINE=InnoDB');
        } else {
            $this->execute('TRUNCATE TABLE {{%posts_visitors}}');
        }

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute('SET UNIQUE_CHECKS=0');
        $this->execute('SET AUTOCOMMIT=0');
        $this->execute('ALTER TABLE {{%posts_visitors}} DISABLE KEYS');

        $indexExists = function ($indexName) {
            $result = $this->db->createCommand("SHOW INDEX FROM {{%posts_visitors}} WHERE Key_name = :indexName")
                ->bindValue(':indexName', $indexName)
                ->queryOne();
            return $result !== false;
        };
        if ($indexExists('idx_posts_visitors_post')) {
            $this->dropIndex('idx_posts_visitors_post', '{{%posts_visitors}}');
        }
        if ($indexExists('idx_posts_visitors_visitor')) {
            $this->dropIndex('idx_posts_visitors_visitor', '{{%posts_visitors}}');
        }

        $chunkSize = 5000;
        $postsCount = 1000000;
        $totalChunks = ceil($postsCount / $chunkSize);

        $users = (new \yii\db\Query())->select('id')->from('{{%user}}')->limit(100000)->column();
        if (empty($users)) {
            throw new \Exception('No users found');
        }

        $file = 'E:/mysql/Uploads/posts_visitors.csv';
        file_put_contents($file, '');
        $totalInserted = 0;
        $startTime = microtime(true);

        for ($chunkId = 0; $chunkId < $totalChunks; $chunkId++) {
            $posts = (new \yii\db\Query())
                ->select('id')
                ->from('{{%posts}}')
                ->offset($chunkId * $chunkSize)
                ->limit($chunkSize)
                ->column();

            if (empty($posts)) {
                continue;
            }

            $buffer = '';
            $totalToInsert = 0;
            echo "Processing chunk $chunkId of $totalChunks...\n";

            foreach ($posts as $postId) {
                $visitsPerPost = rand(100, 200);
                $totalToInsert += $visitsPerPost;
                $selectedUsers = array_rand($users, min($visitsPerPost, count($users)));
                if (!is_array($selectedUsers)) {
                    $selectedUsers = [$selectedUsers];
                }

                $usedTimestamps = [];
                foreach ($selectedUsers as $userIndex) {
                    $visitorId = $users[$userIndex];
                    $timestamp = $this->generateUniqueTimestamp($usedTimestamps, $postId, $visitorId);
                    $buffer .= "$postId,$visitorId,\"$timestamp\"\n";
                    $usedTimestamps[$postId][$visitorId][] = $timestamp;
                }
                unset($usedTimestamps[$postId]);
            }

            file_put_contents($file, $buffer, FILE_APPEND);
            $totalInserted += $totalToInsert;
            echo "Prepared $totalToInsert rows in chunk $chunkId\n";
            unset($usedTimestamps);
            gc_collect_cycles();
        }

        $this->execute("LOAD DATA INFILE '$file' INTO TABLE {{%posts_visitors}} FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' (id_post, id_visitor, view_at)");
        unlink($file);

        if (!$indexExists('idx_posts_visitors_post')) {
            $this->createIndex('idx_posts_visitors_post', '{{%posts_visitors}}', 'id_post');
        }
        if (!$indexExists('idx_posts_visitors_visitor')) {
            $this->createIndex('idx_posts_visitors_visitor', '{{%posts_visitors}}', 'id_visitor');
        }

        $this->execute('ALTER TABLE {{%posts_visitors}} ENABLE KEYS');
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
        $this->execute('SET UNIQUE_CHECKS=1');
        $this->execute('SET AUTOCOMMIT=1');

        $endTime = microtime(true);
        echo "Inserted ~{$totalInserted} rows for $postsCount posts in " . round($endTime - $startTime, 2) . " seconds\n";
    }

    private function generateUniqueTimestamp(&$usedTimestamps, $postId, $visitorId)
    {
        $now = time();
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $timestamp = date('Y-m-d H:i:s', rand(strtotime('2020-01-01'), $now));
            $attempt++;
            if (!isset($usedTimestamps[$postId][$visitorId]) || !in_array($timestamp, $usedTimestamps[$postId][$visitorId])) {
                return $timestamp;
            }
        } while ($attempt < $maxAttempts);

        return date('Y-m-d H:i:s');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_posts_visitors_visitor', '{{%posts_visitors}}');
        $this->dropIndex('idx_posts_visitors_post', '{{%posts_visitors}}');
        $this->dropTable('{{%posts_visitors}}');
    }
}