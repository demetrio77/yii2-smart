<?php
namespace yii\helpers;

use demetrio77\smartadmin\helpers\DkBaseHtml;
use Yii;
use demetrio77\smartadmin\assets\Select2Asset;
use yii\web\View;

class Html extends DkBaseHtml
{
	public static $jarwisWidget=false;

	public static function beginForm($action = '', $method = 'post', $options = [])
	{
		$beginHtml = '';
		if (isset($options['jarwis']) && $options['jarwis']) {
			self::$jarwisWidget = true;
			unset($options['jarwis']);
            $beginHtml = '<div class="panel '.($options['class'] ?? '').'" role="widget" id="'.($options['id'] ?? uniqid()).'">
			<div class="panel-hdr">
			    <h2>'.($options['title'] ?? '').'</h2>
			    <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
			<div class="panel-container show">
				<div class="panel-content border-faded border-top-0 border-right-0 border-left-0">';
		}

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
}
