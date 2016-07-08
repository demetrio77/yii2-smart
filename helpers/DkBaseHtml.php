<?php

namespace demetrio77\smartadmin\helpers;

use Yii;
use yii\helpers\BaseHtml;
use demetrio77\smartadmin\assets\Select2Asset;
use demetrio77\smartadmin\assets\DateDropDownAsset;
use demetrio77\smartadmin\assets\DateTimePickerAsset;
use yii\web\View;

class DkBaseHtml extends BaseHtml
{
	public static function activeSelect2($model, $attribute, $items = [], $options = [])
	{
		$view = Yii::$app->getView();
		Select2Asset::register( $view );
		$id = self::getInputId($model, $attribute);
		$view->registerJs("$('#".$id."').select2();");
		return static::activeListInput('dropDownList', $model, $attribute, $items, $options);
	}
	
	public static function activeDateDropDown($model, $attribute, $options = [])
	{
		$opts = [];
		if (isset($options['minYear'])) {
			$opts[] = "minYear: ".$options['minYear'];
			unset($options['minYear']);
		}
		if (isset($options['maxYear'])) {
			$opts[] = "maxYear: ".$options['maxYear'];
			unset($options['maxYear']);
		}
		if (isset($options['defaultDate'])) {
			$opts[] = "defaultDate: '".$options['defaultDate']."'";
			unset($options['defaultDate']);
		}
		$id = static::getInputId($model, $attribute).'Div';
		$view = Yii::$app->getView();
		DateDropDownAsset::register($view);
		$view->registerJs("$('#".$id."').dateDropDown(".($opts?"{".implode(',', $opts)."}":'').");");
		return '<div id="'.$id.'">'.static::activeHiddenInput($model, $attribute, $options).'</div>';
	}
	
	public static function activeDateTimeInput($model, $attribute, $options = [])
	{
		$view = Yii::$app->getView();
		DateTimePickerAsset::register( $view );
			
		$id = self::getInputId($model, $attribute);
	
		$view->registerJs("
            $('#".$id."').datetimepicker({
               locale: 'ru'
            });
    	", View::POS_READY);
	
		$options['class'] = 'form-control'.(isset($options['class'])?' '.$options['class']:'');
	
		return self::activeTextInput($model, $attribute, $options);
	}
}