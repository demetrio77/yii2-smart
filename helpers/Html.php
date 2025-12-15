<?php
namespace yii\helpers;

use demetrio77\smartadmin\assets\SecurePasswordAsset;
use Yii;
use demetrio77\smartadmin\assets\Select2Asset;
use demetrio77\smartadmin\helpers\DkBaseHtml;
use yii\web\View;

class Html extends DkBaseHtml
{
	public static $jarwisWidget=false;

	public static function beginForm($action = '', $method = 'post', $options = [])
	{
		$noSmartForm = false;
		$beginHtml = '';
		if (isset($options['jarwis']) && $options['jarwis']) {
			self::$jarwisWidget = true;
			unset($options['jarwis']);
			$beginHtml = '
			    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-'.($options['id'] ?? uniqid()).'" data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
			<header><h2 class="font-md">'.($options['title'] ?? '').'</h2></header>
			<div>
				<div class="jarviswidget-editbox"></div>						
    			<div class="widget-body no-padding">';
		}

		if (isset($options['noSmartForm'])) {
			$noSmartForm = true;
			unset($options['noSmartForm']);
		}

		$options['class'] = (isset($options['class']) ?  $options['class'] : '').(!$noSmartForm?' smart-form':'');

		return $beginHtml . parent::beginForm($action, $method, $options);
	}

	public static function endForm()
	{
		if (!self::$jarwisWidget) {
			return parent::endForm();
		}
		self::$jarwisWidget = false;
		return parent::endForm().'</div>
    		</div>
    	</div>';
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

	public static function select2($name, $selection = null, $items = [], $options = [])
	{
        $style = 'padding:0; border:0;';
        if (!isset($options['style']))
            $options['style'] = $style;
        else
            $options['style'] .= $style;

		$view = Yii::$app->getView();
		Select2Asset::register( $view );
        $options['id'] = $options['id'] ?? str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);

        $view->registerJs("$('#".$options['id']."').select2();");
		return parent::select2($name, $selection, $items, $options);
	}

	public static function activeSelect2($model, $attribute, $items = [], $options = [])
	{
		$view = Yii::$app->getView();
		Select2Asset::register( $view );
		$id = self::getInputId($model, $attribute);
		$view->registerJs("$('#".$id."').select2();");
		return parent::activeSelect2($model, $attribute, $items, $options);
	}

	public function remoteDropDown($name, $value, $url = [], $options = [])
	{
	    $view = Yii::$app->getView();
	    \demetrio77\smartadmin\assets\Select2Asset::register( $view );

	    $minimumInputLength = 3;
	    $formatNoMatches = 'Ничего не найдено';
	    $formatSearching = 'Поиск...';
	    $itemsOnPage = 15;
	    $initSelectionUrl = false;
	    $formatResult = null;
	    $formatSelection = null;
	    $escapeMarkup = false;
	    $callback = '';
	    foreach (['minimumInputLength','formatNoMatches','formatSearching','formatInputTooShort','itemsOnPage',
	        'initSelectionUrl','formatResult','formatSelection','escapeMarkup','callback','minimumInputLength'] as $key) {
	        if (isset($options[$key])) {
	            $$key = $options[$key];
	            unset($options[$key]);
	        }
	    }
	    $formatInputTooShort = 'Введите по крайней мере '.$minimumInputLength.' символ(а)';
	    $url['itemsOnPage'] = $itemsOnPage;

	    $view->registerJs("$('input[name=$name]').select2({
	        formatNoMatches: function(q){return '$formatNoMatches';},
	        formatSearching: '$formatSearching',
	        formatInputTooShort: '$formatInputTooShort',
	        ajax: {
    	        url: '".Url::toRoute($url)."',
    	        dataType: 'json',
    	        quietMillis: 250,
    	        data: function (term, page) {
    	        return {
    	        q: term, //search term
    	        page: page // page number
        	};
        	},
        	        results: function (data, page) {
        	        var more = $itemsOnPage == data.total_count; // whether or not there are more results available
        	        return {
        	        results: data.items,
        	        more: more
        	};
        	},
        	        cache: true
        	},
	        ".($initSelectionUrl ? "initSelection: function(element, callback) {
		        var id = $(element).val();
		        if (id !== '') {
		            $.ajax('".$initSelectionUrl."id=' + id, {
		                dataType: 'json'
		            }).done(function(data) { callback(data); });
		        }
		    },":"")."
	        ".($formatResult ? "formatResult: $formatResult,":'').
	        ($formatSelection ? "formatSelection: $formatSelection,":'').
	        ($escapeMarkup ? "escapeMarkup: function (m) { return m; }," : '')."
	        minimumInputLength: $minimumInputLength
	})". ($callback ? ".on('change', ".$callback.")" : '').";", View::POS_READY);

