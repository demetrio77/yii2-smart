<?php

namespace demetrio77\smartadmin\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

class JsonFieldBehavior extends AttributeBehavior
{
    //public $encodeBeforeValidation = true;

    /*public function events()
    {
        return [
            BaseActiveRecord::EVENT_INIT            => ,
            BaseActiveRecord::EVENT_AFTER_FIND      => ,
            BaseActiveRecord::EVENT_BEFORE_INSERT   => ,
            BaseActiveRecord::EVENT_BEFORE_UPDATE   => ,
            BaseActiveRecord::EVENT_AFTER_INSERT    => ,
            BaseActiveRecord::EVENT_AFTER_UPDATE    => ,
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => ,
            BaseActiveRecord::EVENT_AFTER_VALIDATE  => ,
        ];
    }*/

    public $asArray = true;
    private $_oldAttributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encodeAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encodeAttributes',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'decodeAttributes',
        ];
    }

    public function encodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_oldAttributes[$attribute])) {
                $this->owner->setOldAttribute($attribute, $this->_oldAttributes[$attribute]);
            }
            $this->owner->$attribute = $this->owner->$attribute ? Json::encode($this->owner->$attribute) : null;
        }
    }

    public function decodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->_oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);
            $value = Json::decode($this->owner->$attribute, $this->asArray);
            $this->owner->setAttribute($attribute, $value);
            $this->owner->setOldAttribute($attribute, $value);
        }
    }
}
