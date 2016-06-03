<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class MorrisAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/morris';
	public $js = ['raphael.min.js','morris.min.js'];
	public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset'];
}