<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class UpDownBehavior extends Behavior
{
    /*
     * @property owner Model
     */
    
    public $ordinalField = 'ordinal';
    public $subCategoryField;
    public $ordinalOnCreate = 'last';//first
    
    public function moveUp()
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
        	
        	$ordinal = $model->{$this->ordinalField};
        	$where = '1';
        	$params = [];
        	
        	if ($this->subCategoryField) {
        		$where .= ' AND '.$this->subCategoryField.'=:f';
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
    }
}