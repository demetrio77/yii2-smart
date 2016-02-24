<?php

namespace demetrio77\smartadmin\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;

class LetterPager extends Widget
{
	const RU_LETTERS = 1;
	const EN_LETTERS = 2;
	const RU_EN_LETTERS = 3;
	
	private static $sets = [
		self::RU_LETTERS => ['А','Б', 'В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я'],
		self::EN_LETTERS => ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
		self::RU_EN_LETTERS => true
	];
	
	public $template = '';
	
	//массив со значениями либо 
	public $items    = '';
	public $active   = '';
	public $allowEmpty = true;
	
	public function init()
	{
		parent::init();
		
		if (in_array($this->items, array_keys(self::$sets))) {
			if ($this->items == self::RU_EN_LETTERS) {
				$this->items = array_merge(self::$sets[self::RU_LETTERS], self::$sets[self::EN_LETTERS]);
			}
			else {
				$this->items = self::$sets[$this->items];
			}
		}
		
		if (! ArrayHelper::isAssociative($this->items)) {
			$v = $this->items;
			$this->items = [];
			foreach ($v as $val) {
				$this->items[$val] = $val;
			}
		}
		
		if ($this->allowEmpty) {
			$this->items = ArrayHelper::merge([''=>'-'], $this->items);
		}
	}
	
	public function run()
	{
		$s = [];
		$s[] = '<ul class="pagination">';

		foreach ($this->items as $key => $value) {
			$s [] = '<li'.($key===$this->active ?' class="active"':''). '><a href="'.str_replace(['{key}','{value}'], [$key, $value], $this->template).'" data-page="0">'.$value.'</a></li>';
		}
		$s[] = '</ul>';
		return implode('', $s);
	}
}