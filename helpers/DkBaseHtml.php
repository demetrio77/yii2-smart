<?php

namespace demetrio77\smartadmin\helpers;

use demetrio77\smartadmin\assets\CropImageUploaderAsset;
use demetrio77\smartadmin\assets\DateRangePickerAsset;
use Yii;
use demetrio77\smartadmin\assets\DateDropDownAsset;
use demetrio77\smartadmin\assets\DateTimePickerAsset;
use yii\helpers\BaseHtml;
use yii\web\View;
use demetrio77\smartadmin\helpers\typograph\Typograph;
use demetrio77\smartadmin\assets\ClockPickerAsset;
use yii\helpers\ArrayHelper;
use demetrio77\smartadmin\assets\FileUploaderAsset;
use yii\helpers\Url;

class DkBaseHtml extends BaseHtml
{
    public static function fileInput($name, $value = null, $options = [])
    {
        $defaults = [
            'returnPath' => false,
            'folder' => '',
            'alias' => '',
            'isImage' => false,
            'filename' => false,
            'tmpl' => 'upload,server,url,clear',
            'callback' => false
        ];

        $options = ArrayHelper::merge($defaults, $options);

        $options['id'] = $options['id'] ?? str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);

        $js = "$(document).ready(function(){
    	    $('#" . $options['id'] . "').fileUploader({
               value	 : '" . $value . "',
               tmpl:'" . $options['tmpl'] . "',
               " . ($options['callback'] ? "callback:" . $options['callback'] . ',' : '') . "
               " . ($options['returnPath'] ? "returnPath: true," : '') . "
               " . ($options['isImage'] ? "isImage: true," : '') . "
               " . ($options['filename'] ? "filename: '" . $options['filename'] . "'," : '') . "
               connector: '" . Url::toRoute(['//manager/connector']) . "',
               browse: '" . Url::toRoute(['//manager/browse']) . "',
    	       alias: '" . $options['alias'] . "',
    	       folder: '" . $options['folder'] . "'
    		});
    	});";

        foreach (array_keys($defaults) as $key) {
            if (isset($options[$key])) unset($options[$key]);
        }

        $view = Yii::$app->getView();
        FileUploaderAsset::register($view);
        $view->registerJs($js);

        return parent::hiddenInput($name, $value, $options);
    }

    public static function select2($name, $selection = null, $items = [], $options = [])
    {
        return static::dropDownList($name, $selection, $items, $options);
    }

    public static function activeSelect2($model, $attribute, $items = [], $options = [])
    {
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $kv => $vv) {
                    $items[$key][$kv] = Typograph::remove($vv);
                }
            } else {
                $items[$key] = Typograph::remove($value);
            }
        }

        return static::activeListInput('dropDownList', $model, $attribute, $items, $options);
    }

    public static function activeDateDropDown($model, $attribute, $options = [])
    {
        $opts = [];
        if (isset($options['minYear'])) {
            $opts[] = "minYear: " . $options['minYear'];
            unset($options['minYear']);
        }
        if (isset($options['maxYear'])) {
            $opts[] = "maxYear: " . $options['maxYear'];
            unset($options['maxYear']);
        }
        if (isset($options['defaultDate'])) {
            $opts[] = "defaultDate: '" . $options['defaultDate'] . "'";
            unset($options['defaultDate']);
        }
        $id = static::getInputId($model, $attribute) . 'Div';
        $view = Yii::$app->getView();
        DateDropDownAsset::register($view);
        $view->registerJs("$('#" . $id . "').dateDropDown(" . ($opts ? "{" . implode(',', $opts) . "}" : '') . ");");
        return '<div id="' . $id . '">' . static::activeHiddenInput($model, $attribute, $options) . '</div>';
    }

    public static function activeDateTimeInput($model, $attribute, $options = [])
    {
        $view = Yii::$app->getView();
        $id = self::getInputId($model, $attribute);
        $val = $model->{$attribute} ?? '';
        if (is_numeric($val)) {
            if ($val > 0) {
                $model->{$attribute} = date('d.m.Y H:i', $val);
            } else {
                $model->{$attribute} = '';
            }
        }

        if (isset($options['layout']) && $options['layout'] == '2-widgets') {
            $s = self::activeHiddenInput($model, $attribute);
            $name = self::getInputName($model, $attribute);

            $dateId = $id . '_date';
            $timeId = $id . '_time';

            if ($val > 0) {
                $date = date('d.m.Y', $val);
                $time = date('H:i', $val);
            } else {
                $date = '';
                $time = '';
            }

            $s .= '<div class="input-group">' .
                self::textInput($dateId, $date, ['class' => 'form-control datepicker', 'id' => $dateId, 'data-dateformat' => 'dd.mm.yy']) .
                '<span title="Сейчас" class="input-group-addon"><i class="fa fa-calendar"></i></span>' .
                self::textInput($timeId, $time, ['class' => 'form-control', 'id' => $timeId]) .
                '<span title="Сейчас" class="input-group-addon cursor-pointer"><i class="glyphicon glyphicon-time"></i></span>' .
                '</div>';

            ClockPickerAsset::register($view);

            $view->registerJs("
	            $('#" . $timeId . "').clockpicker({
					autoclose: true,
					placement: 'top'
				});
					
				$('#" . $dateId . "').datepicker({
					dateFormat: 'dd.mm.yy'
				});
					
				$('#" . $timeId . "').change( function(){
					var time = $(this).val();
					var date = $('#" . $dateId . "').val();
					$('#$id').val(date + ' ' + time);
				});
					
				$('#" . $dateId . "').change( function(){
					var date = $(this).val();
					var time = $('#" . $timeId . "').val();
					$('#$id').val(date + ' ' + time);
				});
					
				$('.cursor-pointer', $('#" . $id . "').parent().parent()).click( function(){
					var d = new Date,
						day = d.getDate()<10 ? '0' + d.getDate().toString(): d.getDate(),
						mon = d.getMonth() < 9 ? '0' + (d.getMonth()+1).toString() : d.getMonth()+1,
						Y = d.getFullYear(),
						H = d.getHours() < 10 ? '0' + d.getHours().toString(): d.getHours(),
						min = d.getMinutes()< 10 ? '0' + d.getMinutes().toString() : d.getMinutes(),
						date = day + '.' + mon + '.' + Y,
						time = H + ':' + min,
	    				val = date+' '+time;
										
					$('#" . $id . "').val(val);
					$('#" . $timeId . "').val(time);
					$('#" . $dateId . "').val(date);
				});
				",
                View::POS_READY
            );

            return $s;
        }

        DateTimePickerAsset::register($view);

        $view->registerJs("
            $('#" . $id . "').datetimepicker({
               locale: 'ru'
            });
			$('.cursor-pointer', $('#" . $id . "').parent().parent()).click( function(){
				var d = new Date,
    			dformat = [ d.getDate(),
							d.getMonth()+1,
               				d.getFullYear()].join('.')+' '+
              			  [ d.getHours(),
               				d.getMinutes(),
               			  ].join(':');
				
				$('#" . $id . "').data(\"DateTimePicker\").date(dformat);
			});
    	", View::POS_READY);

        $options['class'] = 'form-control' . (isset($options['class']) ? ' ' . $options['class'] : '');

        return '<div class="input-group">' . self::activeTextInput($model, $attribute, $options) . '<span title="Сейчас" class="input-group-addon cursor-pointer"><i class="glyphicon glyphicon-time"></i></span></div>';
    }

    public static function dateInput($name, $value, $options = [])
    {
        if (!isset($options['data-dateformat'])) {
            $options['data-dateformat'] = "yy-mm-dd";
        }
        $options['class'] = 'form-control datepicker' . (isset($options['class']) ? ' ' . $options['class'] : '');

        return '<div class="input-group">' . self::textInput($name, $value, $options) . '<span class="input-group-addon cursor-pointer"><i class="fa fa-calendar"></i></span></div>';
    }

    public static function activeDateInput($model, $attribute, $options = [])
    {
        if (!isset($options['data-dateformat'])) {
            $options['data-dateformat'] = "yy-mm-dd";
        }
        $options['class'] = 'form-control datepicker' . (isset($options['class']) ? ' ' . $options['class'] : '');

        return '<div class="input-group">' . self::activeTextInput($model, $attribute, $options) . '<span title="Cегодня" class="input-group-addon cursor-pointer"><i class="fa fa-calendar"></i></span></div>';
    }

    public static function dateDropDown($name, $value = null, $options = [])
    {
        $opts = [];
        if (isset($options['minYear'])) {
            $opts[] = "minYear: " . $options['minYear'];
            unset($options['minYear']);
        }
        if (isset($options['maxYear'])) {
            $opts[] = "maxYear: " . $options['maxYear'];
            unset($options['maxYear']);
        }
        if (isset($options['defaultDate'])) {
            $opts[] = "defaultDate: '" . $options['defaultDate'] . "'";
            unset($options['defaultDate']);
        }
        $name = strtolower($name);
        $id = str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name) . 'Div';
        $view = Yii::$app->getView();
        DateDropDownAsset::register($view);
        $view->registerJs("$('#" . $id . "').dateDropDown(" . ($opts ? "{" . implode(',', $opts) . "}" : '') . ");");
        return '<div id="' . $id . '">' . static::hiddenInput($name, $value, $options) . '</div>';
    }

    public static function cropImageInput($name, $value = null, $options = [])
    {
        $defaults = [
            'returnPath' => false,
            'folder' => '',
            'alias' => '',
            'tempAlias' => '',
            'filename' => false,
            'uploadTmpl' => 'upload,url',
            'callback' => false,
            'cropWidth' => 100,
            'cropHeight' => 100,
            'callback' => 'function(){}',
            'template' => '<div class="row" id="{id}"><div class="col-xs-12 col-md-4">{image}</div><div class="col-xs-12 col-md-8"><div>{input}</div></div></div>'
        ];

        $options = ArrayHelper::merge($defaults, $options);
        $options['id'] = $options['id'] ?? str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);

        $js = "$(document).ready(function(){
    	    $('#" . $options['id'] . "').cropImageUploader({
               value	 : '" . $value . "',
               uploadTmpl:'" . $options['uploadTmpl'] . "',
               " . ($options['callback'] ? "callback:" . $options['callback'] . ',' : '') . "
               " . ($options['returnPath'] ? "returnPath: true," : '') . "
               " . ($options['filename'] ? "filename: '" . $options['filename'] . "'," : '') . "
               connector: '" . Url::toRoute(['//manager/connector']) . "',
    	       alias: '" . $options['alias'] . "',
    	       tempAlias: '".$options['tempAlias']."',
    	       folder: '" . $options['folder'] . "',
    	       cropWidth: ".$options['cropWidth'].",
    	       cropHeight: ".$options['cropHeight'].",
    	       callback: ".$options['callback'].",
    	       template: '".$options['template']."'
    		});
    	});";

        foreach (array_keys($defaults) as $key) {
            if (isset($options[$key])) unset($options[$key]);
        }

        $view = Yii::$app->getView();
        $view->registerJs($js);
        CropImageUploaderAsset::register($view);

        return parent::hiddenInput($name, $value, $options);
    }

    public static function dateRangePicker($name1, $name2, $value1=null, $value2=null, $options = [])
    {
        $View = \Yii::$app->getView();
        DateRangePickerAsset::register($View);

        if (!isset($options['inputOptions']['id'])) {
            $id = uniqid('datepicker-');
            $options['inputOptions']['id'] = $id;
        }
        else {
            $id = $options['inputOptions']['id'];
        }

        $opens = $options['opens'] ?? 'left';

        $value = ($value1 && $value2) ? "$value1 - $value2" : '';

        $View->registerJs("
            $('#".$id."').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                alwaysShowCalendars: true,
                opens: '$opens',
                ranges: ".(($options['ranges']) ?? "{
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }")."
            });

            $('#".$id."').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                $('input[name=\"".$name1."\"]').val(picker.startDate.format('MM/DD/YYYY'));
                $('input[name=\"".$name2."\"]').val(picker.endDate.format('MM/DD/YYYY'));
                $('input[name=\"".$name1."\"]').change();
                $('input[name=\"".$name2."\"]').change();
                return true;
            });
        
            $('#".$id."').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('input[name=\"".$name1."\"]').val('');
                $('input[name=\"".$name2."\"]').val('');
                $('input[name=\"".$name1."\"]').change();
                $('input[name=\"".$name2."\"]').change();
            });
        ");

        return self::textInput(null, $value, $options['inputOptions'] ?? []).
            self::hiddenInput($name1, $value1, $options['input1Options'] ?? []).
            self::hiddenInput($name2, $value2, $options['input2Options'] ?? []);
    }
}
