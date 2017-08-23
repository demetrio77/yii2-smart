<?php

namespace demetrio77\smartadmin\widgets;

use Yii;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use demetrio77\smartadmin\assets\ColorPickerAsset;
use demetrio77\smartadmin\assets\DateTimePickerAsset;
use demetrio77\smartadmin\assets\SpriteInputAsset;
use demetrio77\smartadmin\assets\StarRatingAsset;
use demetrio77\smartadmin\assets\FileUploaderAsset;
use yii\helpers\Url;
use yii\di\ServiceLocator;
use demetrio77\manager\helpers\Alias;
use demetrio77\smartadmin\helpers\typograph\Typograph;
use demetrio77\smartadmin\assets\ClockPickerAsset;
use demetrio77\smartadmin\assets\Select2Asset;

class BaseActiveField extends \yii\widgets\ActiveField
{
	public $labelCols = 3;
	public $inputCols = 9;
	
	public function init()
	{
		parent::init();
		
		if ( property_exists($this->form, 'layout') && $this->form->layout == ActiveForm::LAYOUT_HORIZONTAL && $this->template=="{label}\n{input}\n{hint}\n{error}") {
			$this->template = str_replace(['{labelCols}','{inputCols}'], [$this->labelCols,$this->inputCols], $this->form->horizontalFieldTemplate);
		}
	}
	
	public function strongPassword($options = [])
	{
		$length = 8;
	
		if (isset($options['length'])) {
			$length = $options['length'];
			unset($options['length']);
		}
	
		$view = Yii::$app->getView();
		$id = Html::getInputId($this->model, $this->attribute);
	
		$this->template = "{label}\n{input}
		<div class='input-group'>
		<div id='{$id}-text' style='line-height:32px; text-indent: 10px;' class='form-control'></div>
		<div class='input-group-addon' style='padding:0'>
		<a id='{$id}-button' class='btn btn-xs btn-warning'>Генерировать</a>
		</div>
		</div>\n{hint}\n{error}";
	
		$view->registerJs("
	$('#{$id}-button').click( function(){
		var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
		var string_length = $length;
		var randomstring = '';
		for (var i=0; i<string_length; i++) {
				var rnum = Math.floor(Math.random() * chars.length);
				randomstring += chars.substring(rnum,rnum+1);
		}
		$('#$id').val(randomstring);
		$('#{$id}-text').text(randomstring);
	});", View::POS_READY);
	
		return $this->hiddenInput($options);
	}
	
	public function lookupDropDownList( $lookupModelName, $lookupModelField, $params=[], $options = [] )
	{
		/**
		 то, что может быть передано в params
		 */
		$lookupModelPk = 'id';
		$allowEmpty    = true;
		$defaultEmptyPk = '0';
		$defaultEmptyValue = '';
		$where = [];
		$autoComplete = false;
		$options = array_merge($this->inputOptions, $options);
	
		foreach ($params as $property => $value) {
			if (isset($$property)) $$property = $value;
		}
	
		$query = new \yii\db\Query();
	
		$query->select([$lookupModelPk, $lookupModelField])
			->from($lookupModelName)
			->orderBy($lookupModelField);
	
		if ($where) {
			foreach ($where as $w)
				$query->andWhere( $w );
		}
	
		$items = ArrayHelper::map(
			$query->all(),
			$lookupModelPk,
			$lookupModelField
		);
		 
		if ($autoComplete) {
			$view = Yii::$app->getView();
			\demetrio77\smartadmin\assets\Select2Asset::register( $view );
			$id = Html::getInputId($this->model, $this->attribute);
			$js = "$(document).ready(function(){ $('#".$id."').select2({}); });";
			$view->registerJs($js);
		}
	
		if ($allowEmpty) {
			$items = \yii\helpers\ArrayHelper::merge([$defaultEmptyPk => $defaultEmptyValue], $items);
		}
	
		return $this->dropDownList( $items, $options );
	}
	
	public function colorInput( $options = [] )
	{
		$view = Yii::$app->getView();
		ColorPickerAsset::register( $view );
		$id = Html::getInputId($this->model, $this->attribute);
		$view->registerJs("
            $('#".$id."').colorpicker().on('changeColor', function(ev) {
                $('#".$id."').css('background-color', ev.color.toHex() )
            });"
		);
		$options[ 'style'] = 'background-color: '.$this->model->{ $this->attribute};
		return $this->textInput( $options );
	}
	
	public function clockInput( $options = [] )
	{
		$view = Yii::$app->getView();
		ClockPickerAsset::register( $view );
		
		$id = Html::getInputId($this->model, $this->attribute);
		
		$view->registerJs("
            $('#".$id."').clockpicker({
				autoclose: true,
				placement: 'top'
			});",
			View::POS_READY
		);
		
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
		
		$this->parts['{input}'] = '<div class="input-group">'.Html::activeTextInput($this->model, $this->attribute, $options).'<span class="input-group-addon"><i class="fa fa-clock-o"></i></span></div>';
		return $this;
	}
	
	public function dateInput( $options = [])
	{
		$this->inputOptions['class'] .= ' datepicker';
		$options['data-dateformat'] = "yy-mm-dd";
	
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
		 
		$this->parts['{input}'] = '<div class="input-group">'.Html::activeTextInput($this->model, $this->attribute, $options).'<span title="Cегодня" class="input-group-addon cursor-pointer"><i class="fa fa-calendar"></i></span></div>';
		 
		return $this;
	}
	
	public function dateTimeInput( $options = [])
	{
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
		$this->parts['{input}'] = Html::activeDateTimeInput($this->model, $this->attribute, $options);
		return $this;
	}
	
	public function ckEditor ( $options = [])
	{
		$route = ['//manager/ckeditor'];
		$defaults = [
			'defaultFolder'=>false,
			'configuration'=>'default',
			'alias'=>false
		];
		$options = ArrayHelper::merge($defaults, $options);
		 
		if ($options['defaultFolder']) {
			$route['defaultFolder'] = $options['defaultFolder'];
		}
		if ($options['alias']) {
			$route['alias'] = $options['alias'];
		}
		$route['configuration'] = $options['configuration'];
		 
		foreach ($defaults as $key) {
			if (isset($options[$key])) unset($options['key']);
		}
		 
		$view = Yii::$app->getView();
		\CkEditorAsset::register( $view );
		$id = Html::getInputId($this->model, $this->attribute);
		$view->registerJs("$('#".$id."').ckeditor({
    		filebrowserBrowseUrl: '".Url::toRoute($route)."'
		});");
		 
		return $this->textarea( $options );
	}
	
