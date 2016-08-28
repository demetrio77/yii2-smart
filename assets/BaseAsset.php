<?php

namespace demetrio77\smartadmin\assets;

use Yii;
use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
	public static function getPublishedUrl() 
	{
		$AssetManager = Yii::$app->get('assetManager', false);
		$obj = new static();
		return $AssetManager ? $AssetManager->getPublishedUrl($obj->sourcePath).DIRECTORY_SEPARATOR : '';
	}
	
	public static function getUrl($fileName)
	{
		return static::getPublishedUrl().$fileName;
	}
}