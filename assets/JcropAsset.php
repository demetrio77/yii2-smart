<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class JcropAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets';
	public $css = ['jcrop/styles.css'];
	public $js = ['files/js/plugin/jcrop/jquery.Jcrop.min.js', 'files/js/plugin/jcrop/jquery.color.min.js'];
	public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset'];
}