	public function dropDownListAutoComplete($items, $options = [])
	{
		$showHints = false;
		$hints = [];
		 
		if (isset($options['hints'])) {
			$h = $options['hints'];
			foreach ($h as $key => $val) {
				$hints[$key] = ['data-hint' => $val ];
			}
			unset( $options['hints']);
			$showHints = true;
		}
		
		foreach ($items as $key => $value) {
			$items[$key] = Typograph::remove($value);
		}
		 
		$view = Yii::$app->getView();
		\demetrio77\smartadmin\assets\Select2Asset::register( $view );
		$id = Html::getInputId($this->model, $this->attribute);
	
		$js = "$('#".$id."').select2({".($showHints ? "
		    formatResult: format,
			formatSelection: format":'')."
		});
		".($showHints ? "
		function format(state) {
			var originalOption = state.element;
			return state.text+\"<br /><span style=\'font-size:80%\'>\"+$(originalOption).data(\"hint\")+\"</span>\";
		}" : "");
		$view->registerJs($js);
		 
		return $this->dropDownList($items, ArrayHelper::merge($options, [ 'options' => $hints ]));
	}
	
	public function spriteInput($sprite, $width, $height, $options = [])
	{
		$view = Yii::$app->getView();
		SpriteInputAsset::register($view);
		 
		$val = trim($this->model->{$this->attribute});
		$x = 'null';
		$y = 'null';
		$bg = false;
		if ($val!='') {
			$found = preg_match_all('/\-?(\d+)\w*? \-?(\d+)/', $val, $matches);
			if ($found && $matches && isset($matches[1][0],$matches[2][0])) {
				$x = abs($matches[1][0]);
				$y  = abs($matches[2][0]);
			}
		}
		 
		if (isset($options['background-color'])) {
			$bg = $options['background-color'];
			unset($options['background-color']);
		}
		$options['class'] = (isset($options['class'])?$options['class'].' ':'').'form-control';
		$id = Html::getInputId($this->model, $this->attribute);
		 
		$view->registerJs("$ ('#".$id."').spriteInput({
				x: $x,
				y: $y,
				width: $width,
				height: $height,
				".($bg ? "backgroundColor: '$bg',":"")."
				sprite: '$sprite'
		});", View::POS_READY);
	 
		$this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);
		return $this;
	}
	
	public function markInput($options=[])
	{
		$view = Yii::$app->getView();
		StarRatingAsset::register( $view );
		$id = Html::getInputId($this->model, $this->attribute);
		
		$view->registerJs("$ ('#".$id."').rating();");
		
		$this->inputOptions['class'] .= ' rating';
		if (!isset($options['min'])) {
			$options['min'] = 0;
		}
		if (!isset($options['max'])) {
			$options['max'] = 5;
		}
		if (!isset($options['step'])) {
			$options['step'] = 1;
		}
		if (!isset($options['disabled'])) {
			$options['disabled'] = false;
		}
	
		$options['data']['size'] = isset($options['size'])?$options['size']:'lg';
	
		$options['type'] = 'number';
		$options['data']['show-clear']=false;
		$options['data']['show-caption']=false;
	
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
	
		$this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
		return $this;
	}
	
	public function numberInput($options=[])
	{
		parent::textInput($options);
		$this->parts['{input}'] = Html::activeInput('number', $this->model, $this->attribute, $options);
		return $this;
	}
	
	public function fileInput($options = [] )
	{
		$defaults = [
			'returnPath' => false,
			'folder' => '',
			'alias'=>'',
			'isImage' => false,
			'filename' => false,
			'tmpl' => 'upload,server,url,clear',
			'callback' => false
		];
			
		$options = ArrayHelper::merge($defaults, $options);
		$id = Html::getInputId($this->model, $this->attribute);
	
		$url = $this->model->{$this->attribute};
	
		$js = "$(document).ready(function(){
    	    $('#".$id."').fileUploader({
               value	 : '".$this->model->{$this->attribute}."',
               tmpl:'".$options['tmpl']."',
               ".($options['callback']?"callback:".$options['callback'].',':'')."
               ".($options['returnPath'] ? "returnPath: true,":'')."
               ".($options['isImage'] ? "isImage: true,":'')."
               ".($options['filename'] ? "filename: '".$options['filename']."',":'')."
               connector: '".Url::toRoute(['//manager/connector'])."',
               browse: '".Url::toRoute(['//manager/browse'])."',
    	       alias: '".$options['alias']."',
    	       folder: '".$options['folder']."'
    		});
    	});";
			
		foreach (array_keys($defaults) as $key) {
			if (isset($options[$key])) unset($options[$key]);
		}
			
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
		$this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute);
			
		$view = Yii::$app->getView();
		FileUploaderAsset::register( $view );
		$view->registerJs($js);
			
		return $this;
	}
	
	public function linkInput($options = [] )
	{
	    $defaults = [
	        'returnPath' => true,
	        'folder' => '',
	        'alias'=> ''
	    ];
	    	
	    $options = ArrayHelper::merge($defaults, $options);
	    $id = Html::getInputId($this->model, $this->attribute);
	
	    $url = $this->model->{$this->attribute};
	
	    $js = "$(document).ready(function(){
			$('#".$id."-a').click( function(){
			    var height = window.innerHeight - 200;
			    var width  = window.innerWidth  - 200;
			    var left = window.screenLeft+100;
			    var top = window.screenTop+100;
			    window.open('".Url::toRoute(['//manager/browse'])."?destination=input&id=$id&alias=".$options['alias']."&path=".$options['folder']."&fileName=' + $('#$id').val() + '&returnPath=".(int)$options['returnPath']."', 'browse', 'menubar=no,location=no,resizable=no,scrollbars=yes,left='+left+',top='+top+',status=no,height='+height+',width='+width);
			});
    	});";
	    	
	    foreach (array_keys($defaults) as $key) {
	        if (isset($options[$key])) unset($options[$key]);
	    }
	    	
	    $options = array_merge($this->inputOptions, $options);
	    $this->adjustLabelFor($options);
	    $this->parts['{input}'] = '<div class="input-group"><span class="input">'.Html::activeTextInput($this->model, $this->attribute, $options).'</span><span class="input-group-addon no-padding "><a id="'.$id.'-a" title="Выбрать" class="btn btn-primary">Выбрать</a></span></div>';
			
	    Yii::$app->view->registerJs($js);
	    
	    return $this;
	}

	public function imageInput($options = [])
	{
		$options['isImage'] = true;
		return $this->fileInput($options);
	}
	
	
	public function dropDownMultiple( $items = [], $options = [], $tags = false)
	{
		$style = 'padding:0; border:0;';
		if (!isset($options['style']))
			$options['style'] = $style;
		else
			$options['style'] .= $style;
	
		$options = array_merge($this->inputOptions, $options);
		$this->adjustLabelFor($options);
	
		if ($tags) {
			$view = Yii::$app->getView();
			\demetrio77\smartadmin\assets\Select2Asset::register( $view );
			$id = Html::getInputId($this->model, $this->attribute);
		
			$js = "$('#".$id."').select2({
				tags:['".implode("','", $items)."'],
				tokenSeparators: [',']
			});";
			
			$this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
			
			$view->registerJs($js);
		} 
		else {
			$options['multiple'] = true;
			$this->parts['{input}'] = Html::activeSelect2($this->model, $this->attribute, $items, $options);
		}
		
		return $this;
	}
	
	public function dateDropDown( $options = [])
	{
	    $value = $this->model->{$this->attribute};
	    $date = date_parse($value);
	    if (!$date['error_count']){
	        $this->model->{$this->attribute} = $date['year'].'-'.($date['month']<10?'0':'').$date['month'].'-'.($date['day']<10?'0':'').$date['day'];
	    }
		$this->parts['{input}'] = Html::activeDateDropDown($this->model, $this->attribute, $options);
		return $this;
	}
	
	public function remoteDropDown($url = [], $options = [])
	{
	    $id = Html::getInputId($this->model, $this->attribute);
	    $view = Yii::$app->getView();	    
	    \site\assets\Select2Asset::register( $view );
	    
	    $minimumInputLength = 3;
	    $formatNoMatches = 'Ничего не найдено';
	    $formatSearching = 'Поиск...';
	    $formatInputTooShort = 'Введите по крайней мере 3 символа';
	    $itemsOnPage = 15;
	    $initSelectionUrl = false;
	    $formatResult = null;
	    $formatSelection = null;
	    $escapeMarkup = false;
	    
	    foreach (['minimumInputLength','formatNoMatches','formatSearching','formatInputTooShort','itemsOnPage',
	           'initSelectionUrl','formatResult','formatSelection','escapeMarkup'] as $key) {
	        if (isset($options[$key])) {
    	        $$key = $options[$key];
    	        unset($options[$key]);
    	    }
	    }
	    
	    $url['itemsOnPage'] = $itemsOnPage;
	    
	    $view->registerJs("$('#".$id."').select2({
		    formatNoMatches: function(q){return '$formatNoMatches';},
			formatSearching: '$formatSearching',
			formatInputTooShort: '$formatInputTooShort',
	        ajax: {
				url: '".Url::toRoute($url)."',
				dataType: 'json',
				quietMillis: 250,
				data: function (term, page) { return {
	                q: term, //search term
	                page: page // page number
	            }; },
	        	results: function (data, page) {
					var more = $itemsOnPage == data.total_count; // whether or not there are more results available
 					return { results: data.items, more: more };
				},
				cache: true
			},
			".($initSelectionUrl ? "initSelection: function(element, callback) {
		        var id = $(element).val();
		        if (id !== '') {
		            $.ajax('".$url."id=' + id, {
		                dataType: 'json'
		            }).done(function(data) { callback(data); });
		        }
		    },":"")."
	        ".($formatResult ? "formatResult: $formatResult,":'').
	        ($formatSelection ? "formatSelection: $formatSelection,":'').
			($escapeMarkup ? "escapeMarkup: function (m) { return m; }," : '')."
	        minimumInputLength: $minimumInputLength
		});", View::POS_READY);
	    
	    return $this->dropDownList(['ff','vv']) ;
	}
}