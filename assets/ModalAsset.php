<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class ModalAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/modal';
	public $css = [];
	public $js = ['modal.js'];
	public $depends = ['yii\web\JqueryAsset'];
}