<?php

use yii\db\Migration;
use bupy7\activerecord\history\Module;

/**
 * Class m200726_112403_addIp
 */
class m200726_112403_addIp extends Migration
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
        $this->addColumn($this->tableName, 'ip', ' VARCHAR(15) DEFAULT NULL AFTER created_by');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'initAttributes');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200726_112403_addIp cannot be reverted.\n";

        return false;
    }
    */
}
