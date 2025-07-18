<?php
use yii\db\Migration;

class m250714_232920_create_posts extends Migration
{
    public function up()
    {
        while (ob_get_level()) {
            ob_end_flush();
        }
        ob_implicit_flush(true);

        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'text' => $this->text()->notNull(),
            'fields' => $this->json(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'FOREIGN KEY (created_by) REFERENCES {{%user}} (id) ON DELETE CASCADE'
        ], 'ENGINE=InnoDB');

        $this->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
        $this->db->createCommand('SET UNIQUE_CHECKS=0')->execute();

        $users = (new \yii\db\Query())->select('id')->from('{{%user}}')->column();
        if (empty($users)) {
            throw new \Exception('No users found in the user table');
        }

        $file = 'E:/mysql/Uploads/posts.csv';
        file_put_contents($file, '');
        $totalRecords = 1000000;
        $recordsPerChunk = 50000; 
        $chunks = ceil($totalRecords / $recordsPerChunk);
        $startTime = microtime(true);

        for ($chunk = 0; $chunk < $chunks; $chunk++) {
            $buffer = ''; 
            for ($i = 0; $i < $recordsPerChunk && ($chunk * $recordsPerChunk + $i) < $totalRecords; $i++) {
                $name = 'Post ' . ($chunk * $recordsPerChunk + $i + 1);
                $text = 'Sample text';
                $fields = json_encode(['extra' => 'word_' . rand(1, 1000)]);
                $createdBy = $users[array_rand($users)];
                $createdAt = date('Y-m-d H:i:s', rand(strtotime('2025-01-01'), time()));
                $buffer .= "\"$name\",\"$text\",\"$fields\",$createdBy,\"$createdAt\"\n";
            }
            file_put_contents($file, $buffer, FILE_APPEND);
            echo "Processed chunk $chunk of $chunks\n";
            gc_collect_cycles();
        }

        $this->execute("LOAD DATA INFILE '$file' INTO TABLE {{%posts}} FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' (name, text, fields, created_by, created_at)");
        unlink($file);

        $this->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
        $this->db->createCommand('SET UNIQUE_CHECKS=1')->execute();

        $endTime = microtime(true);
        echo "Inserted $totalRecords posts in " . round($endTime - $startTime, 2) . " seconds\n";
    }

    public function down()
    {
        $this->dropTable('{{%posts}}');
    }
}