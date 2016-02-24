<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class GlyphiconsAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/glyphicons';
	public $css = ['css/glyphicons.css'];
	public $js  = [];
	public $depends = ['yii\web\JqueryAsset'];
}