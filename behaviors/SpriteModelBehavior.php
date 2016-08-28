<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use common\helpers\Domain;

class SpriteModelBehavior extends Behavior
{
	public $spriteWidth;
	public $spriteHeight;
	public $spriteAsset;
	public $spriteDomain;
	public $spriteFile;
	
	public function getSpriteUrl()
	{
		$Asset = Yii::createObject($this->spriteAsset);
		return $this->spriteDomain . $Asset->getUrl($this->spriteFile);
	}
}