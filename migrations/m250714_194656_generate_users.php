<?php
use yii\db\Migration;

class m250714_194656_generate_users extends Migration
{
    public function up()
    {
        while (ob_get_level()) {
            ob_end_flush();
        }
        ob_implicit_flush(true);

        
        if ($this->db->schema->getTableSchema('{{%user}}') === null) {
            $this->createTable('{{%user}}', [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'email' => $this->string()->notNull()->unique(),
                'password_hash' => $this->string()->notNull(),
                'auth_key' => $this->string(32)->notNull(),
                'confirmed_at' => $this->integer(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ], 'ENGINE=InnoDB');
            $this->createIndex('idx_user_username', '{{%user}}', 'username', true);
        } else {
            $this->execute('TRUNCATE TABLE {{%user}}');
        }

        $passwordHash = Yii::$app->security->generatePasswordHash('password');
        $batchSize = 20000;
        $rows = [];
        $startTime = microtime(true);

        $this->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
        $this->db->createCommand('SET UNIQUE_CHECKS=0')->execute();

        for ($i = 1; $i <= 100000; $i++) {
            $rows[] = [
                'user_' . $i,
                'user_' . $i . '@example.com',
                $passwordHash,
                Yii::$app->security->generateRandomString(),
                time(),
                time(),
                time(),
            ];

            if ($i % $batchSize === 0) {
                $this->batchInsert('{{%user}}',
                    ['username', 'email', 'password_hash', 'auth_key', 'confirmed_at', 'created_at', 'updated_at'],
                    $rows
                );
                echo "Inserted $i users\n";
                $rows = [];
            }
        }

        if (!empty($rows)) {
            $this->batchInsert('{{%user}}',
                ['username', 'email', 'password_hash', 'auth_key', 'confirmed_at', 'created_at', 'updated_at'],
                $rows
            );
        }

        $this->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
        $this->db->createCommand('SET UNIQUE_CHECKS=1')->execute();

        $endTime = microtime(true);
        echo "Inserted 100000 users in " . round($endTime - $startTime, 2) . " seconds\n";
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}