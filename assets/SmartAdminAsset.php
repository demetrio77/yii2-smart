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
    public $sourcePath = '@demetrio77/smartadmin/assets/files';
    public $css = [ 'css/bootstrap.min.css',
                    'css/font-awesome.min.css',
                    'css/smartadmin-production.min.css',
                    'css/smartadmin-skins.min.css',
                    'css/fonts.css',
                    'css/styles.css'
    ];
    public $cssOptions = ['media' => 'screen', 'type' => 'text/css'];
    public $js = [
	   'js/libs/jquery-ui-1.10.3.min.js',
	   'js/app.config.js',
	   'js/plugin/jquery-touch/jquery.ui.touch-punch.min.js',
	   'js/bootstrap/bootstrap.min.js',
	   'js/notification/SmartNotification.min.js',
	   'js/smartwidgets/jarvis.widget.min.js',
	   'js/app.min.js',
    ];
    public $depends = ['yii\web\JqueryAsset'];
}
