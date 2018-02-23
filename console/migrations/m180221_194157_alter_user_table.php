<?php

use yii\db\Migration;

/**
 * Class m180221_194157_alter_user_table
 */
class m180221_194157_alter_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn("{{%user}}", "about", $this->text());
        $this->addColumn("{{%user}}", "nickname", $this->string(70));
        $this->addColumn("{{%user}}", "picture", $this->string(250));
        $this->addColumn("{{%user}}", "type", $this->integer(3));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn("{{%user}}", "about");
        $this->dropColumn("{{%user}}", "nickname");
        $this->dropColumn("{{%user}}", "picture");
        $this->dropColumn("{{%user}}", "type");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180221_194157_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
