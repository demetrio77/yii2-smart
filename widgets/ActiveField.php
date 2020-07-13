<?php

namespace demetrio77\smartadmin\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use demetrio77\smartadmin\assets\StarRatingAsset;
use demetrio77\smartadmin\assets\SpriteInputAsset;
use yii\web\View;
use yii\helpers\Url;
use demetrio77\smartadmin\assets\FileUploaderAsset;
use demetrio77\smartadmin\assets\DateTimePickerAsset;
use demetrio77\smartadmin\assets\ColorPickerAsset;

class ActiveField extends BaseActiveField
{
	public $options = ['tag' => 'div', 'class' => 'form-group'];
	public $labelOptions = ['class' => 'label'];

    public function dateDropDown( $options = [])
    {
    	$this->parts['{input}'] = Html::activeDateDropDown($this->model, $this->attribute, $options);
    	return $this;
    }
}
