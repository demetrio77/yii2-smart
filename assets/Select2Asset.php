<?php
namespace demetrio77\smartadmin\assets;

use admin\assets\AppAsset;

class Select2Asset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/smartadmin/';
	public $js = ['js/formplugins/select2/select2.bundle.js'];
    public $css = ['css/formplugins/select2/select2.bundle.css'];
    public $depends = [AppAsset::class];
}
