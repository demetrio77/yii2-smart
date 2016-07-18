<?php

namespace yii\helpers;

use Yii;
use demetrio77\smartadmin\assets\Select2Asset;
use demetrio77\smartadmin\assets\DateDropDownAsset;
use demetrio77\smartadmin\widgets\JarvisWidget;
use demetrio77\smartadmin\assets\DateTimePickerAsset;
use demetrio77\smartadmin\helpers\DkBaseHtml;

class Html extends DkBaseHtml
{
	public static $jarwisWidget=false;
	
	public static function beginForm($action = '', $method = 'post', $options = [])
	{
		$noSmartForm = false;
		
		if (isset($options['jarwis'])) {
			self::$jarwisWidget = true;
			unset($options['jarwis']);
			JarvisWidget::begin(['nopadding'=> true, 'title'=>isset($options['title'])?$options['title']:'']);
		}
		
		if (isset($options['noSmartForm'])) {
			$noSmartForm = true;
			unset($options['noSmartForm']);
		}
		
		$options['class'] = (isset($options['class']) ?  $options['class'] : '').(!$noSmartForm?' smart-form':'');
			
		return parent::beginForm($action, $method, $options);
	}
	
	public static function endForm()
	{
		if (!self::$jarwisWidget) {
			return parent::endForm();
		}
		self::$jarwisWidget = false;
		echo parent::endForm();
		JarvisWidget::end();
	}
	
	public static function activeTextarea($model, $attribute, $options = [])
	{
		return self::tag('div', parent::activeTextarea($model, $attribute, $options), ['class' => 'textarea']);
	}
			
	public static function radio($name, $checked = false, $options = [])
	{
		$options['checked'] = (bool) $checked;
		$value = array_key_exists('value', $options) ? $options['value'] : '1';
		if (isset($options['uncheck'])) {
			// add a hidden field so that if the radio button is not selected, it still submits a value
			$hidden = static::hiddenInput($name, $options['uncheck']);
			unset($options['uncheck']);
		} else {
			$hidden = '';
		}
		if (isset($options['label'])) {
			$label = $options['label'];
			$labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
			$labelOptions['class'] = (isset($labelOptions['class'])?$labelOptions['class'].' ':'').'radio';
			unset($options['label'], $options['labelOptions']);
			/*
			 * добавлено
			*/if (isset($options['class']) && $options['class']=='styled') {
			$options['id'] = 'radio-'.$name.'-'.$value;
			$content = static::input('radio', $name, $value, $options).' '.static::label($label, $options['id'], $labelOptions);
			}
			else {/* до сюда */
				$content = static::label(static::input('radio', $name, $value, $options) . ' <i></i>' . $label, null, $labelOptions);
			}
			return $hidden . $content;
		} else {
			return $hidden . static::input('radio', $name, $value, $options);
		}
	}
	
	public static function checkbox($name, $checked = false, $options = [])
	{
		$options['checked'] = (bool) $checked;
		if (isset($options['disabled'])) {
			$options['labelOptions']['class'] = (isset($options['labelOptions']['class']) ? $options['labelOptions']['class'].' ' : '').'state-disabled';
		}
		$value = array_key_exists('value', $options) ? $options['value'] : '1';
		if (isset($options['uncheck'])) {
			// add a hidden field so that if the checkbox is not selected, it still submits a value
			$hidden = static::hiddenInput($name, $options['uncheck']);
			unset($options['uncheck']);
		} else {
			$hidden = '';
		}
		if (isset($options['label'])) {
			$label = $options['label'];
			$labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
			$labelOptions['class'] = (isset($labelOptions['class'])?$labelOptions['class'].' ':'').'checkbox';
			unset($options['label'], $options['labelOptions']);
			/*
			 * добавлено
			*/if (isset($options['class']) && strpos($options['class'],'styled')!==false) {
			$options['id'] = 'check-'.$name.'-'.$value;
			$content = static::input('checkbox', $name, $value, $options).' '.static::label($label, $options['id'], $labelOptions);
			}
			else {/* до сюда */
				$content = static::label(static::input('checkbox', $name, $value, $options) . ' <i></i>' . $label, null, $labelOptions);
			}
			return $hidden . $content;
		} else {
			$labelOptions = [];
			if (isset($options['labelOptions'])) {
				$labelOptions = $options['labelOptions'];
				unset($options['labelOptions']);
			}
			$labelOptions['class'] = (isset($labelOptions['class'])?$labelOptions['class'].' ':'').'checkbox';
			return $hidden . self::tag('label', static::input('checkbox', $name, $value, $options).self::tag('i'), $labelOptions);
		}
	}
	
	public static function activeDateTimeInput($model, $attribute, $options = [])
	{
		return self::tag('label', parent::activeDateTimeInput($model, $attribute, $options), ['class' => 'input']);
	}
	
	public static function activeSelect2($model, $attribute, $items = [], $options = [])
	{
		$view = Yii::$app->getView();
		Select2Asset::register( $view );
		$id = self::getInputId($model, $attribute);
		$view->registerJs("$('#".$id."').select2();");
		return parent::activeSelect2($model, $attribute, $items, $options);
	}	
}