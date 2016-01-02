<?php

namespace bupy7\activerecord\history\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\base\Event;
use bupy7\activerecord\history\Module;
use bupy7\activerecord\history\models\History as HistoryModel;
use yii\base\NotSupportedException;

/**
 * Behavior monitoring change the field value to model and saving their in storage.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.0.0
 */
class History extends Behavior
{
    /**
     * Event types of history to the AR object: when create model.
     */
    const EVENT_INSERT = 1;
    /**
     * Event types of history to the AR object: when update model.
     */
    const EVENT_UPDATE = 2;
    /**
     * Event types of history to the AR object: when delete model.
     */
    const EVENT_DELETE = 3;
    
    /**
     * @var array Events list than saved in storage.
     */
    public $allowEvents = [
        self::EVENT_INSERT,
        self::EVENT_UPDATE,
        self::EVENT_DELETE,
    ];
    /**
     * @var array List of attributes which not need track at updating. Apply only for `self::EVENT_UPDATE`.
     */
    public $skipAttributes = [];
    /**
     * @var array Mapping events between behavior and active record model.
     */
    protected $eventMap = [
        self::EVENT_INSERT => BaseActiveRecord::EVENT_AFTER_INSERT,
        self::EVENT_UPDATE => BaseActiveRecord::EVENT_AFTER_UPDATE,
        self::EVENT_DELETE => BaseActiveRecord::EVENT_AFTER_DELETE,
    ];  
    /**
     * @var Module Instance of history module class.
     */
    protected $module;
    
    /**
     * @inhertidoc
     */
    public function init()
    {
        parent::init();
        $this->module = Module::getInstance();
        $this->skipAttributes = array_fill_keys($this->skipAttributes, true);
    }
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        $events = [];
        foreach ($this->allowEvents as $name){
            $events[$this->eventMap[$name]] = 'saveHistory';
        }
        return $events;
    }
    
    /**
     * Process of saving history to storage.
     * @param Event $event
     */
    public function saveHistory(Event $event)
    {   
        $rowId = $this->getRowId();
        $tableName = $this->getTableName();
        $createdBy = $this->getCreatedBy();
        $createdAt = time();    
        
        $storage = new $this->module->storage;
        
        switch ($event->name) {
            case BaseActiveRecord::EVENT_AFTER_INSERT:
                $model = new HistoryModel([
                    'table_name' => $tableName,
                    'row_id' => $rowId,
                    'event' => HistoryModel::EVENT_INSERT,
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ]);
                $storage->add($model);
                break;
            
            case BaseActiveRecord::EVENT_AFTER_DELETE:
                $model = new HistoryModel([
                    'table_name' => $tableName,
                    'row_id' => $rowId,
                    'event' => HistoryModel::EVENT_DELETE,
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ]);
                $storage->add($model);
                break;
            
            case BaseActiveRecord::EVENT_AFTER_UPDATE:
                foreach ($event->changedAttributes as $name => $value) {
                    if ($value == $this->owner->$name || isset($this->skipAttributes[$name])) {
                        continue;
                    }
                    $model = new HistoryModel([
                        'table_name' => $tableName,
                        'row_id' => $rowId,
                        'field_name' => $name,
                        'old_value' => $value,
                        'new_value' => $this->owner->$name,
                        'event' => HistoryModel::EVENT_UPDATE,
                        'created_at' => $createdAt,
                        'created_by' => $createdBy,
                    ]);
                    $storage->add($model);
                }
                break;
        }     
        $storage->flush();
    }
    
    /**
     * Return primary key of attached model.
     * @return string
     * @throws NotSupportedException
     */
    protected function getPrimaryKey()
    {
        $primaryKey = $this->owner->primaryKey();
        if (count($primaryKey) == 1) {
            $primaryKey = array_shift($primaryKey);
        } else {
            throw new NotSupportedException('Composite primary key not supported.');
        } 
        return $primaryKey;
    }
    
    /**
     * Return table name of attached model.
     * @return string
     */
    protected function getTableName()
    {
        $owner = $this->owner;
        return $owner::tableName();
    }
    
    /**
     * Return user id which updated, created or deleted model.
     * @return integer
     */
    protected function getCreatedBy()
    {
        return $this->module->user->id;
    }
    
    /**
     * Return row id which updated, created or deleted.
     * @return integer
     */
    protected function getRowId()
    {
        $primaryKey = $this->getPrimaryKey();
        return $this->owner->$primaryKey;
    }
}

