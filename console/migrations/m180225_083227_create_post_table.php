<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post`.
 */
class m180225_083227_create_post_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('post', [
            'id' => $this->primaryKey(),
            'description' => $this->text(),
            'filename' => $this->string(64)->notNull(),
            'user_id' => $this->integer(7)->notNull(),
            'created_at' => $this->integer(11)->notNull()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('post');
    }
}
