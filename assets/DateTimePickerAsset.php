<?php

namespace demetrio77\smartadmin\assets;

class DateTimePickerAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/bootstrap-datepicker';
	public $css = ['datetimepicker/css/bootstrap-datetimepicker.min.css'];
	public $js  = ['moment/moment-with-locales.min.js', 'datetimepicker/js/bootstrap-datetimepicker.min.js'];
	public $depends = ['yii\web\JqueryAsset'];
}