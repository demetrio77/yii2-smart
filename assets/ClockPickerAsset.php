<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class ClockPickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/clockpicker';
	public $js = ['clockpicker.min.js'];	
	public $depends = ['yii\web\JqueryAsset'];	
}