<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class SecureStorageAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/secure-storage';
    public $js = ['js/secure-storage.js'];
    public $css = ['css/secure-storage.css'];
    public $depends = ['yii\web\JqueryAsset'];
}
