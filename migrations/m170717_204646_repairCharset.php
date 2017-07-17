<?php

use yii\db\Migration;
use bupy7\activerecord\history\Module;

class m170717_204646_repairCharset extends Migration
{
    protected $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = Module::getInstance()->tableName;
    }

    public function up()
    {
        $this->execute('ALTER TABLE ' . $this->tableName . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
    }

    public function down()
    {
        echo 'Revert is not possible';
        return false;
    }

}
