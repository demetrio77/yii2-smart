<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class YandexMapPointsAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/yandexmap';
	public $css = [];
	public $js = ['//api-maps.yandex.ru/2.1/?lang=ru_RU', 'yandexMapPoints.js'];
	public $depends = ['yii\web\JqueryAsset'];
}