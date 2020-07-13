<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SmartAdminAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/smartadmin';
    public $css = [
        'css/vendors.bundle.css',
        'css/app.bundle.css',
        'css/skins/skin-master.css',
    ];
    public $cssOptions = ['media' => 'screen,print', 'type' => 'text/css', 'rel' => 'stylesheet'];
    public $js = [
        'js/vendors.bundle.js',
        'js/app.bundle.js',
    ];
}
