<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class CkEditorAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/ckeditor';
	public $css = [];
	public $js = ['ckeditor.js', 'adapters/jquery.js'];
	public $depends = ['yii\web\JqueryAsset'];
}
