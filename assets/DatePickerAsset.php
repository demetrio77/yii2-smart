<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class DatePickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/datepicker';
	public $css = ['jquery-ui.min.css'];
	public $js  = ['jquery-ui.min.js', 'datepicker.js'];
	public $depends = [];
}