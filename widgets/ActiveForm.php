<?php

namespace demetrio77\smartadmin\widgets;

class ActiveForm extends \yii\bootstrap4\ActiveForm
{
	const LAYOUT_HORIZONTAL = 'horizontal';

	public $fieldClass = 'demetrio77\smartadmin\widgets\BaseActiveField';
	public $layout = 'default';
	public $horizontalFieldTemplate = "<div class='form-group'>\n<div class='row'>\n<div class='col-xs-{labelCols}'>{label}</div>\n<div class='col-xs-{inputCols}'>{input}\n{error}</div>\n{hint}</div></div>";
}
