<?php

namespace demetrio77\smartadmin\widgets;

class ActiveForm extends \yii\widgets\ActiveForm
{
	public $fieldClass = 'demetrio77\smartadmin\widgets\BaseActiveField';
	public $layout = 'default'; //horizontal
	public $horizontalFieldTemplate = "<div class='form-group'>\n<div class='row'>\n<div class='col-xs-3'>{label}</div>\n<div class='col-xs-9'>{input}\n{error}</div>\n{hint}</div></div>";
}