	        return self::textInput($name, $value, $options);
	}

	public function activeRemoteDropDown($model, $attribute, $url = [], $options = [])
	{
	    $id = self::getInputId($model, $attribute);
	    $view = Yii::$app->getView();
	    \demetrio77\smartadmin\assets\Select2Asset::register( $view );

	    $minimumInputLength = 3;
	    $formatNoMatches = 'Ничего не найдено';
	    $formatSearching = 'Поиск...';
	    $itemsOnPage = 15;
	    $initSelectionUrl = false;
	    $formatResult = null;
	    $formatSelection = null;
	    $escapeMarkup = false;
	    $callback = '';
	    foreach (['minimumInputLength','formatNoMatches','formatSearching','formatInputTooShort','itemsOnPage',
	        'initSelectionUrl','formatResult','formatSelection','escapeMarkup','callback','minimumInputLength'] as $key) {
	        if (isset($options[$key])) {
	            $$key = $options[$key];
	            unset($options[$key]);
	        }
	    }
	    $formatInputTooShort = 'Введите по крайней мере '.$minimumInputLength.' символ(а)';
	    $url['itemsOnPage'] = $itemsOnPage;

	    $view->registerJs("$('#".$id."').select2({
	        formatNoMatches: function(q){return '$formatNoMatches';},
	        formatSearching: '$formatSearching',
	        formatInputTooShort: '$formatInputTooShort',
	        ajax: {
    	        url: '".Url::toRoute($url)."',
    	        dataType: 'json',
    	        quietMillis: 250,
    	        data: function (term, page) { 
                    return {
    	               q: term, //search term
    	               page: page // page number
        	       };
                },
    	        results: function (data, page) {
    	           var more = $itemsOnPage == data.total_count; // whether or not there are more results available
    	           return { 
                        results: data.items, 
                        more: more 
                   };
    	        },
    	        cache: true
    	    },
	        ".($initSelectionUrl ? "initSelection: function(element, callback) {
		        var id = $(element).val();
		        if (id !== '') {
		            $.ajax('".$initSelectionUrl."id=' + id, {
		                dataType: 'json'
		            }).done(function(data) { callback(data); });
		        }
		    },":"")."
	        ".($formatResult ? "formatResult: $formatResult,":'').
	        ($formatSelection ? "formatSelection: $formatSelection,":'').
	        ($escapeMarkup ? "escapeMarkup: function (m) { return m; }," : '')."
	        minimumInputLength: $minimumInputLength
	   })". ($callback ? ".on('change', ".$callback.")" : '').";", View::POS_READY);

	   return self::activeTextInput($model, $attribute, $options);
	}


    public static function dropDownMultiple($name, $value = null, $items = [], $options = [], $tags = false)
    {
        $style = 'padding:0; border:0;';
        if (!isset($options['style']))
            $options['style'] = $style;
        else
            $options['style'] .= $style;

        $options['id'] = $options['id'] ?? str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);

        if ($tags) {
            $view = Yii::$app->getView();
            \demetrio77\smartadmin\assets\Select2Asset::register( $view );

            $js = "$('#".$options['id']."').select2({
				tags:['".implode("','", $items)."'],
				tokenSeparators: [',']
			});";
            $view->registerJs($js);

            return self::textInput($name, $value, $options);
        }
        else {
            $options['multiple'] = true;
            return self::select2($name, $value, $items, $options);
        }
    }

    public static function secureText($name, $value = null, $options = [], $configId = 0): string
    {
        $displayValue = (!empty($value) ? '******' : 'no password');

        $view = Yii::$app->getView();
        SecurePasswordAsset::register( $view );

        return $view->render('@demetrio77/smartadmin/views/secure-password/secure-text', [
            'name' => $name,
            'value' => $value,
            'displayValue' => $displayValue,
            'configId' => $configId,
        ]);
    }

    public static function secureInput($name, $value = null, $options = [], $configId = 0): string
    {
        $displayValue = (!empty($value) ? '******' : 'no password');

        parent::textInput($options);

        $view = Yii::$app->getView();
        SecurePasswordAsset::register($view);

        $view->on(\yii\web\View::EVENT_END_BODY, function () use ($view) {
            echo $view->render('@demetrio77/smartadmin/views/secure-password/_set-password-modal');
        });

        return $view->render('@demetrio77/smartadmin/views/secure-password/secure-input', [
            'name' => $name,
            'value' => $value,
            'displayValue' => $displayValue,
            'configId' => $configId,
        ]);
    }
}
