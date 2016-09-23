<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use demetrio77\smartadmin\helpers\typograph\Typograph;

class TypografBehavior extends Behavior
{
    public $attributes = [];
    private static $fields = [];
    
    public function init()
    {
    	parent::init();
    	if (empty($this->attributes)) {
    		$this->attributes = [];
    	}
    	if (!is_array($this->attributes)) {
    		$this->attributes = [ $this->attributes ];
    	}
    }
    
    public function events()
    {
    	return [
    	   ActiveRecord::EVENT_BEFORE_INSERT => 'addTypo',
    	   ActiveRecord::EVENT_BEFORE_UPDATE => 'addTypo'
    	];
    }
    
    public function addTypo( $event = [] )
    {
    	$model = $this->owner;
        foreach ($this->attributes as $attribute) {
        	$model->{$attribute} = Typograph::process($model->{$attribute});
   	 	}    	
	}
	
	public function removeTypo($fields=[], $except=[])
	{
		if ($fields && !is_array($fields)) {
			$fields = [$fields];
		}
		if ($except && !is_array($except)) {
			$except = [$except];
		}
		
		$model = $this->owner;
		foreach ($fields ? $fields : $this->attributes as $attribute) {
			if (!in_array($attribute, $except)) {
				$model->{$attribute} = Typograph::remove($model->{$attribute});
			}
		}
		return $model;
	}
}