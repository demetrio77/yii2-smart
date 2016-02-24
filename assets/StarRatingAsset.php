<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class StarRatingAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/bootstrap-star-rating';
	public $css = ['css/star-rating.min.css','css/styles.css'];
	public $js  = ['js/star-rating.min.js'];
	public $depends = ['yii\web\JqueryAsset', 'demetrio77\smartadmin\assets\GlyphiconsAsset'];
}