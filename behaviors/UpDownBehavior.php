<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class UpDownBehavior extends Behavior
{
    /**
     * @var string name owner attribute, which will store position value.
     * This attribute should be an integer.
     */
	public $ordinalField = 'ordinal';
    /**
     * @var array list of owner attribute names, which values split records into the groups,
     * which should have their own positioning.
     * Example: `['group_id', 'category_id']`
     */
	public $subCategoryField = [];
	
    public $ordinalOnCreate = 'last';//first
    
     /**
     * @var integer position value, which should be applied to the model on its save.
     * Internal usage only.
     */
    private $positionOnSave;
    
    /**
     * Moves owner record by one position towards the start of the list.
     * @return boolean movement successful.
     */
    
    public function init()
    {
    	if (is_string($this->subCategoryField)) {
    		$this->subCategoryField = [ $this->subCategoryField ];
    	}
    	return parent::init();
    }
    
    public function moveUp()
    {
    	$ordinalField = $this->ordinalField;
    	
    	/* @var $previousRecord BaseActiveRecord */
    	$previousRecord = $this->owner->find()
    			->andWhere($this->createGroupConditionAttributes())
    			->andWhere([$ordinalField => ($this->owner->$ordinalField - 1)])
    			->one();
    	
    	if (empty($previousRecord)) {
    		return false;
    	}
    	
    	$previousRecord->updateAttributes([
    		$ordinalField => $this->owner->$ordinalField
    	]);
    
    	$this->owner->updateAttributes([
    		$ordinalField => $this->owner->$ordinalField - 1
    	]);
    	
    	return true;
    }
    /**
     * Moves owner record by one position towards the end of the list.
     * @return boolean movement successful.
     */
    public function moveDown()
    {
    	$ordinalField = $this->ordinalField;
    	
    	/* @var $nextRecord BaseActiveRecord */
    	$nextRecord = $this->owner->find()
    			->andWhere($this->createGroupConditionAttributes())
    			->andWhere([$ordinalField => ($this->owner->$ordinalField + 1)])
    			->one();
    	
    	if (empty($nextRecord)) {
    		return false;
    	}
    	
    	$nextRecord->updateAttributes([
    		$ordinalField => $this->owner->$ordinalField
    	]);
    	
    	$this->owner->updateAttributes([
    		$ordinalField => $this->owner->getAttribute($ordinalField) + 1
    	]);
    	
    	return true;
    }
    /**
     * Moves owner record to the start of the list.
     * @return boolean movement successful.
     */
    public function moveFirst()
    {
    	$ordinalField = $this->ordinalField;
    	if ($this->owner->$ordinalField == 1) {
    		return false;
    	}
    	
    	$this->owner->updateAllCounters(
    		[
    			$ordinalField => +1
    		],
    		[
    			'and',
    			$this->createGroupConditionAttributes(),
    			['<', $ordinalField, $this->owner->$ordinalField]
    		]
    	);
    	
    	$this->owner->updateAttributes([
    		$ordinalField => 1
    	]);
    	
    	return true;
    }
    /**
     * Moves owner record to the end of the list.
     * @return boolean movement successful.
     */
    public function moveLast()
    {
    	$ordinalField = $this->ordinalField;
    	$recordsCount = $this->countGroupRecords();
    	
    	if ($this->owner->getAttribute($ordinalField) == $recordsCount) {
    		return false;
    	}
    	$this->owner->updateAllCounters(
    		[
    			$ordinalField => -1
    		],
    		[
    			'and',
    			$this->createGroupConditionAttributes(),
    			['>', $ordinalField, $this->owner->$ordinalField]
    		]
    	);
    	
    	$this->owner->updateAttributes([
    		$ordinalField => $recordsCount
    	]);
    	
    	return true;
    }
    /**
     * Moves owner record to the specific position.
     * If specified position exceeds the total number of records,
     * owner will be moved to the end of the list.
     * @param integer $position number of the new position.
     * @return boolean movement successful.
     */
    public function moveTo($position)
    {
    	if (!is_numeric($position) || $position < 0) {
    		return false;
    	}
    	
    	$ordinalField = $this->ordinalField;
    	$oldRecord = $this->owner->findOne($this->owner->getPrimaryKey());
    	$oldRecordPosition = $oldRecord->$ordinalField;
    	
    	if ($oldRecordPosition == $position) {
    		return true;
    	}
    	
    	if ($position < $oldRecordPosition) {
    		// Move Up:
    		$this->owner->updateAllCounters(
    			[
    				$ordinalField => +1
    			],
    			[
    				'and',
    				$this->createGroupConditionAttributes(),
    				['>=', $ordinalField, $position],
    				['<', $ordinalField, $oldRecord->$ordinalField],
    			]
    		);
    		
    		$this->owner->updateAttributes([
    			$ordinalField => $position
    		]);
    		
    		return true;    		
    	} else {
    		// Move Down:
    		$recordsCount = $this->countGroupRecords();
    		if ($position >= $recordsCount) {
    			return $this->moveLast();
    		}
    		$this->owner->updateAllCounters(
    			[
    				$ordinalField => -1
    			],
    			[
    				'and',
    				$this->createGroupConditionAttributes(),
    				['>', $ordinalField, $oldRecord->$ordinalField],
    				['<=', $ordinalField, $position],
    			]
    		);
    		$this->owner->updateAttributes([
    			$ordinalField => $position
    		]);
    		
    		return true;
    	}
    }
    /**
     * Creates array of group attributes with their values.
     * @see subCategoryField
     * @return array attribute conditions.
     */
    protected function createGroupConditionAttributes()
    {
    	$condition = [];
    	if (!empty($this->subCategoryField)) {
    		foreach ($this->subCategoryField as $attribute) {
    			$condition[$attribute] = $this->owner->$attribute;
    		}
    	}
    	return $condition;
    }
    /**
     * Finds the number of records which belongs to the group of the owner.
     * @see subCategoryField
     * @return integer records count.
     */
    protected function countGroupRecords()
    {
    	$query = $this->owner->find();
    	if (!empty($this->subCategoryField)) {
    		$query->andWhere($this->createGroupConditionAttributes());
    	}
    	return $query->count();
    }
    
    // Events :
    /**
     * @inheritdoc
     */
     public function events()
     {
    	return [
    		BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
    		BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
    		BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
    		BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
    		BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
     }
     
     /**
     * Handles owner 'beforeInsert' owner event, preparing its positioning.
     * @param ModelEvent $event event instance.
     */
     public function beforeInsert($event)
     {
		$ordinalField = $this->ordinalField;
     	$ordinal = $this->owner->$ordinalField;
     	
     	if ($this->ordinalOnCreate =='first') {
     		$this->positionOnSave = 0;
     	}
     	elseif ( is_numeric($ordinal) ) {
     		$this->positionOnSave = $ordinal;
     	}
     	
     	$this->owner->$ordinalField = $this->countGroupRecords();
     }
     
     /**
     * Handles owner 'beforeInsert' owner event, preparing its possible re-positioning.
     * @param ModelEvent $event event instance.
     */
     public function beforeUpdate($event)
     {
     	$ordinalField = $this->ordinalField;
    	$isNewGroup = false;
     
     	foreach ($this->subCategoryField as $groupAttribute) {
     		if ($this->owner->isAttributeChanged($groupAttribute, false)) {
     			$isNewGroup = true;
     			break;
     		}
    	}
    
    	if ($isNewGroup) {
    		$oldRecord = $this->owner->findOne($this->owner->getPrimaryKey());
    		$oldRecord->moveLast();
    	
    		$this->positionOnSave = $this->owner->$ordinalField;
    		$this->owner->$ordinalField = $this->countGroupRecords() + 1;
    	} 
    	else {
    		if ($this->owner->isAttributeChanged($ordinalField, false)) {
    			$this->positionOnSave = $this->owner->$ordinalField;
    			$this->owner->$ordinalField = $this->owner->getOldAttribute($ordinalField);
    		}
    	}
    }
    
    /**
    * This event raises after owner inserted or updated.
    * It applies previously set [[positionOnSave]].
    * This event supports other functionality.
    * @param ModelEvent $event event instance.
    */
    public function afterSave($event)
    {
    	if ($this->positionOnSave !== null) {
   			$this->moveTo($this->positionOnSave);
    	}
    	$this->positionOnSave = null;
    }
        
    public function afterDelete ( $event )
    {
    	$ordinalField = $this->ordinalField;
    	$ordinal = $this->owner->$ordinalField;
    	
    	$this->owner->updateAllCounters(
    		[
    			$ordinalField => -1
    		],
    		[
    			'and',
    			$this->createGroupConditionAttributes(),
    			['>', $ordinalField, $ordinal]
    		]
    	);
	}

    /*public function moveUp()
    {
        $model = $this->owner;
        $ordinal = $model->{$this->ordinalField} ;
        if ($ordinal >0) {
            $find = [ $this->ordinalField => $ordinal-1 ];
            if ( $this->subCategoryField ) {
                $find [ $this->subCategoryField ] = $model->{$this->subCategoryField} ;
            }
            if ( ( $prev = $model::findOne( $find ) ) !== null ) {
                $prev->{$this->ordinalField}  = $ordinal;
                $model->{$this->ordinalField} = $ordinal-1;
                $prev->save();
                $model->save();
            }
        }
    }
    
    public function moveDown()
    {
        $model = $this->owner;
        $ordinal = $model->{$this->ordinalField} ;
        
        $find = $this->subCategoryField ? [ $this->subCategoryField => $model->{$this->subCategoryField} ] : [] ;
        $cnt = $model::find( $find )->count();
        
        if ($ordinal+1 < $cnt ) {
            
            $find [ $this->ordinalField ] = $ordinal+1;
            if ( ( $next = $model::findOne( $find ) ) !== null ) {
            	$next->{$this->ordinalField}  = $ordinal;
            	$model->{$this->ordinalField} = $ordinal+1;
            	$next->save();
            	$model->save();
            }
        }
    }
    
    public function moveTo( $newOrdinal )
    {
    	$model = $this->owner;
    	$ordinal = $model->{$this->ordinalField};
    	if ( $newOrdinal > $ordinal ) {
    		$where = '('.$this->ordinalField.' <= :newOrdinal) AND ('.$this->ordinalField.' > :ordinal )';
    		$params = [':newOrdinal' => $newOrdinal, ':ordinal' => $ordinal ];
    		if ($this->subCategoryField) {
    			$where .= ' AND ('.$this->subCategoryField.' = :f )';
    			$params['f'] = $model->{$this->subCategoryField};
    		}
    		$model->updateAllCounters([ $this->ordinalField => -1 ], $where, $params);
    		$model->{$this->ordinalField} = $newOrdinal;
    		return $model->save();
    	}
    	elseif ( $newOrdinal < $ordinal ) {
    		$where = '('.$this->ordinalField.' < :ordinal) AND ('.$this->ordinalField.' >= :newOrdinal )';
    		$params = [':newOrdinal' => $newOrdinal, ':ordinal' => $ordinal ];
    		
    		if ($this->subCategoryField) {
    			$where .= ' AND ('.$this->subCategoryField.' = :f )';
    			$params['f'] = $model->{$this->subCategoryField};
    		}
    		$model->updateAllCounters([ $this->ordinalField => 1 ], $where, $params);
    		$model->{$this->ordinalField} = $newOrdinal;
    		return $model->save();
    	}
    	return false;
    }
    
    public function events() 
    {
        return [
	       ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
	       ActiveRecord::EVENT_AFTER_DELETE  => 'afterDelete' ,
	       ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }
    
    public function beforeInsert( $event )
    {
        $model = $this->owner;
        $find = $this->subCategoryField ? [ $this->subCategoryField => $model->{$this->subCategoryField} ] : [] ;
        if ( $this->ordinalOnCreate == 'last') {
        	$model->{$this->ordinalField} = $model::find()->where( $find )->count();
        }
        elseif ( $this->ordinalOnCreate == 'first' ) {
        	$model->{$this->ordinalField} = 0;
        	
        	$where = '1';
        	$params = [];
        	
        	if ($this->subCategoryField) {
        		$where .= ' AND '.$this->subCategoryField.'=:f';
        		$params [':f'] = $model->{$this->subCategoryField};
        	}
        	$model->updateAllCounters([ $this->ordinalField => 1 ], $where, $params );
        }
        elseif ( $this->ordinalOnCreate == 'asIs' ) {
        	$ordinal = $model->{$this->ordinalField};
        	
        	$where = '('.$this->ordinalField . ' >= :ordinal) ';
        	$params = [':ordinal' => $ordinal];
        	
        	if ($this->subCategoryField) {
        		$where .= ' AND ('.$this->subCategoryField.'=:f)';
        		$params [':f'] = $model->{$this->subCategoryField};
        	}
        	
			$model->updateAllCounters([ $this->ordinalField => 1 ], $where, $params );
        }
    }
    
    public function beforeUpdate( $event ) 
    {
        if ( $this->subCategoryField ) {
            $model = $this->owner;
            if ( $model->oldAttributes[ $this->subCategoryField ] != $model->{$this->subCategoryField}) {
                //у старых сдвинуть вверх
                $ordinal = $model->{$this->ordinalField};
                $where = $this->ordinalField.'>:ordinal AND '.$this->subCategoryField.'=:f';
                $params = [':ordinal' => $ordinal, ':f'  => $model->oldAttributes[ $this->subCategoryField ] ];
                $model->updateAllCounters([ $this->ordinalField => -1 ], $where, $params );
                //у новых поместить в конец
                $model->{$this->ordinalField} = $model::find()->where( [ $this->subCategoryField => $model->{$this->subCategoryField} ] )->count();
            }   
        }
    }
    
    public function afterDelete ( $event )
    {
        $model = $this->owner;
        $ordinal = $model->{$this->ordinalField};
        $where = $this->ordinalField.'>:ordinal';
        $params = [':ordinal' => $ordinal];
        
        if ($this->subCategoryField) {
            $where .= ' AND '.$this->subCategoryField.'=:f';
            $params [':f'] = $model->{$this->subCategoryField};
        }
        $model->updateAllCounters([ $this->ordinalField => -1 ], $where, $params );
    }*/
}