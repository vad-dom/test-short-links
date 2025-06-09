<?php

use yii\db\Migration;

class m250608_005355_shortener extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('shortener_log_ip', [
            'id' => $this->primaryKey()->comment('ID'),
            'shortener_id' => $this->integer()->notNull()->comment('ID короткой ссылки'),
            'ip_address' => $this->string(45)->notNull()->comment('IP адрес'),
            'created_at' => $this
                ->timestamp()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Время создания'),
        ]);
        $this->createTable('shortener', [
            'id' => $this->primaryKey()->comment('ID'),
            'url' => $this->string(256)->notNull()->comment('Ссылка'),
            'shortened' => $this->string(16)->notNull()->unique()->comment('Короткая ссылка'),
            'clicks' => $this->integer()->notNull()->defaultValue(0)->comment('Количество переходов'),
        ]);

        $this->createIndex('idx-shortener_log_ip-shortener_id', 'shortener_log_ip', 'shortener_id');

        $this->addForeignKey(
            'fk-shortener_log_ip-shortener_id',
            'shortener_log_ip',
            'shortener_id',
            'shortener',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('shortener_log_ip');
        $this->dropTable('shortener');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250608_005355_add_log cannot be reverted.\n";

        return false;
    }
    */
}
