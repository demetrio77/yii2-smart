<?php

namespace demetrio77\smartadmin\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use demetrio77\smartadmin\assets\StarRatingAsset;
use demetrio77\smartadmin\assets\SpriteInputAsset;
use yii\web\View;
use demetrio77\smartadmin\assets\CkEditorAsset;
use yii\helpers\Url;
use demetrio77\smartadmin\assets\FileUploaderAsset;

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
    
    public function ckEditor ( $options = [])
    {
    	$view = Yii::$app->getView();    	
    	CkEditorAsset::register( $view );    	
    	$id = Html::getInputId($this->model, $this->attribute);
    	$view->registerJs("$('#".$id."').ckeditor({
			filebrowserBrowseUrl: '".Url::toRoute(['//manager/ckeditor'])."'
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
    	
    	if ($val!='') {
    		$found = preg_match_all('/\-?(\d+)\w*? \-?(\d+)/', $val, $matches);
    		if ($found && $matches && isset($matches[1][0],$matches[2][0])) {
    			$x = abs($matches[1][0]);
    			$y  = abs($matches[2][0]);
    		}
    	}
    	
    	$options['class'] = (isset($options['class'])?$options['class'].' ':'').'form-control';
    	$id = Html::getInputId($this->model, $this->attribute);
    	
    	$view->registerJs("$ ('#".$id."').spriteInput({
    		x: $x,
    		y: $y,
    		width: $width,
    		height: $height,
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
    
	public function fileInput( $options = [] )
    {
    	$view = Yii::$app->getView();
    	FileUploaderAsset::register( $view );
    	 
    	$fileUploaderOptions = [];
    	$returnPath = false;
    	
    	if (isset($options['tmpl'])) {
    		$fileUploaderOptions['tmpl'] = $options['tmpl'];
    		unset($options['tmpl']);
    	}
    	else {
    		$fileUploaderOptions['tmpl'] = 'upload,server,url,clear';
    	}
    	
    	if (isset($options['callback'])) {
    		$fileUploaderOptions['callback'] = $options['callback'];
    		unset($options['callback']);
    	}

    	if (isset($options['returnPath'])) {
    		$returnPath = $options['returnPath'];
    		unset($options['returnPath']);
    	}
    	 
    	$options = array_merge($this->inputOptions, $options);
    	$this->adjustLabelFor($options);
    	$name = Html::getInputName($this->model, $this->attribute);
    	$id = Html::getInputId($this->model, $this->attribute);
    	 
    	$js = "$(document).ready(function(){
    	       $('#".$id."').fileUploader({
                   value	 : '".$this->model->{$this->attribute}."',
                   ".(isset($fileUploaderOptions['tmpl'])?"tmpl:'".$fileUploaderOptions['tmpl']."',":'')."
                   ".(isset($fileUploaderOptions['callback'])?"callback:".$fileUploaderOptions['callback'].',':'')."
                   ".($returnPath ? "returnPath: true,":'')."
                   name      : '".$name."',
                   connector: '".Url::toRoute(['//manager/connector'])."'
               });
    	});";
    	$view->registerJs($js);
    	$this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute);
    	return $this;
    }
}