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

class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * Селект с данными из связанной модели
     * @param string $lookupModelName
     * @param string $lookupModelField
     * @param array $params
     * @param array $options
     * @return Ambigous <\yii\widgets\static, \common\widgets\ActiveField>
     */
	
	public $options = ['tag' => 'section'];
	public $labelOptions = ['class' => 'label'];
	
	public function textInput($options = [])
	{
		parent::textInput($options);
		$this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input']);	
		return $this;
	}
	
	public function passwordInput($options = [])
	{
		parent::passwordInput($options);
		$this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input']);
		return $this;
	}
	
	public function textarea($options = [])
	{
		parent::textarea($options);
		$this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'textarea']);
		return $this;
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
        
    	$style = 'padding:0; border:0;';
    	if (!isset($options['style']))
    		$options['style'] = $style;
    	else
    		$options['style'] .= $style;
    	
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
    
    /*public function colorInput( $options = [] )
    {
        $view = Yii::$app->getView();
        \backend\assets\ColorPickerAsset::register( $view );
        $id = Html::getInputId($this->model, $this->attribute);
        $view->registerJs("
            $('#".$id."').colorpicker().on('changeColor', function(ev) {
                $('#".$id."').css('background-color', ev.color.toHex() )
            });"
        );
        $options[ 'style'] = 'background-color: '.$this->model->{ $this->attribute};
        return $this->textInput( $options );
    }*/
    
    public function dateInput( $options = [])
    {
    	$view = Yii::$app->getView();
        
        $this->inputOptions['class'] .= ' datepicker';
    	$options['data-dateformat'] = "yy-mm-dd";

    	$options = array_merge($this->inputOptions, $options);
    	$this->adjustLabelFor($options);
    	
    	$this->parts['{input}'] = '<div class="input"><div class="input-group">'.Html::activeTextInput($this->model, $this->attribute, $options).'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>';
    	
    	return $this;
    }
    
    public function dateDropDown( $options = [])
    {
    	$this->parts['{input}'] = Html::activeDateDropDown($this->model, $this->attribute, $options);
    	return $this;
    }
    
    public function dateTimeInput( $options = [])
    {
    	$view = Yii::$app->getView();
    	DateTimePickerAsset::register( $view );
    	
    	$id = Html::getInputId($this->model, $this->attribute);
    	 
    	$view->registerJs("
            $('#".$id."').datetimepicker({
               locale: 'ru'
            });
    	");
    	 
    	$options = array_merge($this->inputOptions, $options);
    	$this->adjustLabelFor($options);
    	 
    	$this->model->{$this->attribute} = date('d.m.Y H:i', $this->model->{$this->attribute});
    	 
    	$this->parts['{input}'] = '<div class="input"><div class="input-group">'.Html::activeTextInput($this->model, $this->attribute, $options).'<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span></div></div>';
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
    
    /*public function dropDownMultiple( $items = [], $options = [], $tags = false)
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
        	\backend\assets\Select2Asset::register( $view );
        	$id = Html::getInputId($this->model, $this->attribute);
        	$js = "
    	       $('#".$id."').select2({
    	       		tags:['".implode("','", $items)."'],
                    tokenSeparators: [',']
        		});
    	    ";
        	$this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
        	$view->registerJs($js);
        } else {
        	$options['multiple'] = true;
        	$this->parts['{input}'] = Html::activeSelect2($this->model, $this->attribute, $items, $options);
        }
        return $this;
    }*/
    
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
        $this->parts['{input}'] = Html::tag('div', Html::activeInput('number', $this->model, $this->attribute, $options), ['class' => 'input']);
    	return $this;
    }
    
    public function imageInput($options = [])
    {
    	$options['isImage'] = true;
    	return $this->fileInput($options);
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
    		'callback' => false,
    		'prefixUrl'=>false
    	];
    	
    	$options = ArrayHelper::merge($defaults, $options);
    	$id = Html::getInputId($this->model, $this->attribute);
    	
    	$js = "$(document).ready(function(){
    	    $('#".$id."').fileUploader({
               value	 : '".$this->model->{$this->attribute}."',
               tmpl:'".$options['tmpl']."',
               ".($options['callback']?"callback:".$options['callback'].',':'')."
               ".($options['returnPath'] ? "returnPath: true,":'')."
               ".($options['prefixUrl'] ? "prefixUrl: '".$options['prefixUrl']."',":'')."
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
}