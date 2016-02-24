<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class Select2Asset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/select2';
	public $js = ['select2.min.js'];
    public $depends = ['yii\web\JqueryAsset'];
    
    public function init()
    {
    	$view = $view = \Yii::$app->getView();
    	$view->registerCss('.select2-choice {
  			margin-top: -1px;
  			margin-right: -1px;
    		margin-left: -1px;
    	}
    	.select2-container {width: 100%;}');
    }
}
