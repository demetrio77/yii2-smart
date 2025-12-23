<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class SecurePasswordAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/secure-password';
    public $js = ['js/secure-password.js'];
    public $css = ['css/secure-password.css'];
    public $depends = [
        'yii\web\JqueryAsset',
        'demetrio77\smartadmin\assets\ModalAsset',
    ];
}
