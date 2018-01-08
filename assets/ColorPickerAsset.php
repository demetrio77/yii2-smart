<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class ColorPickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/vendor/itsjavi/bootstrap-colorpicker/dist';
	public $js  = ['js/bootstrap-colorpicker.min.js'];
	public $css = ['css/bootstrap-colorpicker.min.css'];
	public $depends = ['yii\web\JqueryAsset'];
}