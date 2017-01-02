<?php

use yii\db\Schema;
use yii\db\Migration;
use bupy7\activerecord\history\Module;

class m170102_114524_AlterHistoryAddInitAttributes extends Migration
{
    protected $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = Module::getInstance()->tableName;
    }

    public function up()
    {
        $tableOptions = null;
        $this->addColumn($this->tableName, 'initAttributes', ' LONGTEXT DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'initAttributes');
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
