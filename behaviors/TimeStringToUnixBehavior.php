<?php

namespace demetrio77\smartadmin\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use demetrio77\smartadmin\helpers\DateHelper;

class TimeStringToUnixBehavior extends Behavior
{
	public $attributes = [];
	
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'process',
		];
	}
	
	public function process( $event )
	{
		$model = $this->owner;
		
		foreach ($this->attributes as $attribute) {
			$model->{$attribute} = DateHelper::toUnix($model->{$attribute});
		}
	}
}