<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use demetrio77\smartadmin\helpers\typograph\Typograph;

class TypografBehavior extends Behavior
{
    public $attributes = [];
    public $html = [];
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
    	if (empty($this->html)) {
    		$this->html = [];
    	}
    	if (!is_array($this->html)) {
    		$this->html = [ $this->html ];
    	}
    }
    
    public function events()
    {
    	return [
    	   ActiveRecord::EVENT_BEFORE_VALIDATE => 'addTypo'
    	];
    }
    
    public function addTypo( $event = [] )
    {
    	$model = $this->owner;
        foreach ($this->attributes as $attribute) {
        	$model->{$attribute} = Typograph::process( htmlspecialchars( $model->{$attribute} ));
   	 	}
   	 	foreach ($this->html as $attribute) {
   	 		$model->{$attribute} = Typograph::process( $model->{$attribute} );
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