<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class ColorPickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/bootstrap-colorpicker-2.5.3/dist';
	public $js  = ['js/bootstrap-colorpicker.min.js'];
	public $css = ['css/bootstrap-colorpicker.min.css'];
	public $depends = ['yii\web\JqueryAsset'];
}