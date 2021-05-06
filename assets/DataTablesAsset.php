<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class DataTablesAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/smartadmin/';

	public $js  = ['js/datagrid/datatables/datatables.bundle.js'];
	public $css = ['css/datagrid/datatables/datatables.bundle.css'];

	public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset', ];
}
