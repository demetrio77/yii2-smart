<?php

namespace demetrio77\smartadmin\widgets;

use yii\grid\DataColumn;

class LabelColumn extends DataColumn
{
	protected function renderDataCellContent($model, $key, $index)
	{
		switch ($model->{$this->attribute})
		{
			case 0:
				return '<span class="label label-default">Нет</span>';
			break;
			case 1:
				return '<span class="label label-success">Да</span>';
			break;
		}
	}
}