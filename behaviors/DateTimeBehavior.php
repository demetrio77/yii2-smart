<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class DateTimeBehavior extends Behavior
{
	public $attributes = [];
	
	public function init()
	{
		if (is_string($this->attributes)) {
			$this->attributes = [ $this->attributes ];
		}
	}

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'process'
		];
	}

	public function process( $event )
	{
		$model = $this->owner;
		foreach ($this->attributes as $attribute) {
			$dateTime = $model->{$attribute};
			$model->{$attribute} = 0;
			preg_match('/(\d\d)\.(\d\d)\.(\d{4}) (\d\d):(\d\d)/', $dateTime, $d);
			if (count($d) == 6 ) {
				$day = intval($d[1]);
				$month = intval($d[2]);
				$year = intval($d[3]);
				$hour = intval($d[4]);
				$min = intval($d[5]);
				$model->{$attribute} = mktime($hour,$min,0,$month,$day,$year);
			}
		}
	}
}