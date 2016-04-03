<?php

namespace demetrio77\smartadmin\assets;

class BootstrapDatepickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/bootstrap-datepicker/datepicker';
	public $css = ['css/bootstrap-datepicker.standalone.min.css'];
	public $js  = ['js/bootstrap-datepicker.min.js', 'locales/bootstrap-datepicker.ru.min.js'];
	public $depends = ['yii\web\JqueryAsset'];
}