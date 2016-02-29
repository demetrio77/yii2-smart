<?php

namespace demetrio77\smartadmin\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use Yii;

/*use Closure;
use yii\base\Event;
use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
*/

/**
 * UserIdBehavior automatically fills the specified attributes with the current user id.
 */

class UserIdBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive user id value
     */
    public $userIdAttribute = 'uid_modified';
    public $value;
    public $userComponent='user';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->userIdAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->userIdAttribute,
            ];
        }
    }

    /**
     * {@inheritdoc}
     * return current user id value, when [[value]] is null.
     */
    protected function getValue($event)
    {
    	if (!isset(Yii::$app->{$this->userComponent})) {
    		return ;
    	}
    	
        if ($this->value === null) {
            return Yii::$app->{$this->userComponent}->id;
        }
        return parent::getValue($event);
    }
}
