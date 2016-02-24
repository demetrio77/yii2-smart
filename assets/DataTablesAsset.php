<?php

namespace demetrio77\smartadmin\assets;

use demetrio77\smartadmin\assets\BaseAsset;

class DataTablesAsset extends BaseAsset
{
	public $sourcePath = '@demetrio77/smartadmin/assets/files/js/plugin/';

	public $js  = ['datatables/jquery.dataTables.min.js', 
		'datatables/dataTables.colVis.min.js',
		'datatables/dataTables.tableTools.min.js',
		'datatables/dataTables.bootstrap.min.js',
		'datatable-responsive/datatables.responsive.min.js'
	];
	
	public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset', ];
}