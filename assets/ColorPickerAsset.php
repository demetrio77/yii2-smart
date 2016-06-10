<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class ColorPickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/colorpicker';
	public $js  = ['bootstrap-colorpicker.min.js'];
	public $depends = ['yii\web\JqueryAsset'];
}