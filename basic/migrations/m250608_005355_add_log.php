<?php

use yii\db\Migration;

class m250608_005355_add_log extends Migration
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
        ]);
        $this->createTable('shortener_log_clicks', [
            'id' => $this->primaryKey()->comment('ID'),
            'shortener_id' => $this->integer()->notNull()->comment('ID короткой ссылки'),
            'clicks' => $this->integer()->notNull()->defaultValue(0)->comment('Количество переходов'),
        ]);

        $this->createIndex('idx-shortener_log_ip-shortener_id', 'shortener_log_ip', 'shortener_id');
        $this->createIndex(
            'idx-shortener_log_clicks-shortener_id',
            'shortener_log_clicks',
            'shortener_id',
            true
        );

        $this->addForeignKey(
            'fk-shortener_log_ip-shortener_id',
            'shortener_log_ip',
            'shortener_id',
            'yii2_shortener',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-shortener_log_clicks-shortener_id',
            'shortener_log_clicks',
            'shortener_id',
            'yii2_shortener',
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
        $this->dropTable('shortener_log_clicks');
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
