<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class MultiFileUploaderAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/fileuploader';
    public $css = [];
    public $js = ['fileMultiUploader.js'];
    public $depends = ['yii\web\JqueryAsset', 'demetrio77\manager\assets\FileApiAsset'];
}
