<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use demetrio77\smartadmin\helpers\typograph\Typograph;

class TypografBehavior extends Behavior
{
    public $attributes = [];
    public $removeScenarios = ['update'];
    
    public function init()
    {
    	parent::init();
    	if (empty($this->attributes)) {
    		$this->attributes = [];
    	}
    	if (empty($this->removeScenarios)) {
    		$this->removeScenarios = [];
    	}
    	if (!is_array($this->attributes)) {
    		$this->attributes = [ $this->attributes ];
    	}
    	if (!is_array($this->removeScenarios)) {
    		$this->removeScenarios = [ $this->removeScenarios ];
    	}
    }
    
    public function events()
    {
    	return [
    	   ActiveRecord::EVENT_BEFORE_INSERT => 'typo',
    	   ActiveRecord::EVENT_BEFORE_UPDATE => 'typo',
    	   ActiveRecord::EVENT_AFTER_FIND => 'remove'
    	];
    }
    
    public function typo( $event )
    {
    	$model = $this->owner;
        foreach ($this->attributes as $attribute) {
        	$model->{$attribute} = Typograph::process($model->{$attribute});
   	 	}    	
	}
	
	public function remove( $event )
	{
		$model = $this->owner;
		if (in_array($model->scenario, $this->removeScenarios))
		{
			foreach ($this->attributes as $attribute) {
				$model->{$attribute} = html_entity_decode($model->{$attribute});
			}
		}
	}
}