<?php

use yii\db\Migration;

/**
 * Handles the creation of table `auth`.
 */
class m180219_181407_create_auth_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk-auth-user_id-user-id', 'auth', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('auth');
    }
}
