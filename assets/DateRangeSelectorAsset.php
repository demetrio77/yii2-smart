<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class DateRangeSelectorAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/dateRangeSelector';
    public $js = ['dateRangeSelector.js'];
    public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset'];
}
