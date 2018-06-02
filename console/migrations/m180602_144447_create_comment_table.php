<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m180602_144447_create_comment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(7)->notNull(),
            'post_id' => $this->integer(7)->notNull(),
            'parent_id' => $this->integer(7)->defaultValue(0),
            'text' => $this->text(),
            'username' => $this->string(70),
            'status' => $this->smallInteger(1)->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('comment');
    }
}
