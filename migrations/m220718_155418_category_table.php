<?php

use yii\db\Migration;

/**
 * Class m220718_155418_category_table
 */
class m220718_155418_category_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull(),
            'image' => $this->string()->null(),
            'parent_id' => $this->integer()->null(),
            'sort_order' => $this->integer()->defaultValue(0),
            'description' => $this->text()->null(),

            'updated_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull()
        ], $tableOptions);

        $this->createIndex('parent_id', '{{%category}}', 'parent_id', false);
        $this->addForeignKey("category_category_fk", "{{%category}}", "parent_id", "{{%category}}", "id", 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%category}}');
    }
}
