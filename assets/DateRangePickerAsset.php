<?php 

namespace demetrio77\smartadmin\assets;

class DateRangePickerAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/bootstrap-daterangepicker';
    public $css = ['daterangepicker.css'];
    public $js = ['moment.min.js','daterangepicker.js'];
    public $depends = ['yii\web\JqueryAsset'];
}