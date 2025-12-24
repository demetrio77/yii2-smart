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

    public function dateDropDown( $options = [])
    {
    	$this->parts['{input}'] = Html::activeDateDropDown($this->model, $this->attribute, $options);
    	return $this;
    }

    public function dateInput( $options = [])
    {
    	parent::dateInput($options);
    	$this->parts['{input}'] = '<div class="input">'.$this->parts['{input}'].'</div>';
    	return $this;
    }

    public function clockInput( $options = [])
    {
    	parent::clockInput($options);
    	$this->parts['{input}'] = '<div class="input">'.$this->parts['{input}'].'</div>';
    	return $this;
    }

    public function dateTimeInput( $options = [])
    {
    	parent::dateTimeInput($options);
    	$this->parts['{input}'] = '<div class="input">'.$this->parts['{input}'].'</div>';
    	return $this;
    }

    public function numberInput($options=[])
    {
     	parent::numberInput($options);
        $this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input']);
    	return $this;
    }

    public function secureInput($options = [], $configId = 0): ActiveField
    {
        $hasAccess = $options['hasAccess'] ?? true;
        unset($options['hasAccess']);

        $this->parts['{input}'] = Html::tag('div', Html::activeSecureInput($this->model, $this->attribute, $options, $configId, $hasAccess), ['class' => 'input']);

        return $this;
    }
}
