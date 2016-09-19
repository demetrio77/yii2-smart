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
	public static function select2($name, $selection = null, $items = [], $options = [])
	{
		return static::dropDownList($name, $selection, $items, $options);
	}
	
	public static function activeSelect2($model, $attribute, $items = [], $options = [])
	{
		foreach ($items as $key => $value) {
			$items[$key] = html_entity_decode($value);
		}
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
		
		$val = $model->{$attribute};
		if (is_numeric($val)) {
			if ($val>0) {
				$model->{$attribute} = date('d.m.Y H:i', $val);
			}
			else {
				$model->{$attribute} = '';
			}
		}
		
		$view->registerJs("
            $('#".$id."').datetimepicker({
               locale: 'ru'
            });
			$('.cursor-pointer', $('#".$id."').parent().parent()).click( function(){
				var d = new Date,
    			dformat = [ d.getDate(),
							d.getMonth()+1,
               				d.getFullYear()].join('.')+' '+
              			  [ d.getHours(),
               				d.getMinutes(),
               			  ].join(':');
				
				$('#".$id."').data(\"DateTimePicker\").date(dformat);
			});
    	", View::POS_READY);
	
		$options['class'] = 'form-control'.(isset($options['class'])?' '.$options['class']:'');
	
		return self::activeTextInput($model, $attribute, $options);
	}
}