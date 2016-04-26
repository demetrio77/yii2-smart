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
        	$model->{$attribute} = Typograph::fast_apply($model->{$attribute}, [	
				'Text.paragraphs' => 'off',
				'Text.breakline'=>'off',
				'OptAlign.oa_oquote' => 'off',
				'OptAlign.oa_obracket_coma' => 'off',
				'Quote.quotation'=>'off',
				'Text.auto_links'=>'off']
			);
   	 	}    	
	}
	
	public function remove( $event )
	{
		$model = $this->owner;
		if (in_array($model->scenario, $this->removeScenarios))
		{
			foreach ($this->attributes as $attribute) {
				$model->{$attribute} = htmlspecialchars_decode($model->{$attribute});
			}
		}
	}
}