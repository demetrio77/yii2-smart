<?php

namespace demetrio77\smartadmin\assets;

use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
	public static function getPublishedUrl() {
		$obj = new static();
		return \Yii::$app->assetManager->getPublishedUrl($obj->sourcePath).DIRECTORY_SEPARATOR;
	}
	
	public static function getUrl($fileName){
		return static::getPublishedUrl().$fileName;
	}
}