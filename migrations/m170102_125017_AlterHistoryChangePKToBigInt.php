<?php

use yii\db\Schema;
use yii\db\Migration;
use bupy7\activerecord\history\Module;

class m170102_125017_AlterHistoryChangePKToBigInt extends Migration
{
    protected $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = Module::getInstance()->tableName;
    }

    public function up()
    {
        $this->alterColumn($this->tableName, 'id', ' BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        $this->alterColumn($this->tableName, 'id', ' INT NOT NULL AUTO_INCREMENT');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
