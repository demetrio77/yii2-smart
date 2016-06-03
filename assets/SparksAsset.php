<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class SparksAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/sparkline';
	public $js = ['jquery.sparkline.min.js'];
	public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset'];
}