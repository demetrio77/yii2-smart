<?php

namespace demetrio77\smartadmin\assets;

class SuperboxAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/superbox/';
	public $css = [];
	public $js = ['superbox.js'];
	public $depends = ['yii\web\JqueryAsset'];
}