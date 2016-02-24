<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class DateDropDownAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/datedropdown';
	public $css = [];
	public $js = ['dateDropDown.js'];
	public $depends = ['yii\web\JqueryAsset'];
}